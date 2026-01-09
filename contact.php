<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ceylonx_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    }
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit();
    }
    
    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, message, submitted_at, status) VALUES (?, ?, ?, ?, NOW(), 'new')");
    
    if ($stmt) {
        $stmt->bind_param("ssss", $name, $email, $phone, $message);
        
        if ($stmt->execute()) {
            $contact_id = $conn->insert_id;
            
            // Send email notification (optional)
            $to = "support@ceylonx.com";
            $email_subject = "New Contact Message - ID: " . $contact_id;
            $email_message = "
            New contact message received:
            
            ID: $contact_id
            Name: $name
            Email: $email
            Phone: $phone
            
            Message:
            $message
            
            Submitted at: " . date('Y-m-d H:i:s') . "
            ";
            
            $headers = "From: noreply@ceylonx.com\r\n";
            $headers .= "Reply-To: $email\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            // Uncomment the line below to enable email notifications
            // mail($to, $email_subject, $email_message, $headers);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Message sent successfully!',
                'contact_id' => $contact_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send message']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>