<?php
/**
 * Contacts API Endpoint
 * 
 * Handles HTTP requests for contacts CRUD operations
 * Methods: GET, POST, PUT, DELETE
 * Returns: JSON responses
 * 
 * @author Portfolio Project - WMSU CCS
 * @version 1.0
 * 
 * POSTMAN TESTING EXAMPLES:
 * 
 * 1. GET All Contacts
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/contacts_api.php
 * 
 * 2. GET Single Contact
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/contacts_api.php?id=1
 * 
 * 3. GET Contacts by Status
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/contacts_api.php?action=by_status&status=New
 * 
 * 4. GET Recent Contacts (last 30 days)
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/contacts_api.php?action=recent&days=30
 * 
 * 5. GET Contact Statistics
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/contacts_api.php?action=statistics
 * 
 * 6. GET Search Contacts
 *    Method: GET
 *    URL: http://localhost/portfolio_project/api/contacts_api.php?action=search&keyword=maria
 * 
 * 7. POST Create Contact
 *    Method: POST
 *    URL: http://localhost/portfolio_project/api/contacts_api.php
 *    Body (raw JSON):
 *    {
 *        "name": "Pedro Santos",
 *        "email": "pedro.santos@email.com",
 *        "subject": "Freelance Inquiry",
 *        "message": "I would like to hire you for a project",
 *        "status": "New",
 *        "ip_address": "192.168.1.100"
 *    }
 * 
 * 8. PUT Update Contact
 *    Method: PUT
 *    URL: http://localhost/portfolio_project/api/contacts_api.php
 *    Body (raw JSON):
 *    {
 *        "id": 1,
 *        "name": "Pedro Santos Updated",
 *        "email": "pedro.updated@email.com",
 *        "subject": "Updated Subject",
 *        "message": "Updated message",
 *        "status": "Read"
 *    }
 * 
 * 9. PUT Update Contact Status Only
 *    Method: PUT
 *    URL: http://localhost/portfolio_project/api/contacts_api.php?action=update_status
 *    Body (raw JSON):
 *    {
 *        "id": 1,
 *        "status": "Replied"
 *    }
 * 
 * 10. DELETE Contact
 *     Method: DELETE
 *     URL: http://localhost/portfolio_project/api/contacts_api.php?id=1
 */

// Set headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include required files
require_once 'database.php';
require_once '../class/Contact.php';

// Instantiate database and contact object
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

