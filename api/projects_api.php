<?php
// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include required files
require_once 'database.php';
require_once '../class/Project.php';

// Instantiate database and project object
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

$project = new Project($db);

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Handle different HTTP methods
switch ($method) {
    case 'GET':
        handleGet($project);
        break;
    case 'POST':
        handlePost($project);
        break;
    
    case 'PUT':
        handlePut($project);
        break;
    
    case 'DELETE':
        handleDelete($project);
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
function handleGet($project) {
    try {
        // Check for special actions
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'featured':
                    $result = $project->getFeaturedProjects();
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Featured projects retrieved successfully",
                        "count" => count($result),
                        "data" => $result
                    ]);
                    return;
                
                case 'with_duration':
                    $result = $project->getProjectsWithDuration();
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Projects with duration retrieved successfully",
                        "data" => $result
                    ]);
                    return;
                
                case 'statistics':
                    $result = $project->getProjectStatistics();
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Project statistics retrieved successfully",
                        "data" => $result
                    ]);
                    return;
                
                case 'search':
                    if (!isset($_GET['technology'])) {
                        http_response_code(400);
                        echo json_encode([
                            "status" => "error",
                            "message" => "Technology parameter is required for search"
                        ]);
                        return;
                    }
                    $result = $project->searchByTechnology($_GET['technology']);
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Search results retrieved successfully",
                        "count" => count($result),
                        "data" => $result
                    ]);
                    return;
                
                case 'by_status':
                    if (!isset($_GET['status'])) {
                        http_response_code(400);
                        echo json_encode([
                            "status" => "error",
                            "message" => "Status parameter is required"
                        ]);
                        return;
                    }
                    $result = $project->getProjectsByStatus($_GET['status']);
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Projects by status retrieved successfully",
                        "data" => $result
                    ]);
                    return;
            }
        }
        
        // Get single project by ID
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $result = $project->getRecordById($id);
            
            if ($result) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Project retrieved successfully",
                    "data" => $result
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "status" => "error",
                    "message" => "Project not found"
                ]);
            }
        } else {
            // Get all projects
            $result = $project->getRecords();
            
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Projects retrieved successfully",
                "count" => count($result),
                "data" => $result
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error retrieving projects: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle POST requests
 */
function handlePost($project) {
    try {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validate required fields
        if (empty($data['project_title']) || empty($data['description'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: project_title and description are required"
            ]);
            return;
        }
        
        // Validate status
        $validStatuses = ['Planning', 'In Progress', 'Completed', 'Archived'];
        $status = $data['status'] ?? 'In Progress';
        if (!in_array($status, $validStatuses)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid status. Must be: Planning, In Progress, Completed, or Archived"
            ]);
            return;
        }
        
        // Prepare project data
        $projectData = [
            'project_title' => trim($data['project_title']),
            'description' => trim($data['description']),
            'technologies_used' => $data['technologies_used'] ?? null,
            'project_url' => $data['project_url'] ?? null,
            'github_url' => $data['github_url'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'status' => $status,
            'featured' => isset($data['featured']) ? (bool)$data['featured'] : false
        ];
        
        // Create project
        if ($project->addRecord($projectData)) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Project created successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to create project"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error creating project: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle PUT requests
 */
function handlePut($project) {
    try {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validate ID
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Project ID is required"
            ]);
            return;
        }
        
        $id = intval($data['id']);
        
        // Check if project exists
        if (!$project->getRecordById($id)) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Project not found"
            ]);
            return;
        }
        
        // Validate required fields
        if (empty($data['project_title']) || empty($data['description'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: project_title and description are required"
            ]);
            return;
        }
        
        // Validate status
        $validStatuses = ['Planning', 'In Progress', 'Completed', 'Archived'];
        $status = $data['status'] ?? 'In Progress';
        if (!in_array($status, $validStatuses)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid status. Must be: Planning, In Progress, Completed, or Archived"
            ]);
            return;
        }
        
        // Prepare update data
        $projectData = [
            'project_title' => trim($data['project_title']),
            'description' => trim($data['description']),
            'technologies_used' => $data['technologies_used'] ?? null,
            'project_url' => $data['project_url'] ?? null,
            'github_url' => $data['github_url'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'status' => $status,
            'featured' => isset($data['featured']) ? (bool)$data['featured'] : false
        ];
        
        // Update project
        if ($project->updateRecord($id, $projectData)) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Project updated successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to update project"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error updating project: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle DELETE requests
 */
function handleDelete($project) {
    try {
        // Get ID from query parameter
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Project ID is required"
            ]);
            return;
        }
        
        $id = intval($_GET['id']);
        
        // Check if project exists
        if (!$project->getRecordById($id)) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Project not found"
            ]);
            return;
        }
        
        // Delete project
        if ($project->deleteRecord($id)) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Project deleted successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to delete project"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error deleting project: " . $e->getMessage()
        ]);
    }
}
?>