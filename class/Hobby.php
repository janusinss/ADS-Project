<?php
class Hobby {
    // Database connection
    private $conn;
    
    // Table name
    private $table_name = "hobbies";
    
    // Object properties
    public $id;
    public $hobby_name;
    public $description;
    public $icon_class;
    public $created_at;
    public $updated_at;
    
    /**
     * Constructor
     * 
     * @param PDO $db Database connection object
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Add new hobby record
     * 
     * @param array $data Associative array of hobby data
     * @return bool True on success, false on failure
     */
    public function addRecord($data) {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (hobby_name, description, icon_class)
                     VALUES 
                     (:hobby_name, :description, :icon_class)";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':hobby_name', $data['hobby_name']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':icon_class', $data['icon_class']);
            
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Hobby addRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update existing hobby record
     * 
     * @param int $id Hobby ID to update
     * @param array $data Associative array of updated data
     * @return bool True on success, false on failure
     */
    public function updateRecord($id, $data) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET 
                         hobby_name = :hobby_name,
                         description = :description,
                         icon_class = :icon_class
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':hobby_name', $data['hobby_name']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':icon_class', $data['icon_class']);
            
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Hobby updateRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete hobby record
     * 
     * @param int $id Hobby ID to delete
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
            error_log("Hobby deleteRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all hobby records
     * 
     * @return array Array of hobby records
     */
    public function getRecords() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     ORDER BY hobby_name ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Hobby getRecords Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get single hobby record by ID
     * 
     * @param int $id Hobby ID
     * @return array|false Hobby data array or false if not found
     */
    public function getRecordById($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Hobby getRecordById Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get total count of hobbies
     * 
     * @return int Total number of hobbies
     */
    public function getTotalCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row['total'];
            
        } catch (PDOException $e) {
            error_log("Hobby getTotalCount Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Search hobbies by name
     * 
     * @param string $keyword Search keyword
     * @return array Array of matching hobbies
     */
    public function searchByName($keyword) {
        try {
            $searchTerm = "%{$keyword}%";
            
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE hobby_name LIKE :keyword 
                        OR description LIKE :keyword
                     ORDER BY hobby_name ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':keyword', $searchTerm);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Hobby searchByName Error: " . $e->getMessage());
            return [];
        }
    }
}
?>