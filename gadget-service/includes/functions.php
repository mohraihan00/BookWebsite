<?php
/**
 * Gadget Service Management System - Utility Functions
 *
 * This file contains utility functions for common operations
 * including validation, formatting, and helper functions.
 */

// Prevent direct access
if (!defined('GADGET_SERVICE')) {
    exit('Direct access to this file is not allowed.');
}

require_once __DIR__ . '/../config/constants.php';

// ========================================
// VALIDATION FUNCTIONS
// ========================================

/**
 * Validate email address
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match(REGEX_EMAIL, $email);
}

/**
 * Validate phone number
 */
function validatePhone($phone) {
    return preg_match(REGEX_PHONE, $phone);
}

/**
 * Validate ticket number format
 */
function validateTicketNumber($ticketNo) {
    return preg_match(REGEX_TICKET_NO, $ticketNo);
}

/**
 * Validate customer code format
 */
function validateCustomerCode($customerCode) {
    return preg_match(REGEX_CUSTOMER_CODE, $customerCode);
}

/**
 * Validate payment number format
 */
function validatePaymentNumber($paymentNo) {
    return preg_match(REGEX_PAYMENT_NO, $paymentNo);
}

/**
 * Validate part SKU format
 */
function validatePartSKU($sku) {
    return preg_match(REGEX_PART_SKU, $sku);
}

/**
 * Validate serial number
 */
function validateSerialNumber($serial) {
    return preg_match(REGEX_SERIAL_NUMBER, $serial);
}

/**
 * Validate IMEI number
 */
function validateIMEI($imei) {
    return preg_match(REGEX_IMEI, $imei);
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate required fields
 */
function validateRequired($data, $requiredFields) {
    $errors = [];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $errors[] = ucwords(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    return $errors;
}

/**
 * Clean and validate numeric input
 */
function validateNumber($value, $decimal = false) {
    if ($decimal) {
        return is_numeric($value) ? floatval($value) : 0;
    }
    return is_numeric($value) ? intval($value) : 0;
}

// ========================================
// FORMATTING FUNCTIONS
// ========================================

/**
 * Format currency amount
 */
function formatCurrency($amount, $symbol = CURRENCY_SYMBOL) {
    return $symbol . number_format($amount, CURRENCY_DECIMAL_PLACES, ',', '.');
}

/**
 * Format date
 */
function formatDate($date, $format = 'd M Y') {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '-';
    }
    return date($format, strtotime($date));
}

/**
 * Format date time
 */
function formatDateTime($datetime, $format = 'd M Y H:i') {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return '-';
    }
    return date($format, strtotime($datetime));
}

/**
 * Format time difference (human readable)
 */
function formatTimeDiff($datetime) {
    if (empty($datetime)) {
        return '-';
    }

    $diff = time() - strtotime($datetime);

    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' minutes ago';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' hours ago';
    } elseif ($diff < 2592000) {
        return floor($diff / 86400) . ' days ago';
    } else {
        return formatDate($datetime);
    }
}

/**
 * Format phone number for display
 */
function formatPhone($phone) {
    // Remove all non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // Format Indonesian phone numbers
    if (strlen($phone) === 12 && substr($phone, 0, 2) === '62') {
        return '+' . substr($phone, 0, 2) . '-' . substr($phone, 2, 3) . '-' . substr($phone, 5, 4) . '-' . substr($phone, 9);
    } elseif (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
        return '+62-' . substr($phone, 1, 3) . '-' . substr($phone, 4, 4) . '-' . substr($phone, 8);
    }

    return $phone;
}

/**
 * Truncate text
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Convert bytes to human readable format
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Generate slug from text
 */
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    return $text ?: 'untitled';
}

// ========================================
// URL AND PATH FUNCTIONS
// ========================================

/**
 * Get base URL
 */
