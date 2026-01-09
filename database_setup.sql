-- CeylonX Database Setup
-- Run this SQL script to create the database and tables

-- Create database
CREATE DATABASE IF NOT EXISTS ceylonx_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE ceylonx_db;

-- Create assignments table
CREATE TABLE IF NOT EXISTS assignments (
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
);

-- Create contacts table
CREATE TABLE IF NOT EXISTS contacts (
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
);

-- Create admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(191) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'moderator') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email(191))
);

-- Insert default admin user
-- Username: admin
-- Password: admin123
INSERT INTO admin_users (username, email, password, role) VALUES 
('admin', 'admin@ceylonx.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE username = username;

-- Create sample data (optional)
INSERT INTO assignments (name, email, phone, subject, deadline, description, status) VALUES
('John Doe', 'john@example.com', '+94771234567', 'Web Development', '2026-02-15', 'Need help with PHP and MySQL project', 'pending'),
('Jane Smith', 'jane@example.com', '+94777654321', 'Mobile App Development', '2026-02-20', 'Android app development guidance needed', 'in_progress'),
('Mike Johnson', 'mike@example.com', '+94771111111', 'Database Design', '2026-02-10', 'SQL database optimization project', 'completed')
ON DUPLICATE KEY UPDATE name = name;

INSERT INTO contacts (name, email, phone, message, status) VALUES
('Sarah Wilson', 'sarah@example.com', '+94772222222', 'Interested in your services for final year project', 'new'),
('David Brown', 'david@example.com', '+94773333333', 'Can you help with React.js development?', 'read'),
('Lisa Davis', 'lisa@example.com', '+94774444444', 'Need assistance with machine learning project', 'replied')
ON DUPLICATE KEY UPDATE name = name;

-- Show tables
SHOW TABLES;

-- Show table structures
DESCRIBE assignments;
DESCRIBE contacts;
DESCRIBE admin_users;