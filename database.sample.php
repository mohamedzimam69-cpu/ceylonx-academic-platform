<?php
// Database configuration - SAMPLE FILE
// Copy this file to database.php and update with your credentials

class Database {
    private $host = 'localhost';
    private $db_name = 'ceylonx_db';
    private $username = 'root';
    private $password = '';  // Enter your MySQL password here
    private $conn;
    
    // Get database connection
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
    
    // Create database and tables if they don't exist
    public function createDatabase() {
        try {
            // Connect without database name first
            $conn = new PDO(
                "mysql:host=" . $this->host,
                $this->username,
                $this->password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if it doesn't exist
            $sql = "CREATE DATABASE IF NOT EXISTS " . $this->db_name . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            $conn->exec($sql);
            
            // Use the database
            $conn->exec("USE " . $this->db_name);
            
            // Create assignments table
            $sql = "CREATE TABLE IF NOT EXISTS assignments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(191) NOT NULL,
                email VARCHAR(191) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                subject VARCHAR(191) NOT NULL,
                deadline DATE NOT NULL,
                description TEXT NOT NULL,
                submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_status (status),
                INDEX idx_submitted_at (submitted_at),
                INDEX idx_deadline (deadline),
                INDEX idx_email (email(191))
            )";
            $conn->exec($sql);
            
            // Create contacts table
            $sql = "CREATE TABLE IF NOT EXISTS contacts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(191) NOT NULL,
                email VARCHAR(191) NOT NULL,
                phone VARCHAR(20),
                message TEXT NOT NULL,
                submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_status (status),
                INDEX idx_submitted_at (submitted_at),
                INDEX idx_email (email(191))
            )";
            $conn->exec($sql);
            
            // Create admin users table
            $sql = "CREATE TABLE IF NOT EXISTS admin_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(191) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'moderator') DEFAULT 'admin',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_username (username),
                INDEX idx_email (email(191))
            )";
            $conn->exec($sql);
            
            // Insert default admin user (password: admin123)
            $default_password = password_hash('admin123', PASSWORD_DEFAULT);
            $sql = "INSERT IGNORE INTO admin_users (username, email, password, role) VALUES ('admin', 'admin@ceylonx.com', ?, 'admin')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$default_password]);
            
            return true;
            
        } catch(PDOException $exception) {
            echo "Database creation error: " . $exception->getMessage();
            return false;
        }
    }
}

// Initialize database
$database = new Database();
$database->createDatabase();
?>