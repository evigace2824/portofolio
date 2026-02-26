<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/includes/db.php';

// Only admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Gather & validate
        $first = trim($_POST['first_name'] ?? '');
        $last  = trim($_POST['last_name']  ?? '');
        $dob   = $_POST['date_of_birth']   ?? null;
        $phone = trim($_POST['phone']      ?? '');
        $addr  = trim($_POST['address']    ?? '');
        $role  = $_POST['role']            ?? '';
        $user  = trim($_POST['username']   ?? '');
        $email = trim($_POST['email']      ?? '');
        $pw    = $_POST['password']        ?? '';
        $pw2   = $_POST['confirm_password']?? '';

        if (!$first || !$last || !$user || !$email || !$pw || !$pw2) {
            throw new Exception('Please fill in all required fields.');
        }
        if ($pw !== $pw2) {
            throw new Exception('Passwords must match.');
        }
        if (!in_array($role, ['customer','admin'])) {
            throw new Exception('Invalid role.');
        }

        // Check uniqueness
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param('ss', $user, $email);
        $stmt->execute();
        $stmt->bind_result($cnt);
        $stmt->fetch();
        $stmt->close();
        if ($cnt > 0) {
            throw new Exception('Username or email already taken.');
        }

        // Hash password & insert
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            INSERT INTO users
              (first_name, last_name, date_of_birth, phone, address, role, username, email, password_hash, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param(
            'sssssssss',
            $first, $last, $dob, $phone, $addr, $role, $user, $email, $hash
        );
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $_SESSION['success'] = "User created successfully.";
        header('Location: admin.php?tab=users');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: add_user.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Add User – FitFlex</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FF6B6B;
            --primary-dark: #E05555;
            --accent: #4ECDC4;
            --dark: #292F36;
            --light: #F7FFF7;
            --text-dark: #292F36;
            --text-light: #6C757D;
            --gray-light: #f8f9fa;
            --gradient-primary: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
            --gradient-accent: linear-gradient(135deg, #4ECDC4 0%, #45B7D1 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        nav {
            height: 90px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 0 3rem;
            background-color: rgba(41, 47, 54, 0.9);
            backdrop-filter: blur(10px);
            color: var(--light);
            transition: all 0.3s ease;
        }

        nav.scrolled {
            height: 70px;
            background-color: rgba(41, 47, 54, 0.95);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            margin-right: auto;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .logo i {
            margin-right: 10px;
        }

        nav.scrolled .logo {
            font-size: 1.5rem;
        }

        /* Main Content */
        main {
            flex: 1;
            padding-top: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 100px 2rem 2rem;
        }

        /* Form Container */
        .form-container {
            background: white;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 600px;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: var(--gradient-accent);
            border-radius: 50%;
            opacity: 0.1;
            z-index: 0;
        }

        .form-container::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 300px;
            height: 300px;
            background: var(--gradient-primary);
            border-radius: 50%;
            opacity: 0.1;
            z-index: 0;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
            z-index: 1;
        }

        .form-header h1 {
            font-size: 2.2rem;
            color: var(--dark);
            margin-bottom: 1rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            position: relative;
            display: inline-block;
        }

        .form-header h1::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 6px;
            background: var(--gradient-primary);
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 5px;
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 12px;
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background-color: #ffebee;
            color: var(--primary-dark);
            border-left: 4px solid var(--primary-dark);
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark);
            font-weight: 600;
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="date"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 0.9rem 1.2rem;
            border: none;
            border-radius: 12px;
            transition: all 0.3s;
            font-size: 0.9rem;
            background-color: var(--gray-light);
        }

        input:focus,
        select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(78, 205, 196, 0.2);
        }

        /* Buttons */
        .btn {
            background: var(--gradient-primary);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            font-size: 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        }

        .btn i {
            margin-right: 8px;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            nav {
                padding: 0 1.5rem;
            }

            .form-container {
                padding: 2rem;
            }

            .form-header h1 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            .form-container {
                padding: 1.5rem;
            }

            .form-header h1 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav>
    <a href="admin.php" class="logo">FitFlex</a>
</nav>

<main>
    <div class="form-container">
        <div class="form-header">
            <h1><i class="fas fa-user-plus"></i> Add New User</h1>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>

            <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth">
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address">
            </div>

            <div class="form-group">
                <label for="role">Role *</label>
                <select id="role" name="role" required>
                    <option value="">Select role</option>
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn"><i class="fas fa-check"></i> Create User</button>
        </form>
    </div>
</main>

<script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const nav = document.querySelector('nav');
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });
</script>
</body>
</html>