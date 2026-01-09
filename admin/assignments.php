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
    $assignment_id = $_POST['assignment_id'];
    $new_status = $_POST['status'];
    $notes = $_POST['notes'] ?? '';
    
    $stmt = $conn->prepare("UPDATE assignments SET status = ?, notes = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$new_status, $notes, $assignment_id]);
    
    $success_message = "Assignment status updated successfully!";
}

// Get all assignments
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$query = "SELECT * FROM assignments WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ?)";
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
$assignments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - CeylonX Admin</title>
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
        
        .assignments-table {
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
        
        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }
        
        .status-in_progress {
            background: rgba(0, 123, 255, 0.2);
            color: #007bff;
        }
        
        .status-completed {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        
        .status-cancelled {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
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
                <h1 style="color: #fff;">Assignment Management</h1>
                <p style="color: #ccc;">Manage and track assignment submissions</p>
            </div>
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>
        
        <div class="admin-nav">
            <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="assignments.php" class="active"><i class="fas fa-tasks"></i> Assignments</a>
            <a href="contacts.php"><i class="fas fa-envelope"></i> Contacts</a>
            <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <div class="filters">
            <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                <input type="text" name="search" placeholder="Search assignments..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="status">
                    <option value="">All Status</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo $status_filter === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <button type="submit" class="action-btn">Filter</button>
                <a href="assignments.php" class="action-btn">Clear</a>
            </form>
        </div>
        
        <div class="assignments-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignments as $assignment): ?>
                    <tr>
                        <td>#<?php echo $assignment['id']; ?></td>
                        <td><?php echo htmlspecialchars($assignment['name']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['email']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['subject']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($assignment['deadline'])); ?></td>
                        <td><span class="status-badge status-<?php echo $assignment['status']; ?>"><?php echo ucfirst(str_replace('_', ' ', $assignment['status'])); ?></span></td>
                        <td><?php echo date('M d, Y H:i', strtotime($assignment['submitted_at'])); ?></td>
                        <td>
                            <button class="action-btn" onclick="viewAssignment(<?php echo $assignment['id']; ?>)">View</button>
                            <button class="action-btn" onclick="updateStatus(<?php echo $assignment['id']; ?>)">Update</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- View Assignment Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('viewModal')">&times;</span>
            <h2>Assignment Details</h2>
            <div id="assignmentDetails"></div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('updateModal')">&times;</span>
            <h2>Update Assignment Status</h2>
            <form method="POST">
                <input type="hidden" id="updateAssignmentId" name="assignment_id">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem;">Status:</label>
                    <select name="status" id="updateStatus" style="width: 100%; padding: 0.5rem; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 5px; color: #fff;">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem;">Notes:</label>
                    <textarea name="notes" id="updateNotes" rows="4" style="width: 100%; padding: 0.5rem; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 5px; color: #fff;"></textarea>
                </div>
                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
            </form>
        </div>
    </div>

    <script>
        const assignments = <?php echo json_encode($assignments); ?>;
        
        function viewAssignment(id) {
            const assignment = assignments.find(a => a.id == id);
            if (assignment) {
                document.getElementById('assignmentDetails').innerHTML = `
                    <p><strong>Name:</strong> ${assignment.name}</p>
                    <p><strong>Email:</strong> ${assignment.email}</p>
                    <p><strong>Phone:</strong> ${assignment.phone}</p>
                    <p><strong>Subject:</strong> ${assignment.subject}</p>
                    <p><strong>Deadline:</strong> ${new Date(assignment.deadline).toLocaleDateString()}</p>
                    <p><strong>Status:</strong> ${assignment.status.replace('_', ' ')}</p>
                    <p><strong>Description:</strong></p>
                    <div style="background: rgba(255,255,255,0.1); padding: 1rem; border-radius: 5px; margin-top: 0.5rem;">
                        ${assignment.description}
                    </div>
                    ${assignment.notes ? `<p><strong>Notes:</strong></p><div style="background: rgba(255,255,255,0.1); padding: 1rem; border-radius: 5px; margin-top: 0.5rem;">${assignment.notes}</div>` : ''}
                `;
                document.getElementById('viewModal').style.display = 'block';
            }
        }
        
        function updateStatus(id) {
            const assignment = assignments.find(a => a.id == id);
            if (assignment) {
                document.getElementById('updateAssignmentId').value = id;
                document.getElementById('updateStatus').value = assignment.status;
                document.getElementById('updateNotes').value = assignment.notes || '';
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