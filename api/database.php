<?php
/**
 * Database Connection Class
 * 
 * Establishes PDO connection to MySQL database
 * Follows singleton pattern for efficient connection management
 * 
 * @author Portfolio Project - WMSU CCS
 * @version 1.0
 */

class Database {
    // Database credentials
    private $host = "localhost";
    private $db_name = "portfolio_db";
    private $username = "root";
    private $password = ""; // Default XAMPP password is empty
    private $conn;
    
    /**
     * Get database connection
     * 
     * @return PDO|null PDO connection object or null on failure
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            // Create PDO connection
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            
            // Set charset to UTF-8
            $this->conn->exec("set names utf8mb4");
            
            // Set error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Set default fetch mode to associative array
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $exception) {
            // Log connection error
            error_log("Database Connection Error: " . $exception->getMessage());
            
            // In production, don't expose database errors to users
            // For development, you can uncomment the line below:
            // echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
    
    /**
     * Test database connection
     * 
     * @return array Status and message
     */
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            
            if ($conn) {
                return [
                    'status' => 'success',
                    'message' => 'Database connected successfully',
                    'database' => $this->db_name,
                    'host' => $this->host
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to connect to database'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
?>