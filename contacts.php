<?php
/**
 * Contacts CRUD Controller / API Endpoint
 */

header('Content-Type: application/json');

session_start();

// Ensure user is authenticated
if (!isset($_SESSION['user_info'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized: Please log in first.']);
    exit();
}

$user = $_SESSION['user_info'];
$user_sub = $user['sub'] ?? '';

if (empty($user_sub)) {
    http_response_code(400);
    echo json_encode(['error' => 'Bad Request: Subject identifier (sub) missing from user session.']);
    exit();
}

require_once __DIR__ . '/db.php';
$db = get_db_connection();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        // Retrieve contacts for the current user, sorted alphabetically by name
        $stmt = $db->prepare("SELECT id, name, email, phone, company, job_title, created_at FROM contacts WHERE user_sub = ? ORDER BY name ASC");
        $stmt->execute([$user_sub]);
        $contacts = $stmt->fetchAll();
        
        echo json_encode($contacts);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal Server Error: ' . $e->getMessage()]);
    }
    exit();
}

if ($method === 'POST') {
    $action = $_GET['action'] ?? '';
    
    // Parse JSON body or form POST variables
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input === null) {
        $input = $_POST;
    }

    if ($action === 'create') {
        $name = isset($input['name']) ? trim($input['name']) : '';
        $email = isset($input['email']) ? trim($input['email']) : '';
        $phone = isset($input['phone']) ? trim($input['phone']) : '';
        $company = isset($input['company']) ? trim($input['company']) : '';
        $job_title = isset($input['job_title']) ? trim($input['job_title']) : '';

        // Validation
        if (empty($name)) {
            http_response_code(400);
            echo json_encode(['error' => 'Validation Error: Contact Name is required.']);
            exit();
        }

        try {
            $stmt = $db->prepare("INSERT INTO contacts (user_sub, name, email, phone, company, job_title) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_sub, $name, $email, $phone, $company, $job_title]);
            $new_id = $db->lastInsertId();

            echo json_encode([
                'success' => true,
                'message' => 'Contact added successfully.',
                'contact' => [
                    'id' => $new_id,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'company' => $company,
                    'job_title' => $job_title
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error: ' . $e->getMessage()]);
        }
        exit();
    }

    if ($action === 'update') {
        $id = isset($input['id']) ? intval($input['id']) : 0;
        $name = isset($input['name']) ? trim($input['name']) : '';
        $email = isset($input['email']) ? trim($input['email']) : '';
        $phone = isset($input['phone']) ? trim($input['phone']) : '';
        $company = isset($input['company']) ? trim($input['company']) : '';
        $job_title = isset($input['job_title']) ? trim($input['job_title']) : '';

        // Validation
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Validation Error: Invalid contact ID.']);
            exit();
        }
        if (empty($name)) {
            http_response_code(400);
            echo json_encode(['error' => 'Validation Error: Contact Name is required.']);
            exit();
        }

        try {
            // Check ownership
            $check = $db->prepare("SELECT id FROM contacts WHERE id = ? AND user_sub = ?");
            $check->execute([$id, $user_sub]);
            if (!$check->fetch()) {
                http_response_code(403);
                echo json_encode(['error' => 'Access Denied: Contact not found or does not belong to you.']);
                exit();
            }

            // Perform update
            $stmt = $db->prepare("UPDATE contacts SET name = ?, email = ?, phone = ?, company = ?, job_title = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_sub = ?");
            $stmt->execute([$name, $email, $phone, $company, $job_title, $id, $user_sub]);

            echo json_encode([
                'success' => true,
                'message' => 'Contact updated successfully.',
                'contact' => [
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'company' => $company,
                    'job_title' => $job_title
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error: ' . $e->getMessage()]);
        }
        exit();
    }

    if ($action === 'delete') {
        $id = isset($input['id']) ? intval($input['id']) : 0;

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Validation Error: Invalid contact ID.']);
            exit();
        }

        try {
            // Check ownership
            $check = $db->prepare("SELECT id FROM contacts WHERE id = ? AND user_sub = ?");
            $check->execute([$id, $user_sub]);
            if (!$check->fetch()) {
                http_response_code(403);
                echo json_encode(['error' => 'Access Denied: Contact not found or does not belong to you.']);
                exit();
            }

            // Perform delete
            $stmt = $db->prepare("DELETE FROM contacts WHERE id = ? AND user_sub = ?");
            $stmt->execute([$id, $user_sub]);

            echo json_encode([
                'success' => true,
                'message' => 'Contact deleted successfully.'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error: ' . $e->getMessage()]);
        }
        exit();
    }
}

http_response_code(405);
echo json_encode(['error' => 'Method Not Allowed']);
exit();
