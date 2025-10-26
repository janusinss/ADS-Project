<?php
/**
 * Profile Class
 * 
 * Handles all CRUD operations for the profile table
 * Demonstrates: PDO prepared statements, error handling, advanced SQL with JOINs
 * 
 * @author Portfolio Project - WMSU CCS
 * @version 1.0
 */

class Profile {
    // Database connection
    private $conn;
    
    // Table name
    private $table_name = "profile";
    
    // Object properties matching database columns
    public $id;
    public $full_name;
    public $email;
    public $phone;
    public $address;
    public $bio;
    public $photo_url;
    public $linkedin_url;
    public $github_url;
    public $website_url;
    public $date_of_birth;
    public $created_at;
    public $updated_at;
    
    /**
     * Constructor - receives database connection
     * 
     * @param PDO $db Database connection object
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Add new profile record
     * 
     * @param array $data Associative array of profile data
     * @return bool True on success, false on failure
     */
    public function addRecord($data) {
        try {
            // SQL query to insert new profile
            $query = "INSERT INTO " . $this->table_name . " 
                     (full_name, email, phone, address, bio, photo_url, 
                      linkedin_url, github_url, website_url, date_of_birth)
                     VALUES 
                     (:full_name, :email, :phone, :address, :bio, :photo_url,
                      :linkedin_url, :github_url, :website_url, :date_of_birth)";
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters to prevent SQL injection
            $stmt->bindParam(':full_name', $data['full_name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':address', $data['address']);
            $stmt->bindParam(':bio', $data['bio']);
            $stmt->bindParam(':photo_url', $data['photo_url']);
            $stmt->bindParam(':linkedin_url', $data['linkedin_url']);
            $stmt->bindParam(':github_url', $data['github_url']);
            $stmt->bindParam(':website_url', $data['website_url']);
            $stmt->bindParam(':date_of_birth', $data['date_of_birth']);
            
            // Execute query
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            // Log error for debugging (in production, log to file)
            error_log("Profile addRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update existing profile record
     * 
     * @param int $id Profile ID to update
     * @param array $data Associative array of updated data
     * @return bool True on success, false on failure
     */
    public function updateRecord($id, $data) {
        try {
            // SQL query to update profile
            $query = "UPDATE " . $this->table_name . " 
                     SET 
                         full_name = :full_name,
                         email = :email,
                         phone = :phone,
                         address = :address,
                         bio = :bio,
                         photo_url = :photo_url,
                         linkedin_url = :linkedin_url,
                         github_url = :github_url,
                         website_url = :website_url,
                         date_of_birth = :date_of_birth
                     WHERE id = :id";
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':full_name', $data['full_name']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':address', $data['address']);
            $stmt->bindParam(':bio', $data['bio']);
            $stmt->bindParam(':photo_url', $data['photo_url']);
            $stmt->bindParam(':linkedin_url', $data['linkedin_url']);
            $stmt->bindParam(':github_url', $data['github_url']);
            $stmt->bindParam(':website_url', $data['website_url']);
            $stmt->bindParam(':date_of_birth', $data['date_of_birth']);
            
            // Execute query
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Profile updateRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete profile record
     * Note: This will cascade delete related education and certifications
     * 
     * @param int $id Profile ID to delete
     * @return bool True on success, false on failure
     */
    public function deleteRecord($id) {
        try {
            // SQL query to delete profile
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            // Bind parameter
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            // Execute query
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Profile deleteRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all profile records
     * Note: Typically only one profile exists per portfolio
     * 
     * @return array Array of profile records
     */
    public function getRecords() {
        try {
            // SQL query to select all profiles
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            // Execute query
            $stmt->execute();
            
            // Fetch all results as associative array
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Profile getRecords Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get single profile record by ID
     * 
     * @param int $id Profile ID
     * @return array|false Profile data array or false if not found
     */
    public function getRecordById($id) {
        try {
            // SQL query to select profile by ID
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            // Bind parameter
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            // Execute query
            $stmt->execute();
            
            // Fetch single result
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Profile getRecordById Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ADVANCED SQL: Get profile with complete statistics
     * Demonstrates: JOINs, COUNT aggregation, GROUP_CONCAT
     * 
     * @param int $id Profile ID
     * @return array|false Complete profile with stats
     */
    public function getProfileWithStats($id) {
        try {
            // Complex query with LEFT JOINs and aggregations
            $query = "SELECT 
                        p.*,
                        COUNT(DISTINCT e.id) AS total_education,
                        COUNT(DISTINCT c.id) AS total_certifications,
                        GROUP_CONCAT(DISTINCT e.degree ORDER BY e.end_date DESC SEPARATOR ', ') AS degrees,
                        GROUP_CONCAT(DISTINCT c.certification_name ORDER BY c.issue_date DESC SEPARATOR ', ') AS certifications,
                        (SELECT COUNT(*) FROM skills) AS total_skills,
                        (SELECT COUNT(*) FROM projects) AS total_projects,
                        (SELECT COUNT(*) FROM projects WHERE featured = TRUE) AS featured_projects,
                        fn_profile_completion(p.id) AS completion_percentage
                      FROM profile p
                      LEFT JOIN education e ON p.id = e.profile_id
                      LEFT JOIN certifications c ON p.id = c.profile_id
                      WHERE p.id = :id
                      GROUP BY p.id";
            
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            
            // Bind parameter
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            // Execute query
            $stmt->execute();
            
            // Fetch result
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Profile getProfileWithStats Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if email already exists (for validation)
     * 
     * @param string $email Email to check
     * @param int|null $exclude_id Profile ID to exclude (for updates)
     * @return bool True if exists, false otherwise
     */
    public function emailExists($email, $exclude_id = null) {
        try {
            if ($exclude_id) {
                $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                         WHERE email = :email AND id != :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':id', $exclude_id, PDO::PARAM_INT);
            } else {
                $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                         WHERE email = :email";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':email', $email);
            }
            
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $row['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("Profile emailExists Error: " . $e->getMessage());
            return false;
        }
    }
}
?>