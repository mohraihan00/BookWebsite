<?php
/**
 * Gadget Service Management System - Authentication
 *
 * This file handles user authentication, session management,
 * and role-based access control.
 */

// Prevent direct access
if (!defined('GADGET_SERVICE')) {
    exit('Direct access to this file is not allowed.');
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

class Auth {
    private $db;
    private static $instance = null;
    private $user = null;

    /**
     * Private constructor for singleton pattern
     */
    private function __construct() {
        $this->db = Database::getInstance();
        $this->initSession();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize secure session
     */
    private function initSession() {
        // Set session name
        session_name(SESSION_NAME);

        // Set session parameters
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Strict');

        // Set session cookie parameters
        $cookieParams = [
            'lifetime' => SESSION_LIFETIME,
            'path' => SESSION_PATH,
            'domain' => SESSION_DOMAIN,
            'secure' => SESSION_SECURE,
            'httponly' => SESSION_HTTPONLY,
            'samesite' => 'Strict'
        ];

        session_set_cookie_params($cookieParams);

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Regenerate session ID for security
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }

        // Load current user if logged in
        if ($this->isLoggedIn()) {
            $this->loadCurrentUser();
        }
    }

    /**
     * Login user with username and password
     */
    public function login($username, $password, $remember = false) {
        // Validate input
        if (empty($username) || empty($password)) {
            throw new Exception($GLOBALS['ERROR_MESSAGES']['required_field_missing']);
        }

        // Check if account is locked due to too many attempts
        if ($this->isAccountLocked($username)) {
            throw new Exception($GLOBALS['ERROR_MESSAGES']['account_locked']);
        }

        try {
            // Get user by username
            $user = $this->db->get(
                "SELECT u.*, r.role_name
                 FROM users u
                 JOIN roles r ON u.role_id = r.id
                 WHERE u.username = ? AND u.is_active = 1",
                [$username]
            );

            if (!$user) {
                $this->recordFailedAttempt($username);
                throw new Exception($GLOBALS['ERROR_MESSAGES']['login_failed']);
            }

            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                $this->recordFailedAttempt($username);
                throw new Exception($GLOBALS['ERROR_MESSAGES']['login_failed']);
            }

            // Check if password needs rehash (new algorithm available)
            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $this->db->update('users',
                    ['password_hash' => $newHash],
                    'id = ?',
                    [$user['id']]
                );
            }

            // Clear failed attempts
            $this->clearFailedAttempts($username);

            // Set session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['role_name'] = $user['role_name'];
            $_SESSION['login_time'] = time();
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

            // Update last login in database
            $this->db->update('users',
                ['last_login' => date('Y-m-d H:i:s')],
                'id = ?',
                [$user['id']]
            );

            // Set remember me cookie if requested
            if ($remember) {
                $this->setRememberMeToken($user['id']);
            }

            // Load current user data
            $this->user = $user;

            return true;

        } catch (Exception $e) {
            error_log("Login failed for username '$username': " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Logout user and destroy session
     */
    public function logout() {
        // Clear remember me token if exists
        if (isset($_COOKIE['remember_token'])) {
            $this->clearRememberMeToken($_COOKIE['remember_token']);
            setcookie('remember_token', '', time() - 3600, '/');
        }

        // Unset all session variables
        $_SESSION = [];

        // Destroy session
        session_destroy();

        // Clear current user
        $this->user = null;
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Check if current user has specific role
     */
    public function hasRole($roleName) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        return $_SESSION['role_name'] === $roleName;
    }

    /**
     * Check if current user has permission (can be extended)
     */
    public function hasPermission($permission) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        // Admin has all permissions
        if ($this->hasRole('admin')) {
            return true;
        }

        // Define role permissions
        $permissions = [
            'admin' => ['*'], // All permissions
            'cs' => [
                'customer.create', 'customer.view', 'customer.update',
                'ticket.create', 'ticket.view', 'ticket.update',
                'estimate.view', 'report.view'
            ],
            'teknisi' => [
                'ticket.view', 'ticket.update',
                'diagnostic.create', 'diagnostic.update', 'diagnostic.view',
                'estimate.create', 'estimate.update', 'estimate.view',
                'repair.create', 'repair.update', 'repair.view',
                'parts.view', 'parts.use',
                'report.view'
            ],
            'pelanggan' => [
                'ticket.create', 'ticket.view.own',
                'estimate.view.own', 'estimate.approve.own',
                'warranty.view.own', 'warranty.claim.own'
            ]
        ];

        $userRole = $_SESSION['role_name'];

        if (!isset($permissions[$userRole])) {
            return false;
        }

        // Check if role has all permissions
        if (in_array('*', $permissions[$userRole])) {
            return true;
        }

        // Check specific permission
        return in_array($permission, $permissions[$userRole]);
    }

    /**
     * Require login - redirect if not logged in
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->redirect('/pages/auth/login.php');
            exit;
        }
    }

    /**
     * Require specific role - redirect if not authorized
     */
    public function requireRole($roleName) {
        $this->requireLogin();

        if (!$this->hasRole($roleName)) {
            $_SESSION['error_message'] = $GLOBALS['ERROR_MESSAGES']['access_denied'];
            $this->redirect('/pages/auth/dashboard.php');
            exit;
        }
    }

    /**
     * Require specific permission - redirect if not authorized
     */
    public function requirePermission($permission) {
        $this->requireLogin();

        if (!$this->hasPermission($permission)) {
            $_SESSION['error_message'] = $GLOBALS['ERROR_MESSAGES']['access_denied'];
            $this->redirect('/pages/auth/dashboard.php');
            exit;
        }
    }

    /**
     * Get current user data
     */
    public function getCurrentUser() {
        return $this->user;
    }

    /**
     * Get current user ID
     */
    public function getCurrentUserId() {
        return $this->isLoggedIn() ? $_SESSION['user_id'] : null;
    }

    /**
     * Get current user role
     */
    public function getCurrentUserRole() {
        return $this->isLoggedIn() ? $_SESSION['role_name'] : null;
    }

    /**
     * Load current user data from database
     */
    private function loadCurrentUser() {
        if (!$this->isLoggedIn()) {
            return;
        }

        $this->user = $this->db->get(
            "SELECT u.*, r.role_name
             FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE u.id = ? AND u.is_active = 1",
            [$_SESSION['user_id']]
        );

        // If user not found or inactive, logout
        if (!$this->user) {
            $this->logout();
            return;
        }

        // Validate session
        if (!$this->validateSession()) {
            $this->logout();
            $_SESSION['error_message'] = $GLOBALS['ERROR_MESSAGES']['session_expired'];
            return;
        }
    }

    /**
     * Validate session for security
     */
    private function validateSession() {
        // Check session timeout
        if (isset($_SESSION['login_time'])) {
            $sessionAge = time() - $_SESSION['login_time'];
            if ($sessionAge > SESSION_TIMEOUT) {
                return false;
            }
        }

        // Check IP address (optional, can be disabled for dynamic IPs)
        if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
            // Log potential session hijacking attempt
            error_log("Session IP mismatch for user {$_SESSION['user_id']}: {$_SESSION['ip_address']} vs {$_SERVER['REMOTE_ADDR']}");
            return false;
        }

        // Check user agent (optional)
        if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            error_log("Session User-Agent mismatch for user {$_SESSION['user_id']}");
            return false;
        }

        return true;
    }

    /**
     * Record failed login attempt
     */
    private function recordFailedAttempt($username) {
        $ip = $_SERVER['REMOTE_ADDR'];

        // Create failed_attempts table if not exists
        $this->db->query("
            CREATE TABLE IF NOT EXISTS failed_login_attempts (
                id INT PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(50) NOT NULL,
                ip_address VARCHAR(45) NOT NULL,
                attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_username_ip (username, ip_address),
                INDEX idx_attempt_time (attempt_time)
            )
        ");

        // Record failed attempt
        $this->db->insert('failed_login_attempts', [
            'username' => $username,
            'ip_address' => $ip
        ]);
    }

    /**
     * Check if account is locked due to failed attempts
     */
    private function isAccountLocked($username) {
        $ip = $_SERVER['REMOTE_ADDR'];

        $recentAttempts = $this->db->getValue(
            "SELECT COUNT(*) FROM failed_login_attempts
             WHERE username = ? AND ip_address = ?
             AND attempt_time > DATE_SUB(NOW(), INTERVAL ? SECOND)",
            [$username, $ip, LOGIN_LOCKOUT_DURATION]
        );

        return $recentAttempts >= MAX_LOGIN_ATTEMPTS;
    }

    /**
     * Clear failed login attempts
     */
    private function clearFailedAttempts($username) {
        $ip = $_SERVER['REMOTE_ADDR'];

        $this->db->query(
            "DELETE FROM failed_login_attempts
             WHERE username = ? AND ip_address = ?",
            [$username, $ip]
        );
    }

    /**
     * Set remember me token
     */
    private function setRememberMeToken($userId) {
        $token = bin2hex(random_bytes(32));
        $selector = bin2hex(random_bytes(8));
        $hash = hash('sha256', $token);
        $expiry = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 days

        // Store in database
        $this->db->query("
            CREATE TABLE IF NOT EXISTS auth_tokens (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                selector VARCHAR(8) NOT NULL UNIQUE,
                token_hash VARCHAR(64) NOT NULL,
                expiry DATETIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_selector (selector),
                INDEX idx_user_id (user_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");

        $this->db->insert('auth_tokens', [
            'user_id' => $userId,
            'selector' => $selector,
            'token_hash' => $hash,
            'expiry' => $expiry
        ]);

        // Set cookie
        $cookieValue = $selector . ':' . $token;
        setcookie('remember_token', $cookieValue, time() + (30 * 24 * 60 * 60), '/', '', SESSION_SECURE, true);
    }

    /**
     * Clear remember me token
     */
    private function clearRememberMeToken($cookieValue) {
        $parts = explode(':', $cookieValue);
        if (count($parts) === 2) {
            $selector = $parts[0];
            $this->db->query("DELETE FROM auth_tokens WHERE selector = ?", [$selector]);
        }
    }

    /**
     * Auto-login from remember me cookie
     */
    public function autoLogin() {
        if (isset($_COOKIE['remember_token']) && !$this->isLoggedIn()) {
            $parts = explode(':', $_COOKIE['remember_token']);

            if (count($parts) === 2) {
                $selector = $parts[0];
                $token = $parts[1];

                $tokenRecord = $this->db->get(
                    "SELECT t.*, u.username, u.password_hash, r.role_name
                     FROM auth_tokens t
                     JOIN users u ON t.user_id = u.id
                     JOIN roles r ON u.role_id = r.id
                     WHERE t.selector = ? AND t.expiry > NOW() AND u.is_active = 1",
                    [$selector]
                );

                if ($tokenRecord && hash_equals($tokenRecord['token_hash'], hash('sha256', $token))) {
                    // Set session data
                    $_SESSION['user_id'] = $tokenRecord['user_id'];
                    $_SESSION['username'] = $tokenRecord['username'];
                    $_SESSION['role_id'] = $tokenRecord['role_id'];
                    $_SESSION['role_name'] = $tokenRecord['role_name'];
                    $_SESSION['login_time'] = time();
                    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                    // Update last login
                    $this->db->update('users',
                        ['last_login' => date('Y-m-d H:i:s')],
                        'id = ?',
                        [$tokenRecord['user_id']]
                    );

                    // Remove used token (one-time use)
                    $this->db->query("DELETE FROM auth_tokens WHERE selector = ?", [$selector]);

                    // Set new remember me token
                    $this->setRememberMeToken($tokenRecord['user_id']);

                    // Load current user
                    $this->loadCurrentUser();

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Redirect to URL
     */
    private function redirect($url) {
        header("Location: " . BASE_URL . $url);
        exit;
    }

    /**
     * Hash password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Validate password strength
     */
    public static function validatePassword($password) {
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            return "Password must be at least " . PASSWORD_MIN_LENGTH . " characters long.";
        }

        if (PASSWORD_REQUIRE_UPPERCASE && !preg_match('/[A-Z]/', $password)) {
            return "Password must contain at least one uppercase letter.";
        }

        if (PASSWORD_REQUIRE_LOWERCASE && !preg_match('/[a-z]/', $password)) {
            return "Password must contain at least one lowercase letter.";
        }

        if (PASSWORD_REQUIRE_NUMBER && !preg_match('/[0-9]/', $password)) {
            return "Password must contain at least one number.";
        }

        if (PASSWORD_REQUIRE_SPECIAL && !preg_match('/[^A-Za-z0-9]/', $password)) {
            return "Password must contain at least one special character.";
        }

        return true;
    }

    /**
     * Generate secure random token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = self::generateToken();
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /**
     * Validate CSRF token
     */
    public static function validateCSRFToken($token) {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }
        return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }

    /**
     * Clear CSRF token
     */
    public static function clearCSRFToken() {
        unset($_SESSION[CSRF_TOKEN_NAME]);
    }
}

?>