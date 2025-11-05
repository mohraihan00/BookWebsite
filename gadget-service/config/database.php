<?php
/**
 * Gadget Service Management System - Database Connection
 *
 * This file handles database connection and provides methods
 * for database operations with PDO.
 */

// Prevent direct access
if (!defined('GADGET_SERVICE')) {
    exit('Direct access to this file is not allowed.');
}

require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $connection;
    private $statement;
    private $query_count = 0;
    private $query_log = [];

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->connect();
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
     * Establish database connection
     */
    private function connect() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, DB_OPTIONS);

            // Set error mode
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Emulate prepares disabled for security
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            // Set fetch mode to associative array
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Set SQL mode
            $this->connection->exec("SET sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");

            if (DEBUG_MODE && SAVE_QUERIES) {
                $this->connection->setAttribute(PDO::ATTR_STATEMENT_CLASS, ['LoggedPDOStatement', [$this]]);
            }

        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    /**
     * Prepare and execute a query
     */
    public function query($sql, $params = []) {
        try {
            $start_time = microtime(true);

            $this->statement = $this->connection->prepare($sql);
            $this->statement->execute($params);

            $this->query_count++;

            if (DEBUG_MODE && SAVE_QUERIES) {
                $execution_time = microtime(true) - $start_time;
                $this->logQuery($sql, $params, $execution_time);
            }

            return $this->statement;

        } catch (PDOException $e) {
            $this->handleError($e, $sql, $params);
        }
    }

    /**
     * Get single row
     */
    public function get($sql, $params = []) {
        $this->query($sql, $params);
        return $this->statement->fetch();
    }

    /**
     * Get multiple rows
     */
    public function getAll($sql, $params = []) {
        $this->query($sql, $params);
        return $this->statement->fetchAll();
    }

    /**
     * Get single value
     */
    public function getValue($sql, $params = []) {
        $this->query($sql, $params);
        return $this->statement->fetchColumn();
    }

    /**
     * Insert record and return last insert ID
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $this->query($sql, $data);

        return $this->connection->lastInsertId();
    }

    /**
     * Update record
     */
    public function update($table, $data, $where, $whereParams = []) {
        $setClause = [];
        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
        }

        $setClause = implode(', ', $setClause);
        $sql = "UPDATE $table SET $setClause WHERE $where";

        $params = array_merge($data, $whereParams);
        $this->query($sql, $params);

        return $this->statement->rowCount();
    }

    /**
     * Delete record
     */
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        $this->query($sql, $params);

        return $this->statement->rowCount();
    }

    /**
     * Check if record exists
     */
    public function exists($table, $where, $params = []) {
        $sql = "SELECT COUNT(*) as count FROM $table WHERE $where LIMIT 1";
        $result = $this->get($sql, $params);

        return $result['count'] > 0;
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->connection->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->connection->rollback();
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    /**
     * Get affected rows count
     */
    public function rowCount() {
        return $this->statement ? $this->statement->rowCount() : 0;
    }

    /**
     * Call stored procedure
     */
    public function callProcedure($procedureName, $params = []) {
        $placeholders = [];
        foreach ($params as $key => $value) {
            $placeholders[] = ":$key";
        }

        $sql = "CALL $procedureName(" . implode(', ', $placeholders) . ")";
        return $this->query($sql, $params);
    }

    /**
     * Get table schema information
     */
    public function getTableInfo($tableName) {
        $sql = "DESCRIBE $tableName";
        return $this->getAll($sql);
    }

    /**
     * Get foreign key information
     */
    public function getForeignKeys($tableName) {
        $sql = "
            SELECT
                COLUMN_NAME as column_name,
                REFERENCED_TABLE_NAME as referenced_table,
                REFERENCED_COLUMN_NAME as referenced_column
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ";

        return $this->getAll($sql, [DB_NAME, $tableName]);
    }

    /**
     * Log query for debugging
     */
    private function logQuery($sql, $params, $executionTime) {
        $this->query_log[] = [
            'sql' => $sql,
            'params' => $params,
            'execution_time' => $executionTime,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        if (SHOW_QUERY_TIMES && $executionTime > 0.1) { // Log slow queries
            error_log("Slow Query ({$executionTime}s): $sql");
        }
    }

    /**
     * Get query log
     */
    public function getQueryLog() {
        return $this->query_log;
    }

    /**
     * Get total query count
     */
    public function getQueryCount() {
        return $this->query_count;
    }

    /**
     * Get database connection for raw PDO operations
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Handle database errors
     */
    private function handleError($exception, $sql = '', $params = []) {
        $error_message = $exception->getMessage();

        // Log error
        error_log("Database Error: $error_message");
        if ($sql) {
            error_log("SQL: $sql");
            if (!empty($params)) {
                error_log("Parameters: " . json_encode($params));
            }
        }

        // In development, show detailed error
        if (DEBUG_MODE) {
            throw new Exception("Database Error: $error_message<br>SQL: $sql<br>Parameters: " . json_encode($params));
        } else {
            // In production, show generic error
            throw new Exception("A database error occurred. Please try again later.");
        }
    }

    /**
     * Test database connection
     */
    public function testConnection() {
        try {
            $this->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get database version
     */
    public function getVersion() {
        $result = $this->get("SELECT VERSION() as version");
        return $result['version'];
    }

    /**
     * Close connection
     */
    public function close() {
        $this->connection = null;
        self::$instance = null;
    }

    /**
     * Prevent cloning of singleton
     */
    private function __clone() {}

    /**
     * Prevent unserialization of singleton
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Extended PDOStatement class for query logging
 */
if (DEBUG_MODE && SAVE_QUERIES) {
    class LoggedPDOStatement extends PDOStatement {
        private $database;

        protected function __construct($database) {
            $this->database = $database;
        }

        public function execute($params = null) {
            $start_time = microtime(true);
            $result = parent::execute($params);
            $execution_time = microtime(true) - $start_time;

            // Log execution time
            $this->database->logQuery($this->queryString, $params ?: [], $execution_time);

            return $result;
        }
    }
}

?>