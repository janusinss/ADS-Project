<?php
/**
 * Projects API Endpoint
 * 
 * Handles HTTP requests for projects CRUD operations
 * Methods: GET, POST, PUT, DELETE
 * Returns: JSON responses
 * 
 * @author Portfolio Project - WMSU CCS
 * @version 1.0
 * 
 * POSTMAN TESTING EXAMPLES:
 * 
 * 1. GET All Projects
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/projects_api.php
 * 
 * 2. GET Single Project
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/projects_api.php?id=1
 * 
 * 3. GET Featured Projects
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/projects_api.php?action=featured
 * 
 * 4. GET Projects with Duration
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/projects_api.php?action=with_duration
 * 
 * 5. GET Project Statistics
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/projects_api.php?action=statistics
 * 
 * 6. GET Search by Technology
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/projects_api.php?action=search&technology=PHP
 * 
 * 7. POST Create Project
 *    Method: POST
 *    URL: http://localhost/portfolio_project/api/projects_api.php
 *    Body (raw JSON):
 *    {
 *        "project_title": "Portfolio Website",
 *        "description": "Personal portfolio website with admin panel",
 *        "technologies_used": "PHP, MySQL, JavaScript, Tailwind CSS",
 *        "project_url": "https://myportfolio.com",
 *        "github_url": "https://github.com/user/portfolio",
 *        "image_url": "https://via.placeholder.com/600x400",
 *        "start_date": "2024-01-01",
 *        "end_date": "2024-03-15",
 *        "status": "Completed",
 *        "featured": true
 *    }
 * 
 * 8. PUT Update Project
 *    Method: PUT
 *    URL: http://localhost/portfolio_project/api/projects_api.php
 *    Body (raw JSON):
 *    {
 *        "id": 1,
 *        "project_title": "Updated Project Title",
 *        "description": "Updated description",
 *        "technologies_used": "PHP, MySQL, React",
 *        "project_url": "https://updated.com",
 *        "github_url": "https://github.com/user/updated",
 *        "image_url": "https://via.placeholder.com/600x400",
 *        "start_date": "2024-01-01",
 *        "end_date": "2024-04-01",
 *        "status": "Completed",
 *        "featured": true
 *    }
 * 
 * 9. DELETE Project
 *    Method: DELETE
 *    URL: http://localhost/portfolio_project/api/projects_api.php?id=1
 */

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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