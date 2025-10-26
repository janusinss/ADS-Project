<?php
/**
 * Hobbies API Endpoint
 * 
 * Handles HTTP requests for hobbies CRUD operations
 * Methods: GET, POST, PUT, DELETE
 * Returns: JSON responses
 * 
 * @author Portfolio Project - WMSU CCS
 * @version 1.0
 * 
 * POSTMAN TESTING EXAMPLES:
 * 
 * 1. GET All Hobbies
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/hobbies_api.php
 * 
 * 2. GET Single Hobby
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/hobbies_api.php?id=1
 * 
 * 3. GET Search Hobbies
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/hobbies_api.php?action=search&keyword=coding
 * 
 * 4. POST Create Hobby
 *    Method: POST
 *    URL: http://localhost/portfolio_project/api/hobbies_api.php
 *    Body (raw JSON):
 *    {
 *        "hobby_name": "Swimming",
 *        "description": "Enjoy swimming at the beach on weekends",
 *        "icon_class": "fas fa-swimming-pool"
 *    }
 * 
 * 5. PUT Update Hobby
 *    Method: PUT
 *    URL: http://localhost/portfolio_project/api/hobbies_api.php
 *    Body (raw JSON):
 *    {
 *        "id": 1,
 *        "hobby_name": "Competitive Coding",
 *        "description": "Participating in coding competitions and hackathons",
 *        "icon_class": "fas fa-code"
 *    }
 * 
 * 6. DELETE Hobby
 *    Method: DELETE
 *    URL: http://localhost/portfolio_project/api/hobbies_api.php?id=1
 */

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include required files
require_once 'database.php';
require_once '../class/Hobby.php';

// Instantiate database and hobby object
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

$hobby = new Hobby($db);

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Handle different HTTP methods
switch ($method) {
    case 'GET':
        handleGet($hobby);
        break;
    
    case 'POST':
        handlePost($hobby);
        break;
    
    case 'PUT':
        handlePut($hobby);
        break;
    
    case 'DELETE':
        handleDelete($hobby);
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
function handleGet($hobby) {
    try {
        // Check for search action
        if (isset($_GET['action']) && $_GET['action'] === 'search') {
            if (!isset($_GET['keyword'])) {
                http_response_code(400);
                echo json_encode([
                    "status" => "error",
                    "message" => "Keyword parameter is required for search"
                ]);
                return;
            }
            
            $result = $hobby->searchByName($_GET['keyword']);
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Search results retrieved successfully",
                "count" => count($result),
                "data" => $result
            ]);
            return;
        }
        
        // Get single hobby by ID
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $result = $hobby->getRecordById($id);
            
            if ($result) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Hobby retrieved successfully",
                    "data" => $result
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "status" => "error",
                    "message" => "Hobby not found"
                ]);
            }
        } else {
            // Get all hobbies
            $result = $hobby->getRecords();
            
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Hobbies retrieved successfully",
                "count" => count($result),
                "data" => $result
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error retrieving hobbies: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle POST requests
 */
function handlePost($hobby) {
    try {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validate required fields
        if (empty($data['hobby_name'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Missing required field: hobby_name is required"
            ]);
            return;
        }
        
        // Prepare hobby data
        $hobbyData = [
            'hobby_name' => trim($data['hobby_name']),
            'description' => $data['description'] ?? null,
            'icon_class' => $data['icon_class'] ?? null
        ];
        
        // Create hobby
        if ($hobby->addRecord($hobbyData)) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Hobby created successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to create hobby"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error creating hobby: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle PUT requests
 */
function handlePut($hobby) {
    try {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validate ID
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Hobby ID is required"
            ]);
            return;
        }
        
        $id = intval($data['id']);
        
        // Check if hobby exists
        if (!$hobby->getRecordById($id)) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Hobby not found"
            ]);
            return;
        }
        
        // Validate required fields
        if (empty($data['hobby_name'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Missing required field: hobby_name is required"
            ]);
            return;
        }
        
        // Prepare update data
        $hobbyData = [
            'hobby_name' => trim($data['hobby_name']),
            'description' => $data['description'] ?? null,
            'icon_class' => $data['icon_class'] ?? null
        ];
        
        // Update hobby
        if ($hobby->updateRecord($id, $hobbyData)) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Hobby updated successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to update hobby"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error updating hobby: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle DELETE requests
 */
function handleDelete($hobby) {
    try {
        // Get ID from query parameter
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Hobby ID is required"
            ]);
            return;
        }
        
        $id = intval($_GET['id']);
        
        // Check if hobby exists
        if (!$hobby->getRecordById($id)) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Hobby not found"
            ]);
            return;
        }
        
        // Delete hobby
        if ($hobby->deleteRecord($id)) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Hobby deleted successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to delete hobby"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error deleting hobby: " . $e->getMessage()
        ]);
    }
}
?>