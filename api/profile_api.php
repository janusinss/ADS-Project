<?php
/**
 * Profile API Endpoint
 * 
 * Handles HTTP requests for profile CRUD operations
 * Methods: GET, POST, PUT, DELETE
 * Returns: JSON responses
 * 
 * @author Portfolio Project - WMSU CCS
 * @version 1.0
 * 
 * POSTMAN TESTING EXAMPLES:
 * 
 * 1. GET All Profiles
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/profile_api.php
 * 
 * 2. GET Single Profile
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/profile_api.php?id=1
 * 
 * 3. GET Profile with Statistics
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/profile_api.php?action=stats&id=1
 * 
 * 4. POST Create Profile
 *    Method: POST
 *    URL: http://localhost/portfolio_project/api/profile_api.php
 *    Body (raw JSON):
 *    {
 *        "full_name": "Maria Clara Santos",
 *        "email": "maria.santos@email.com",
 *        "phone": "+63 917 123 4567",
 *        "address": "Manila, Philippines",
 *        "bio": "Web developer and designer",
 *        "photo_url": "https://via.placeholder.com/300",
 *        "linkedin_url": "https://linkedin.com/in/mariasantos",
 *        "github_url": "https://github.com/mariasantos",
 *        "website_url": "https://mariasantos.dev",
 *        "date_of_birth": "2000-03-15"
 *    }
 * 
 * 5. PUT Update Profile
 *    Method: PUT
 *    URL: http://localhost/portfolio_project/api/profile_api.php
 *    Body (raw JSON):
 *    {
 *        "id": 1,
 *        "full_name": "Juan Dela Cruz Updated",
 *        "email": "juan.updated@email.com",
 *        "phone": "+63 912 999 8888",
 *        "address": "Zamboanga City, Philippines",
 *        "bio": "Updated bio text",
 *        "photo_url": "https://via.placeholder.com/300",
 *        "linkedin_url": "https://linkedin.com/in/juandelacruz",
 *        "github_url": "https://github.com/juandelacruz",
 *        "website_url": "https://juandelacruz.dev",
 *        "date_of_birth": "2001-05-15"
 *    }
 * 
 * 6. DELETE Profile
 *    Method: DELETE
 *    URL: http://localhost/portfolio_project/api/profile_api.php?id=1
 */

// Set headers for JSON response and CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database connection and Profile class
require_once 'database.php';
require_once '../class/Profile.php';

// Instantiate database and profile object
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

$profile = new Profile($db);

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Handle different HTTP methods
switch ($method) {
    
    case 'GET':
        handleGet($profile);
        break;
    
    case 'POST':
        handlePost($profile);
        break;
    
    case 'PUT':
        handlePut($profile);
        break;
    
    case 'DELETE':
        handleDelete($profile);
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
 * Retrieves all profiles or single profile by ID
 */
function handleGet($profile) {
    try {
        // Check if requesting profile with statistics
        if (isset($_GET['action']) && $_GET['action'] === 'stats' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $result = $profile->getProfileWithStats($id);
            
            if ($result) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Profile with statistics retrieved successfully",
                    "data" => $result
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "status" => "error",
                    "message" => "Profile not found"
                ]);
            }
            return;
        }
        
        // Check if ID parameter exists (get single profile)
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $result = $profile->getRecordById($id);
            
            if ($result) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Profile retrieved successfully",
                    "data" => $result
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "status" => "error",
                    "message" => "Profile not found"
                ]);
            }
        } else {
            // Get all profiles
            $result = $profile->getRecords();
            
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Profiles retrieved successfully",
                "count" => count($result),
                "data" => $result
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error retrieving profile: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle POST requests
 * Creates a new profile record
 */
function handlePost($profile) {
    try {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validate required fields
        if (empty($data['full_name']) || empty($data['email'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: full_name and email are required"
            ]);
            return;
        }
        
        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid email format"
            ]);
            return;
        }
        
        // Check if email already exists
        if ($profile->emailExists($data['email'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Email already exists"
            ]);
            return;
        }
        
        // Set default values for optional fields
        $profileData = [
            'full_name' => trim($data['full_name']),
            'email' => trim($data['email']),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'bio' => $data['bio'] ?? null,
            'photo_url' => $data['photo_url'] ?? null,
            'linkedin_url' => $data['linkedin_url'] ?? null,
            'github_url' => $data['github_url'] ?? null,
            'website_url' => $data['website_url'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null
        ];
        
        // Create profile
        if ($profile->addRecord($profileData)) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Profile created successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to create profile"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error creating profile: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle PUT requests
 * Updates an existing profile record
 */
function handlePut($profile) {
    try {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validate ID
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Profile ID is required"
            ]);
            return;
        }
        
        $id = intval($data['id']);
        
        // Check if profile exists
        if (!$profile->getRecordById($id)) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Profile not found"
            ]);
            return;
        }
        
        // Validate required fields
        if (empty($data['full_name']) || empty($data['email'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: full_name and email are required"
            ]);
            return;
        }
        
        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid email format"
            ]);
            return;
        }
        
        // Check if email already exists (excluding current profile)
        if ($profile->emailExists($data['email'], $id)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Email already exists"
            ]);
            return;
        }
        
        // Prepare update data
        $profileData = [
            'full_name' => trim($data['full_name']),
            'email' => trim($data['email']),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'bio' => $data['bio'] ?? null,
            'photo_url' => $data['photo_url'] ?? null,
            'linkedin_url' => $data['linkedin_url'] ?? null,
            'github_url' => $data['github_url'] ?? null,
            'website_url' => $data['website_url'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null
        ];
        
        // Update profile
        if ($profile->updateRecord($id, $profileData)) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Profile updated successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to update profile"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error updating profile: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle DELETE requests
 * Deletes a profile record
 */
function handleDelete($profile) {
    try {
        // Get ID from query parameter
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Profile ID is required"
            ]);
            return;
        }
        
        $id = intval($_GET['id']);
        
        // Check if profile exists
        if (!$profile->getRecordById($id)) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Profile not found"
            ]);
            return;
        }
        
        // Delete profile
        if ($profile->deleteRecord($id)) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Profile deleted successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to delete profile"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error deleting profile: " . $e->getMessage()
        ]);
    }
}
?>