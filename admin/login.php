<?php
session_start();
require_once '../config/database.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (!empty($username) && !empty($password)) {
        $database = new Database();
        $conn = $database->getConnection();
        
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM admin_users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_role'] = $user['role'];
            
            header('Location: index.php');
            exit();
        } else {
            $error_message = 'Invalid username or password';
        }
    } else {
        $error_message = 'Please fill in all fields';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CeylonX Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
        }
        
        .login-form {
            background: rgba(255, 255, 255, 0.05);
            padding: 3rem;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            color: #fff;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #ccc;
            font-size: 0.9rem;
        }
        
        .error-message {
            background: rgba(255, 0, 0, 0.1);
            color: #ff6b6b;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid rgba(255, 0, 0, 0.3);
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            color: #fff;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #00d4ff;
            background: rgba(255, 255, 255, 0.15);
        }
        
        .login-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #00d4ff, #0099cc);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
        }
        
        .back-link {
            text-align: center;
            margin-top: 2rem;
        }
        
        .back-link a {
            color: #00d4ff;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .default-credentials {
            background: rgba(0, 212, 255, 0.1);
            color: #00d4ff;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid rgba(0, 212, 255, 0.3);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <form class="login-form" method="POST">
            <div class="login-header">
                <h1>CeylonX Admin</h1>
                <p>Next Gen Digital Solutions</p>
            </div>
            
            <div class="default-credentials">
                <strong>Default Login:</strong><br>
                Username: admin<br>
                Password: admin123
            </div>
            
            <?php if ($error_message): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
            
            <div class="back-link">
                <a href="../index.php">
                    <i class="fas fa-arrow-left"></i> Back to Website
                </a>
            </div>
        </form>
    </div>
</body>
</html>