function baseUrl($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Get assets URL
 */
function assetsUrl($path = '') {
    return ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * Get uploads URL
 */
function uploadsUrl($path = '') {
    return UPLOADS_URL . '/' . ltrim($path, '/');
}

/**
 * Current URL
 */
function currentUrl() {
    return BASE_URL . $_SERVER['REQUEST_URI'];
}

/**
 * Redirect to URL
 */
function redirect($url, $statusCode = 302) {
    if (!headers_sent()) {
        header("Location: " . baseUrl($url), true, $statusCode);
        exit;
    } else {
        echo "<script>window.location.href='" . baseUrl($url) . "';</script>";
        exit;
    }
}

/**
 * Redirect back to previous page
 */
function redirectBack() {
    $referer = $_SERVER['HTTP_REFERER'] ?? baseUrl('/pages/auth/dashboard.php');
    redirect($referer);
}

// ========================================
// SESSION AND MESSAGE FUNCTIONS
// ========================================

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_messages'][$type] = $message;
}

/**
 * Get flash messages
 */
function getFlashMessages() {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

/**
 * Display flash messages
 */
function displayFlashMessages() {
    $messages = getFlashMessages();
    if (empty($messages)) {
        return;
    }

    foreach ($messages as $type => $message) {
        $alertClass = match ($type) {
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info',
            default => 'alert-primary'
        };
        echo "<div class='alert {$alertClass} alert-dismissible fade show' role='alert'>";
        echo htmlspecialchars($message);
        echo "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>";
        echo "</div>";
    }
}

/**
 * Set error message
 */
function setError($message) {
    setFlashMessage('error', $message);
}

/**
 * Set success message
 */
function setSuccess($message) {
    setFlashMessage('success', $message);
}

/**
 * Set warning message
 */
function setWarning($message) {
    setFlashMessage('warning', $message);
}

/**
 * Set info message
 */
function setInfo($message) {
    setFlashMessage('info', $message);
}

// ========================================
// ARRAY AND DATA FUNCTIONS
// ========================================

/**
 * Get value from array with default
 */
function arrayGet($array, $key, $default = null) {
    return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * Convert array to select options
 */
function arrayToOptions($array, $selected = '', $default = '') {
    $options = '';
    if ($default !== '') {
        $selectedAttr = $selected === '' ? 'selected' : '';
        $options .= "<option value='' {$selectedAttr}>{$default}</option>";
    }

    foreach ($array as $value => $text) {
        $selectedAttr = $value === $selected ? 'selected' : '';
        $options .= "<option value='{$value}' {$selectedAttr}>" . htmlspecialchars($text) . "</option>";
    }

    return $options;
}

/**
 * Build pagination links
 */
function buildPagination($currentPage, $totalPages, $baseUrl, $maxVisible = 5) {
    if ($totalPages <= 1) {
        return '';
    }

    $html = '<nav><ul class="pagination">';

    // Previous link
    $prevPage = $currentPage - 1;
    if ($prevPage >= 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $prevPage . '">Previous</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }

    // Page range
    $start = max(1, $currentPage - floor($maxVisible / 2));
    $end = min($totalPages, $start + $maxVisible - 1);
    $start = max(1, $end - $maxVisible + 1);

    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=1">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        $activeClass = $i === $currentPage ? 'active' : '';
        $html .= '<li class="page-item ' . $activeClass . '"><a class="page-link" href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a></li>';
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }

    // Next link
    $nextPage = $currentPage + 1;
    if ($nextPage <= $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $nextPage . '">Next</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }

    $html .= '</ul></nav>';
    return $html;
}

// ========================================
// FILE FUNCTIONS
// ========================================

/**
 * Generate unique filename
 */
function generateUniqueFilename($originalName, $prefix = '') {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $filename = ($prefix ? $prefix . '_' : '') . uniqid() . '_' . time();
    return $filename . '.' . $extension;
}

/**
 * Check if file is allowed image type
 */
function isAllowedImage($fileType) {
    return in_array($fileType, ALLOWED_IMAGE_TYPES);
}

/**
 * Check if file is allowed document type
 */
function isAllowedDocument($fileType) {
    return in_array($fileType, ALLOWED_DOCUMENT_TYPES);
}

/**
 * Resize image
 */
function resizeImage($sourceFile, $destFile, $maxWidth, $maxHeight, $quality = IMAGE_QUALITY) {
    // Get image info
    $imageInfo = getimagesize($sourceFile);
    if (!$imageInfo) {
        return false;
    }

    list($originalWidth, $originalHeight, $imageType) = $imageInfo;

    // Calculate new dimensions
    $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
    $newWidth = intval($originalWidth * $ratio);
    $newHeight = intval($originalHeight * $ratio);

    // Create image resource based on type
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($sourceFile);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($sourceFile);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($sourceFile);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($sourceFile);
            break;
        default:
            return false;
    }

    // Create new image
    $destination = imagecreatetruecolor($newWidth, $newHeight);

    // Handle transparency for PNG and GIF
    if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
        imagealphablending($destination, false);
        imagesavealpha($destination, true);
        $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
        imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $transparent);
    }

    // Resize image
    imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

    // Save image
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            imagejpeg($destination, $destFile, $quality);
            break;
        case IMAGETYPE_PNG:
            imagepng($destination, $destFile, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($destination, $destFile);
            break;
        case IMAGETYPE_WEBP:
            imagewebp($destination, $destFile, $quality);
            break;
    }

    // Free memory
    imagedestroy($source);
    imagedestroy($destination);

    return true;
}

/**
 * Create directory if it doesn't exist
 */
function ensureDirectoryExists($path) {
    if (!is_dir($path)) {
        return mkdir($path, 0755, true);
    }
    return true;
}

// ========================================
// SECURITY FUNCTIONS
// ========================================

/**
 * Generate CSRF token field
 */
function csrfField() {
    $token = Auth::generateCSRFToken();
    return "<input type='hidden' name='" . CSRF_TOKEN_NAME . "' value='{$token}'>";
}

/**
 * Validate CSRF token
 */
function validateCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!Auth::validateCSRFToken($token)) {
            setError('Invalid request. Please try again.');
            redirectBack();
            exit;
        }
    }
}

/**
 * Escape HTML output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

// ========================================
// DEBUG FUNCTIONS
// ========================================

/**
 * Debug variable
 */
function debug($var, $die = false) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if ($die) {
        die();
    }
}

/**
 * Log message to file
 */
function logMessage($message, $level = 'INFO') {
    if (!LOG_ENABLED) {
        return;
    }

    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;

    error_log($logEntry, 3, LOG_FILE_PATH);
}

/**
 * Get client IP address
 */
function getClientIP() {
    $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];

    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            $ips = explode(',', $_SERVER[$key]);
            $ip = trim($ips[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
}

/**
 * Check if request is AJAX
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get current page
 */
function getCurrentPage() {
    $uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($uri, PHP_URL_PATH);
    return basename($path, '.php');
}

?>