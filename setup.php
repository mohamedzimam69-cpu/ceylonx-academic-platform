<?php
/**
 * CeylonX Setup Script for WAMP Server
 * Run this file once to set up the database and initial configuration
 */

// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>CeylonX Setup for WAMP Server</h1>";
echo "<p>Setting up your CeylonX application...</p>";

try {
    // Include database configuration
    require_once 'config/database.php';
    
    echo "<h2>✅ Database Configuration Loaded</h2>";
    
    // Create database instance
    $database = new Database();
    
    // Create database and tables
    if ($database->createDatabase()) {
        echo "<h2>✅ Database and Tables Created Successfully</h2>";
        
        // Get connection to verify
        $conn = $database->getConnection();
        
        if ($conn) {
            echo "<h2>✅ Database Connection Successful</h2>";
            
            // Check if tables exist
            $tables = ['assignments', 'contacts', 'admin_users'];
            echo "<h3>Checking Tables:</h3>";
            echo "<ul>";
            
            foreach ($tables as $table) {
                $stmt = $conn->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$table]);
                if ($stmt->rowCount() > 0) {
                    echo "<li>✅ Table '$table' exists</li>";
                } else {
                    echo "<li>❌ Table '$table' missing</li>";
                }
            }
            echo "</ul>";
            
            // Check admin user
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM admin_users WHERE username = 'admin'");
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                echo "<h3>✅ Default Admin User Created</h3>";
                echo "<p><strong>Username:</strong> admin</p>";
                echo "<p><strong>Password:</strong> admin123</p>";
            } else {
                echo "<h3>❌ Default Admin User Not Found</h3>";
            }
            
            // Test data insertion
            echo "<h3>Testing Data Operations:</h3>";
            
            // Test assignment insertion
            $test_stmt = $conn->prepare("INSERT INTO assignments (name, email, phone, subject, deadline, description, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $test_result = $test_stmt->execute([
                'Test User',
                'test@example.com',
                '+94771234567',
                'Test Subject',
                date('Y-m-d', strtotime('+7 days')),
                'This is a test assignment submission.',
                'pending'
            ]);
            
            if ($test_result) {
                echo "<p>✅ Test assignment created successfully</p>";
                
                // Clean up test data
                $cleanup_stmt = $conn->prepare("DELETE FROM assignments WHERE email = 'test@example.com'");
                $cleanup_stmt->execute();
                echo "<p>✅ Test data cleaned up</p>";
            } else {
                echo "<p>❌ Failed to create test assignment</p>";
            }
            
        } else {
            echo "<h2>❌ Database Connection Failed</h2>";
        }
        
    } else {
        echo "<h2>❌ Database Creation Failed</h2>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Setup Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>🚀 Setup Complete!</h2>";
echo "<p>Your CeylonX application is ready to use on WAMP server.</p>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Access your website: <a href='index.php' target='_blank'>http://localhost/your-folder-name/index.php</a></li>";
echo "<li>Access admin panel: <a href='admin/login.php' target='_blank'>http://localhost/your-folder-name/admin/login.php</a></li>";
echo "<li>Default admin credentials: <strong>admin / admin123</strong></li>";
echo "</ol>";

echo "<h3>File Structure Check:</h3>";
$required_files = [
    'index.php',
    'config/database.php',
    'admin/login.php',
    'admin/index.php',
    'admin/assignments.php',
    'admin/contacts.php',
    'submit_assignment.php',
    'contact.php',
    'assets/css/style.css',
    'assets/js/script.js'
];

echo "<ul>";
foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "<li>✅ $file</li>";
    } else {
        echo "<li>❌ $file (missing)</li>";
    }
}
echo "</ul>";

echo "<hr>";
echo "<p><strong>Note:</strong> Make sure your WAMP server is running and MySQL service is active.</p>";
echo "<p><strong>Tip:</strong> You can delete this setup.php file after successful setup.</p>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
}

h1, h2, h3 {
    color: #333;
}

ul, ol {
    background: white;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

p {
    background: white;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

hr {
    margin: 30px 0;
    border: none;
    border-top: 2px solid #ddd;
}
</style>