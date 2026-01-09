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
    $subject = trim($_POST['subject'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    }
    
    if (empty($subject)) {
        $errors[] = 'Subject/Module is required';
    }
    
    if (empty($deadline)) {
        $errors[] = 'Deadline is required';
    }
    
    if (empty($description)) {
        $errors[] = 'Assignment details are required';
    }
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit();
    }
    
    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO assignments (name, email, phone, subject, deadline, description, submitted_at, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'pending')");
    
    if ($stmt) {
        $stmt->bind_param("ssssss", $name, $email, $phone, $subject, $deadline, $description);
        
        if ($stmt->execute()) {
            $assignment_id = $conn->insert_id;
            
            // Send email notification (optional)
            $to = "support@ceylonx.com";
            $email_subject = "New Assignment Submission - ID: " . $assignment_id;
            $message = "
            New assignment submission received:
            
            ID: $assignment_id
            Name: $name
            Email: $email
            Phone: $phone
            Subject: $subject
            Deadline: $deadline
            
            Description:
            $description
            
            Submitted at: " . date('Y-m-d H:i:s') . "
            ";
            
            $headers = "From: noreply@ceylonx.com\r\n";
            $headers .= "Reply-To: $email\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            // Uncomment the line below to enable email notifications
            // mail($to, $email_subject, $message, $headers);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Assignment submitted successfully!',
                'assignment_id' => $assignment_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit assignment']);
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