<?php
// Set headers for JSON response and CORS
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