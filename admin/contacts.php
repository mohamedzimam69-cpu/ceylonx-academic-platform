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

// Handle status updates
if ($_POST && isset($_POST['update_status'])) {
    $contact_id = $_POST['contact_id'];
    $new_status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE contacts SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$new_status, $contact_id]);
    
    $success_message = "Contact status updated successfully!";
}

// Get all contacts
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$query = "SELECT * FROM contacts WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR message LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
}

if ($status_filter) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY submitted_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$contacts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts - CeylonX Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-container {
            max-width: 1400px;
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
        
        .admin-nav a:hover, .admin-nav a.active {
            background: rgba(0, 212, 255, 0.2);
        }
        
        .filters {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filters input, .filters select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            padding: 0.5rem 1rem;
            color: #fff;
        }
        
        .contacts-table {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
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
            background: rgba(0, 212, 255, 0.1);
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
        
        .status-new {
            background: rgba(0, 212, 255, 0.2);
            color: #00d4ff;
        }
        
        .status-read {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }
        
        .status-replied {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        
        .status-closed {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
        }
        
        .action-btn {
            background: rgba(0, 212, 255, 0.2);
            color: #00d4ff;
            border: 1px solid rgba(0, 212, 255, 0.3);
            padding: 0.3rem 0.8rem;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.8rem;
            margin: 0 0.2rem;
            cursor: pointer;
        }
        
        .action-btn:hover {
            background: rgba(0, 212, 255, 0.3);
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
        }
        
        .modal-content {
            background: #222;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            color: #fff;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #fff;
        }
        
        .success-message {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div>
                <h1 style="color: #fff;">Contact Management</h1>
                <p style="color: #ccc;">Manage and respond to contact messages</p>
            </div>
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>
        
        <div class="admin-nav">
            <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="assignments.php"><i class="fas fa-tasks"></i> Assignments</a>
            <a href="contacts.php" class="active"><i class="fas fa-envelope"></i> Contacts</a>
            <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <div class="filters">
            <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                <input type="text" name="search" placeholder="Search contacts..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="status">
                    <option value="">All Status</option>
                    <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                    <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Read</option>
                    <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Replied</option>
                    <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Closed</option>
                </select>
                <button type="submit" class="action-btn">Filter</button>
                <a href="contacts.php" class="action-btn">Clear</a>
            </form>
        </div>
        
        <div class="contacts-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Message Preview</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td>#<?php echo $contact['id']; ?></td>
                        <td><?php echo htmlspecialchars($contact['name']); ?></td>
                        <td><?php echo htmlspecialchars($contact['email']); ?></td>
                        <td><?php echo htmlspecialchars($contact['phone'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars(substr($contact['message'], 0, 50)) . '...'; ?></td>
                        <td><span class="status-badge status-<?php echo $contact['status']; ?>"><?php echo ucfirst($contact['status']); ?></span></td>
                        <td><?php echo date('M d, Y H:i', strtotime($contact['submitted_at'])); ?></td>
                        <td>
                            <button class="action-btn" onclick="viewContact(<?php echo $contact['id']; ?>)">View</button>
                            <button class="action-btn" onclick="updateStatus(<?php echo $contact['id']; ?>)">Update</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- View Contact Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('viewModal')">&times;</span>
            <h2>Contact Details</h2>
            <div id="contactDetails"></div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('updateModal')">&times;</span>
            <h2>Update Contact Status</h2>
            <form method="POST">
                <input type="hidden" id="updateContactId" name="contact_id">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem;">Status:</label>
                    <select name="status" id="updateStatus" style="width: 100%; padding: 0.5rem; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 5px; color: #fff;">
                        <option value="new">New</option>
                        <option value="read">Read</option>
                        <option value="replied">Replied</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
            </form>
        </div>
    </div>

    <script>
        const contacts = <?php echo json_encode($contacts); ?>;
        
        function viewContact(id) {
            const contact = contacts.find(c => c.id == id);
            if (contact) {
                document.getElementById('contactDetails').innerHTML = `
                    <p><strong>Name:</strong> ${contact.name}</p>
                    <p><strong>Email:</strong> ${contact.email}</p>
                    <p><strong>Phone:</strong> ${contact.phone || 'N/A'}</p>
                    <p><strong>Status:</strong> ${contact.status}</p>
                    <p><strong>Submitted:</strong> ${new Date(contact.submitted_at).toLocaleString()}</p>
                    <p><strong>Message:</strong></p>
                    <div style="background: rgba(255,255,255,0.1); padding: 1rem; border-radius: 5px; margin-top: 0.5rem; white-space: pre-wrap;">
                        ${contact.message}
                    </div>
                `;
                document.getElementById('viewModal').style.display = 'block';
            }
        }
        
        function updateStatus(id) {
            const contact = contacts.find(c => c.id == id);
            if (contact) {
                document.getElementById('updateContactId').value = id;
                document.getElementById('updateStatus').value = contact.status;
                document.getElementById('updateModal').style.display = 'block';
            }
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>