$contact = new Contact($db);

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Handle different HTTP methods
switch ($method) {
    case 'GET':
        handleGet($contact);
        break;
    
    case 'POST':
        handlePost($contact);
        break;
    
    case 'PUT':
        handlePut($contact);
        break;
    
    case 'DELETE':
        handleDelete($contact);
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
function handleGet($contact) {
    try {
        // Check for special actions
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'by_status':
                    if (!isset($_GET['status'])) {
                        http_response_code(400);
                        echo json_encode([
                            "status" => "error",
                            "message" => "Status parameter is required"
                        ]);
                        return;
                    }
                    $result = $contact->getContactsByStatus($_GET['status']);
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Contacts by status retrieved successfully",
                        "count" => count($result),
                        "data" => $result
                    ]);
                    return;
                
                case 'recent':
                    $days = isset($_GET['days']) ? intval($_GET['days']) : 30;
                    $result = $contact->getRecentContacts($days);
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Recent contacts retrieved successfully",
                        "count" => count($result),
                        "data" => $result
                    ]);
                    return;
                
                case 'statistics':
                    $result = $contact->getContactStatistics();
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Contact statistics retrieved successfully",
                        "data" => $result
                    ]);
                    return;
                
                case 'search':
                    if (!isset($_GET['keyword'])) {
                        http_response_code(400);
                        echo json_encode([
                            "status" => "error",
                            "message" => "Keyword parameter is required for search"
                        ]);
                        return;
                    }
                    $result = $contact->searchContacts($_GET['keyword']);
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Search results retrieved successfully",
                        "count" => count($result),
                        "data" => $result
                    ]);
                    return;
                
                case 'by_date':
                    $result = $contact->getContactsByDate();
                    http_response_code(200);
                    echo json_encode([
                        "status" => "success",
                        "message" => "Contacts by date retrieved successfully",
                        "data" => $result
                    ]);
                    return;
            }
        }
        
        // Get single contact by ID
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $result = $contact->getRecordById($id);
            
            if ($result) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Contact retrieved successfully",
                    "data" => $result
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "status" => "error",
                    "message" => "Contact not found"
                ]);
            }
        } else {
            // Get all contacts
            $result = $contact->getRecords();
            
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Contacts retrieved successfully",
                "count" => count($result),
                "data" => $result
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error retrieving contacts: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle POST requests
 */
function handlePost($contact) {
    try {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Validate required fields
        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: name, email, and message are required"
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
        
        // Validate status
        $validStatuses = ['New', 'Read', 'Replied', 'Archived'];
        $status = $data['status'] ?? 'New';
        if (!in_array($status, $validStatuses)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid status. Must be: New, Read, Replied, or Archived"
            ]);
            return;
        }
        
        // Get IP address if not provided
        $ip_address = $data['ip_address'] ?? $_SERVER['REMOTE_ADDR'];
        
        // Prepare contact data
        $contactData = [
            'name' => trim($data['name']),
            'email' => trim($data['email']),
            'subject' => $data['subject'] ?? null,
            'message' => trim($data['message']),
            'status' => $status,
            'ip_address' => $ip_address
        ];
        
        // Create contact
        if ($contact->addRecord($contactData)) {
            http_response_code(201);
            echo json_encode([
                "status" => "success",
                "message" => "Contact created successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to create contact"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error creating contact: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle PUT requests
 */
function handlePut($contact) {
    try {
        // Get posted data
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Check if updating status only
        if (isset($_GET['action']) && $_GET['action'] === 'update_status') {
            if (empty($data['id']) || empty($data['status'])) {
                http_response_code(400);
                echo json_encode([
                    "status" => "error",
                    "message" => "Contact ID and status are required"
                ]);
                return;
            }
            
            $id = intval($data['id']);
            
            // Check if contact exists
            if (!$contact->getRecordById($id)) {
                http_response_code(404);
                echo json_encode([
                    "status" => "error",
                    "message" => "Contact not found"
                ]);
                return;
            }
            
            // Validate status
            $validStatuses = ['New', 'Read', 'Replied', 'Archived'];
            if (!in_array($data['status'], $validStatuses)) {
                http_response_code(400);
                echo json_encode([
                    "status" => "error",
                    "message" => "Invalid status. Must be: New, Read, Replied, or Archived"
                ]);
                return;
            }
            
            // Update status
            if ($contact->updateStatus($id, $data['status'])) {
                http_response_code(200);
                echo json_encode([
                    "status" => "success",
                    "message" => "Contact status updated successfully"
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to update contact status"
                ]);
            }
            return;
        }
        
        // Full update
        // Validate ID
        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Contact ID is required"
            ]);
            return;
        }
        
        $id = intval($data['id']);
        
        // Check if contact exists
        if (!$contact->getRecordById($id)) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Contact not found"
            ]);
            return;
        }
        
        // Validate required fields
        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields: name, email, and message are required"
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
        
        // Validate status
        $validStatuses = ['New', 'Read', 'Replied', 'Archived'];
        $status = $data['status'] ?? 'New';
        if (!in_array($status, $validStatuses)) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Invalid status. Must be: New, Read, Replied, or Archived"
            ]);
            return;
        }
        
        // Prepare update data
        $contactData = [
            'name' => trim($data['name']),
            'email' => trim($data['email']),
            'subject' => $data['subject'] ?? null,
            'message' => trim($data['message']),
            'status' => $status
        ];
        
        // Update contact
        if ($contact->updateRecord($id, $contactData)) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Contact updated successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to update contact"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error updating contact: " . $e->getMessage()
        ]);
    }
}

/**
 * Handle DELETE requests
 */
function handleDelete($contact) {
    try {
        // Get ID from query parameter
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Contact ID is required"
            ]);
            return;
        }
        
        $id = intval($_GET['id']);
        
        // Check if contact exists
        if (!$contact->getRecordById($id)) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Contact not found"
            ]);
            return;
        }
        
        // Delete contact
        if ($contact->deleteRecord($id)) {
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Contact deleted successfully"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Failed to delete contact"
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Error deleting contact: " . $e->getMessage()
        ]);
    }
}
?>