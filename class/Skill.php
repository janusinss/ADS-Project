<?php
/**
 * Skill Class
 * 
 * Handles all CRUD operations for the skills table
 * Demonstrates: GROUP BY, HAVING, aggregate functions
 * 
 * @author Portfolio Project - WMSU CCS
 * @version 1.0
 */

class Skill {
    // Database connection
    private $conn;
    
    // Table name
    private $table_name = "skills";
    
    // Object properties
    public $id;
    public $skill_name;
    public $category;
    public $proficiency_level;
    public $years_of_experience;
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
     * Add new skill record
     * 
     * @param array $data Associative array of skill data
     * @return bool True on success, false on failure
     */
    public function addRecord($data) {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (skill_name, category, proficiency_level, years_of_experience, 
                      description, icon_class)
                     VALUES 
                     (:skill_name, :category, :proficiency_level, :years_of_experience,
                      :description, :icon_class)";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':skill_name', $data['skill_name']);
            $stmt->bindParam(':category', $data['category']);
            $stmt->bindParam(':proficiency_level', $data['proficiency_level']);
            $stmt->bindParam(':years_of_experience', $data['years_of_experience']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':icon_class', $data['icon_class']);
            
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Skill addRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update existing skill record
     * 
     * @param int $id Skill ID to update
     * @param array $data Associative array of updated data
     * @return bool True on success, false on failure
     */
    public function updateRecord($id, $data) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET 
                         skill_name = :skill_name,
                         category = :category,
                         proficiency_level = :proficiency_level,
                         years_of_experience = :years_of_experience,
                         description = :description,
                         icon_class = :icon_class
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':skill_name', $data['skill_name']);
            $stmt->bindParam(':category', $data['category']);
            $stmt->bindParam(':proficiency_level', $data['proficiency_level']);
            $stmt->bindParam(':years_of_experience', $data['years_of_experience']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':icon_class', $data['icon_class']);
            
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Skill updateRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete skill record
     * 
     * @param int $id Skill ID to delete
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
            error_log("Skill deleteRecord Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all skill records
     * 
     * @return array Array of skill records
     */
    public function getRecords() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     ORDER BY category ASC, proficiency_level DESC, years_of_experience DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Skill getRecords Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get single skill record by ID
     * 
     * @param int $id Skill ID
     * @return array|false Skill data array or false if not found
     */
    public function getRecordById($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Skill getRecordById Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ADVANCED SQL: Get skills by proficiency level
     * Demonstrates: WHERE clause with ENUM, ORDER BY
     * 
     * @param string $level Proficiency level (Beginner, Intermediate, Advanced, Expert)
     * @return array Array of skills matching the proficiency level
     */
    public function getSkillsByProficiencyLevel($level) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE proficiency_level = :level
                     ORDER BY years_of_experience DESC, skill_name ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':level', $level);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Skill getSkillsByProficiencyLevel Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ADVANCED SQL: Get skills grouped by category with statistics
     * Demonstrates: GROUP BY, COUNT, AVG, MAX, HAVING, aggregate functions
     * 
     * @return array Array of categories with skill statistics
     */
    public function getSkillsByCategory() {
        try {
            $query = "SELECT 
                        category,
                        COUNT(*) AS skill_count,
                        AVG(years_of_experience) AS avg_experience,
                        MAX(years_of_experience) AS max_experience,
                        MIN(years_of_experience) AS min_experience,
                        GROUP_CONCAT(skill_name ORDER BY proficiency_level DESC SEPARATOR ', ') AS skills_list
                     FROM " . $this->table_name . "
                     GROUP BY category
                     HAVING skill_count > 0
                     ORDER BY avg_experience DESC, skill_count DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Skill getSkillsByCategory Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ADVANCED SQL: Get skill statistics
     * Demonstrates: Multiple aggregate functions, subqueries
     * 
     * @return array|false Statistics about skills
     */
    public function getSkillStatistics() {
        try {
            $query = "SELECT 
                        COUNT(*) AS total_skills,
                        COUNT(DISTINCT category) AS total_categories,
                        AVG(years_of_experience) AS avg_years_experience,
                        MAX(years_of_experience) AS max_years_experience,
                        (SELECT COUNT(*) FROM " . $this->table_name . " 
                         WHERE proficiency_level = 'Expert') AS expert_count,
                        (SELECT COUNT(*) FROM " . $this->table_name . " 
                         WHERE proficiency_level = 'Advanced') AS advanced_count,
                        (SELECT COUNT(*) FROM " . $this->table_name . " 
                         WHERE proficiency_level = 'Intermediate') AS intermediate_count,
                        (SELECT COUNT(*) FROM " . $this->table_name . " 
                         WHERE proficiency_level = 'Beginner') AS beginner_count
                     FROM " . $this->table_name;
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Skill getSkillStatistics Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get skills by category name
     * 
     * @param string $category Category name
     * @return array Array of skills in the specified category
     */
    public function getSkillsByCategoryName($category) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE category = :category
                     ORDER BY proficiency_level DESC, years_of_experience DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':category', $category);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Skill getSkillsByCategoryName Error: " . $e->getMessage());
            return [];
        }
    }
}
?>