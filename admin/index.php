<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

// Get statistics
$stats = [];

// Total assignments
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM assignments");
$stmt->execute();
$stats['total_assignments'] = $stmt->fetch()['total'];

// Pending assignments
$stmt = $conn->prepare("SELECT COUNT(*) as pending FROM assignments WHERE status = 'pending'");
$stmt->execute();
$stats['pending_assignments'] = $stmt->fetch()['pending'];

// Total contacts
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM contacts");
$stmt->execute();
$stats['total_contacts'] = $stmt->fetch()['total'];

// New contacts
$stmt = $conn->prepare("SELECT COUNT(*) as new FROM contacts WHERE status = 'new'");
$stmt->execute();
$stats['new_contacts'] = $stmt->fetch()['new'];

// Recent assignments
$stmt = $conn->prepare("SELECT * FROM assignments ORDER BY submitted_at DESC LIMIT 5");
$stmt->execute();
$recent_assignments = $stmt->fetchAll();

// Recent contacts
$stmt = $conn->prepare("SELECT * FROM contacts ORDER BY submitted_at DESC LIMIT 5");
$stmt->execute();
$recent_contacts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CeylonX Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            background: #111;
            min-height: 100vh;
        }
        
        .admin-header {
            background: rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-nav {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .admin-nav a {
            background: rgba(0, 212, 255, 0.1);
            color: #00d4ff;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid rgba(0, 212, 255, 0.3);
            transition: all 0.3s ease;
        }
        
        .admin-nav a:hover {
            background: rgba(0, 212, 255, 0.2);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        
        .stat-card i {
            font-size: 2.5rem;
            color: #00d4ff;
            margin-bottom: 1rem;
        }
        
        .stat-card h3 {
            font-size: 2rem;
            color: #fff;
            margin-bottom: 0.5rem;
        }
        
        .stat-card p {
            color: #ccc;
        }
        
        .recent-section {
            background: rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 2rem;
        }
        
        .recent-section h3 {
            color: #fff;
            margin-bottom: 1rem;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .table th {
            color: #00d4ff;
            font-weight: 600;
        }
        
        .table td {
            color: #ccc;
        }
        
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }
        
        .status-new {
            background: rgba(0, 212, 255, 0.2);
            color: #00d4ff;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div>
                <h1 style="color: #fff;">CeylonX Admin Dashboard</h1>
                <p style="color: #ccc;">Welcome, <?php echo $_SESSION['admin_username']; ?>!</p>
            </div>
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>
        
        <div class="admin-nav">
            <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="assignments.php"><i class="fas fa-tasks"></i> Assignments</a>
            <a href="contacts.php"><i class="fas fa-envelope"></i> Contacts</a>
            <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-tasks"></i>
                <h3><?php echo $stats['total_assignments']; ?></h3>
                <p>Total Assignments</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3><?php echo $stats['pending_assignments']; ?></h3>
                <p>Pending Assignments</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-envelope"></i>
                <h3><?php echo $stats['total_contacts']; ?></h3>
                <p>Total Contacts</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-bell"></i>
                <h3><?php echo $stats['new_contacts']; ?></h3>
                <p>New Messages</p>
            </div>
        </div>
        
        <div class="recent-section">
            <h3>Recent Assignments</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Subject</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_assignments as $assignment): ?>
                    <tr>
                        <td>#<?php echo $assignment['id']; ?></td>
                        <td><?php echo htmlspecialchars($assignment['name']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['subject']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($assignment['deadline'])); ?></td>
                        <td><span class="status-badge status-<?php echo $assignment['status']; ?>"><?php echo ucfirst($assignment['status']); ?></span></td>
                        <td><?php echo date('M d, Y H:i', strtotime($assignment['submitted_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="recent-section">
            <h3>Recent Contact Messages</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_contacts as $contact): ?>
                    <tr>
                        <td>#<?php echo $contact['id']; ?></td>
                        <td><?php echo htmlspecialchars($contact['name']); ?></td>
                        <td><?php echo htmlspecialchars($contact['email']); ?></td>
                        <td><?php echo htmlspecialchars(substr($contact['message'], 0, 50)) . '...'; ?></td>
                        <td><span class="status-badge status-<?php echo $contact['status']; ?>"><?php echo ucfirst($contact['status']); ?></span></td>
                        <td><?php echo date('M d, Y H:i', strtotime($contact['submitted_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>