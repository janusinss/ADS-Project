<?php
/**
 * Skills API Endpoint
 * 
 * Handles HTTP requests for skills CRUD operations
 * Methods: GET, POST, PUT, DELETE
 * Returns: JSON responses
 * 
 * @author Portfolio Project - WMSU CCS
 * @version 1.0
 * 
 * POSTMAN TESTING EXAMPLES:
 * 
 * 1. GET All Skills
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/skills_api.php
 * 
 * 2. GET Single Skill
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/skills_api.php?id=1
 * 
 * 3. GET Skills by Category
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/skills_api.php?action=by_category
 * 
 * 4. GET Skills by Proficiency Level
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/skills_api.php?action=by_proficiency&level=Advanced
 * 
 * 5. GET Skill Statistics
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/skills_api.php?action=statistics
 * 
 * 6. POST Create Skill
 *    Method: POST
 *    URL: http://localhost/portfolio_project/api/skills_api.php
 *    Body (raw JSON):
 *    {
 *        "skill_name": "React.js",
 *        "category": "Frontend",
 *        "proficiency_level": "Intermediate",
 *        "years_of_experience": 2.0,
 *        "description": "Building interactive UIs with React",
 *        "icon_class": "fab fa-react"
 *    }
 * 
 * 7. PUT Update Skill
 *    Method: PUT
 *    URL: http://localhost/portfolio_project/api/skills_api.php
 *    Body (raw JSON):
 *    {
 *        "id": 1,
 *        "skill_name": "PHP Advanced",
 *        "category": "Backend",
 *        "proficiency_level": "Expert",
 *        "years_of_experience": 4.0,
 *        "description": "Updated description",
 *        "icon_class": "fab fa-php"
 *    }
 * 
 * 8. DELETE Skill
 *    Method: DELETE
 *    URL: http://localhost/portfolio_project/api/skills_api.php?id=1
 */

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include required files
require_once 'database.php';
require_once '../class/Skill.php';

// Instantiate database and skill object
$database = new Database();
$db = $database->getConnection();

// Check database connection
if (!$db) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit();
}

$skill = new Skill($db);

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Handle different HTTP methods
switch ($method) {
    case 'GET':
        handleGet($skill);
        break;
    
    case 'POST':
        handlePost($skill);
        break;
    
    case 'PUT':
        handlePut($skill);
        break;
    
    case 'DELETE':
        handleDelete($skill);
        break;
    
    default:
        http_response_code(405);
        echo json_encode([
            "status" => "error",
            "message" => "Method not allowed"
        ]);
        break;
}

/**
 * Handle GET requests
 */
function handleGet($skill) {
    try {
        // Check for special actions
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'by_category':
                    $result = $skill->getSkillsByCategory();
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Skills by category retrieved successfully",
                        "data" => $result
                    ]);
                    return;
                
                case 'by_proficiency':
                    if (!isset($_GET['level'])) {
                        http_response_code(400);
                        echo json_encode([
                            "status" => "error",
                            "message" => "Proficiency level is required"
                        ]);
                        return;
                    }
                    $result = $skill->getSkillsByProficiencyLevel($_GET['level']);
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Skills by proficiency level retrieved successfully",
                        "data" => $result
                    ]);
                    return;
                
                case 'statistics':
                    $result = $skill->getSkillStatistics();
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Skill statistics retrieved successfully",
                        "data" => $result
                    ]);
                    return;
            }
        }
        
        // Get single skill by ID
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $result = $skill->getRecordById($id);
            
            if ($result) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Skill retrieved successfully",
                    "data" => $result
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "status" => "error",
                    "message" => "Skill not found"
                ]);
            }
        } else {
            // Get all skills
            $result = $skill->getRecords();
            
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Skills retrieved successfully",
                "count" => count($result),
                "data" => $result
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error retrieving skills: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle POST requests
 */
function handlePost($skill) {
    try {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validate required fields
        if (empty($data['skill_name']) || empty($data['category']) || empty($data['proficiency_level'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: skill_name, category, and proficiency_level are required"
            ]);
            return;
        }
        
        // Validate proficiency level
        $validLevels = ['Beginner', 'Intermediate', 'Advanced', 'Expert'];
        if (!in_array($data['proficiency_level'], $validLevels)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid proficiency level. Must be: Beginner, Intermediate, Advanced, or Expert"
            ]);
            return;
        }
        
        // Prepare skill data
        $skillData = [
            'skill_name' => trim($data['skill_name']),
            'category' => trim($data['category']),
            'proficiency_level' => $data['proficiency_level'],
            'years_of_experience' => $data['years_of_experience'] ?? 0,
            'description' => $data['description'] ?? null,
            'icon_class' => $data['icon_class'] ?? null
        ];
        
        // Create skill
        if ($skill->addRecord($skillData)) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Skill created successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to create skill"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error creating skill: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle PUT requests
 */
function handlePut($skill) {
    try {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validate ID
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Skill ID is required"
            ]);
            return;
        }
        
        $id = intval($data['id']);
        
        // Check if skill exists
        if (!$skill->getRecordById($id)) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Skill not found"
            ]);
            return;
        }
        
        // Validate required fields
        if (empty($data['skill_name']) || empty($data['category']) || empty($data['proficiency_level'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: skill_name, category, and proficiency_level are required"
            ]);
            return;
        }
        
        // Validate proficiency level
        $validLevels = ['Beginner', 'Intermediate', 'Advanced', 'Expert'];
        if (!in_array($data['proficiency_level'], $validLevels)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid proficiency level. Must be: Beginner, Intermediate, Advanced, or Expert"
            ]);
            return;
        }
        
        // Prepare update data
        $skillData = [
            'skill_name' => trim($data['skill_name']),
            'category' => trim($data['category']),
            'proficiency_level' => $data['proficiency_level'],
            'years_of_experience' => $data['years_of_experience'] ?? 0,
            'description' => $data['description'] ?? null,
            'icon_class' => $data['icon_class'] ?? null
        ];
        
        // Update skill
        if ($skill->updateRecord($id, $skillData)) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Skill updated successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to update skill"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error updating skill: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle DELETE requests
 */
function handleDelete($skill) {
    try {
        // Get ID from query parameter
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Skill ID is required"
            ]);
            return;
        }
        
        $id = intval($_GET['id']);
        
        // Check if skill exists
        if (!$skill->getRecordById($id)) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Skill not found"
            ]);
            return;
        }
        
        // Delete skill
        if ($skill->deleteRecord($id)) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Skill deleted successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to delete skill"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error deleting skill: " . $e->getMessage()
        ]);
    }
}
?>