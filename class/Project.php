<?php
/**
 * Project Class
 * 
 * Handles all CRUD operations for the projects table
 * Demonstrates: FULLTEXT search, date calculations, CASE statements
 * 
 * @author Portfolio Project - WMSU CCS
 * @version 1.0
 */

class Project {
    // Database connection
    private $conn;
    
    // Table name
    private $table_name = "projects";
    
    // Object properties
    public $id;
    public $project_title;
    public $description;
    public $technologies_used;
    public $project_url;
    public $github_url;
    public $image_url;
    public $start_date;
    public $end_date;
    public $status;
    public $featured;
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
     * Add new project record
     * 
     * @param array $data Associative array of project data
     * @return bool True on success, false on failure
     */
    public function addRecord($data) {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (project_title, description, technologies_used, project_url, 
                      github_url, image_url, start_date, end_date, status, featured)
                     VALUES 
                     (:project_title, :description, :technologies_used, :project_url,
                      :github_url, :image_url, :start_date, :end_date, :status, :featured)";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':project_title', $data['project_title']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':technologies_used', $data['technologies_used']);
            $stmt->bindParam(':project_url', $data['project_url']);
            $stmt->bindParam(':github_url', $data['github_url']);
            $stmt->bindParam(':image_url', $data['image_url']);
            $stmt->bindParam(':start_date', $data['start_date']);
            $stmt->bindParam(':end_date', $data['end_date']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':featured', $data['featured'], PDO::PARAM_BOOL);
            
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Project addRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update existing project record
     * 
     * @param int $id Project ID to update
     * @param array $data Associative array of updated data
     * @return bool True on success, false on failure
     */
    public function updateRecord($id, $data) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET 
                         project_title = :project_title,
                         description = :description,
                         technologies_used = :technologies_used,
                         project_url = :project_url,
                         github_url = :github_url,
                         image_url = :image_url,
                         start_date = :start_date,
                         end_date = :end_date,
                         status = :status,
                         featured = :featured
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':project_title', $data['project_title']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':technologies_used', $data['technologies_used']);
            $stmt->bindParam(':project_url', $data['project_url']);
            $stmt->bindParam(':github_url', $data['github_url']);
            $stmt->bindParam(':image_url', $data['image_url']);
            $stmt->bindParam(':start_date', $data['start_date']);
            $stmt->bindParam(':end_date', $data['end_date']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':featured', $data['featured'], PDO::PARAM_BOOL);
            
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Project updateRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete project record
     * 
     * @param int $id Project ID to delete
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
            error_log("Project deleteRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all project records
     * 
     * @return array Array of project records
     */
    public function getRecords() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     ORDER BY featured DESC, start_date DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Project getRecords Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get single project record by ID
     * 
     * @param int $id Project ID
     * @return array|false Project data array or false if not found
     */
    public function getRecordById($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Project getRecordById Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ADVANCED SQL: Search projects by technology using FULLTEXT search
     * Demonstrates: FULLTEXT index usage with MATCH AGAINST
     * 
     * @param string $technology Technology keyword to search
     * @return array Array of matching projects with relevance score
     */
    public function searchByTechnology($technology) {
        try {
            $query = "SELECT *,
                        MATCH(technologies_used, description) 
                        AGAINST(:technology IN NATURAL LANGUAGE MODE) AS relevance
                     FROM " . $this->table_name . "
                     WHERE MATCH(technologies_used, description) 
                        AGAINST(:technology IN NATURAL LANGUAGE MODE)
                     ORDER BY relevance DESC, featured DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':technology', $technology);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Project searchByTechnology Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ADVANCED SQL: Get projects with duration calculation
     * Demonstrates: DATE functions, CASE statements, subqueries
     * 
     * @return array Array of projects with calculated duration
     */
    public function getProjectsWithDuration() {
        try {
            $query = "SELECT 
                        *,
                        DATEDIFF(COALESCE(end_date, CURDATE()), start_date) AS duration_days,
                        CASE 
                            WHEN DATEDIFF(COALESCE(end_date, CURDATE()), start_date) > 180 
                                THEN 'Long Term'
                            WHEN DATEDIFF(COALESCE(end_date, CURDATE()), start_date) > 90 
                                THEN 'Medium Term'
                            ELSE 'Short Term'
                        END AS duration_type,
                        (SELECT AVG(DATEDIFF(COALESCE(end_date, CURDATE()), start_date)) 
                         FROM " . $this->table_name . ") AS avg_project_duration
                     FROM " . $this->table_name . "
                     ORDER BY duration_days DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Project getProjectsWithDuration Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get featured projects only
     * 
     * @return array Array of featured projects
     */
    public function getFeaturedProjects() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE featured = TRUE
                     ORDER BY start_date DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Project getFeaturedProjects Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ADVANCED SQL: Get project statistics by status
     * Demonstrates: COUNT with CASE, aggregate functions, GROUP BY
     * 
     * @return array|false Project statistics
     */
    public function getProjectStatistics() {
        try {
            $query = "SELECT 
                        COUNT(*) AS total_projects,
                        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completed_count,
                        SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) AS in_progress_count,
                        SUM(CASE WHEN status = 'Planning' THEN 1 ELSE 0 END) AS planning_count,
                        SUM(CASE WHEN status = 'Archived' THEN 1 ELSE 0 END) AS archived_count,
                        SUM(CASE WHEN featured = TRUE THEN 1 ELSE 0 END) AS featured_count,
                        AVG(DATEDIFF(COALESCE(end_date, CURDATE()), start_date)) AS avg_duration_days,
                        MAX(DATEDIFF(COALESCE(end_date, CURDATE()), start_date)) AS max_duration_days,
                        MIN(DATEDIFF(COALESCE(end_date, CURDATE()), start_date)) AS min_duration_days
                     FROM " . $this->table_name;
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Project getProjectStatistics Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get projects by status
     * 
     * @param string $status Project status (Planning, In Progress, Completed, Archived)
     * @return array Array of projects with the specified status
     */
    public function getProjectsByStatus($status) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE status = :status
                     ORDER BY start_date DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Project getProjectsByStatus Error: " . $e->getMessage());
            return [];
        }
    }
}
?>