<?php
/**
 * Contact Class
 * 
 * Handles all CRUD operations for the contacts table
 * Demonstrates: Date filtering, status management
 * 
 * @author Portfolio Project - WMSU CCS
 * @version 1.0
 */

class Contact {
    // Database connection
    private $conn;
    
    // Table name
    private $table_name = "contacts";
    
    // Object properties
    public $id;
    public $name;
    public $email;
    public $subject;
    public $message;
    public $status;
    public $ip_address;
    public $created_at;
    
    /**
     * Constructor
     * 
     * @param PDO $db Database connection object
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Add new contact record
     * 
     * @param array $data Associative array of contact data
     * @return bool True on success, false on failure
     */
    public function addRecord($data) {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (name, email, subject, message, status, ip_address)
                     VALUES 
                     (:name, :email, :subject, :message, :status, :ip_address)";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':subject', $data['subject']);
            $stmt->bindParam(':message', $data['message']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':ip_address', $data['ip_address']);
            
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Contact addRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update existing contact record (mainly for status updates)
     * 
     * @param int $id Contact ID to update
     * @param array $data Associative array of updated data
     * @return bool True on success, false on failure
     */
    public function updateRecord($id, $data) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET 
                         name = :name,
                         email = :email,
                         subject = :subject,
                         message = :message,
                         status = :status
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':subject', $data['subject']);
            $stmt->bindParam(':message', $data['message']);
            $stmt->bindParam(':status', $data['status']);
            
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Contact updateRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update contact status only
     * 
     * @param int $id Contact ID
     * @param string $status New status (New, Read, Replied, Archived)
     * @return bool True on success, false on failure
     */
    public function updateStatus($id, $status) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET status = :status
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status);
            
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Contact updateStatus Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete contact record
     * 
     * @param int $id Contact ID to delete
     * @return bool True on success, false on failure
     */
    public function deleteRecord($id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Contact deleteRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all contact records
     * 
     * @return array Array of contact records
     */
    public function getRecords() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     ORDER BY created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Contact getRecords Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get single contact record by ID
     * 
     * @param int $id Contact ID
     * @return array|false Contact data array or false if not found
     */
    public function getRecordById($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Contact getRecordById Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get contacts by status
     * 
     * @param string $status Contact status (New, Read, Replied, Archived)
     * @return array Array of contacts with the specified status
     */
    public function getContactsByStatus($status) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE status = :status
                     ORDER BY created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Contact getContactsByStatus Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ADVANCED SQL: Get recent contacts (last 30 days)
     * Demonstrates: DATE functions, WHERE with date filtering
     * 
     * @param int $days Number of days to look back (default 30)
     * @return array Array of recent contacts
     */
    public function getRecentContacts($days = 30) {
        try {
            $query = "SELECT 
                        *,
                        DATE_FORMAT(created_at, '%M %d, %Y %h:%i %p') AS formatted_date,
                        DATEDIFF(CURDATE(), DATE(created_at)) AS days_ago
                     FROM " . $this->table_name . "
                     WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                     ORDER BY created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Contact getRecentContacts Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ADVANCED SQL: Get contact statistics
     * Demonstrates: COUNT with CASE, aggregate functions by status
     * 
     * @return array|false Contact statistics
     */
    public function getContactStatistics() {
        try {
            $query = "SELECT 
                        COUNT(*) AS total_contacts,
                        SUM(CASE WHEN status = 'New' THEN 1 ELSE 0 END) AS new_count,
                        SUM(CASE WHEN status = 'Read' THEN 1 ELSE 0 END) AS read_count,
                        SUM(CASE WHEN status = 'Replied' THEN 1 ELSE 0 END) AS replied_count,
                        SUM(CASE WHEN status = 'Archived' THEN 1 ELSE 0 END) AS archived_count,
                        (SELECT COUNT(*) FROM " . $this->table_name . " 
                         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)) AS last_week_count,
                        (SELECT COUNT(*) FROM " . $this->table_name . " 
                         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) AS last_month_count
                     FROM " . $this->table_name;
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Contact getContactStatistics Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ADVANCED SQL: Get contacts grouped by date
     * Demonstrates: DATE_FORMAT, GROUP BY with date
     * 
     * @return array Array of contacts grouped by date
     */
    public function getContactsByDate() {
        try {
            $query = "SELECT 
                        DATE(created_at) AS contact_date,
                        COUNT(*) AS count_per_day,
                        GROUP_CONCAT(name SEPARATOR ', ') AS contacts_list
                     FROM " . $this->table_name . "
                     GROUP BY DATE(created_at)
                     ORDER BY contact_date DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Contact getContactsByDate Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search contacts by name or email
     * 
     * @param string $keyword Search keyword
     * @return array Array of matching contacts
     */
    public function searchContacts($keyword) {
        try {
            $searchTerm = "%{$keyword}%";
            
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE name LIKE :keyword 
                        OR email LIKE :keyword 
                        OR subject LIKE :keyword
                     ORDER BY created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':keyword', $searchTerm);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Contact searchContacts Error: " . $e->getMessage());
            return [];
        }
    }
}
?>