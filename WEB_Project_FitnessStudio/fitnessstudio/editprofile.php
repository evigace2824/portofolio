<?php
session_start();
require_once __DIR__ . '/includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Get user data
$stmt = $conn->prepare("
    SELECT user_id, username, first_name, last_name, email, 
           phone, date_of_birth, address, role
    FROM users 
    WHERE user_id = ?
");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $dateOfBirth = $_POST['date_of_birth'] ?? null;
    $address = trim($_POST['address'] ?? '');

    $errors = [];
    if (empty($firstName)) $errors[] = "First name is required";
    if (empty($lastName)) $errors[] = "Last name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";

    if (empty($errors)) {
        // Update user in db
        $stmt = $conn->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, email = ?, 
                phone = ?, date_of_birth = ?, address = ?,
                updated_at = NOW()
            WHERE user_id = ?
        ");
        $stmt->bind_param(
            'ssssssi',
            $firstName, $lastName, $email,
            $phone, $dateOfBirth, $address,
            $userId
        );

        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            // Refresh user data
            $user['first_name'] = $firstName;
            $user['last_name'] = $lastName;
            $user['email'] = $email;
            $user['phone'] = $phone;
            $user['date_of_birth'] = $dateOfBirth;
            $user['address'] = $address;
        } else {
            $errors[] = "Error updating profile: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - FitFlex</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #FF6B6B;
            --primary-dark: #E05555;
            --accent: #4ECDC4;
            --dark: #292F36;
            --light: #F7FFF7;
            --text-dark: #292F36;
            --text-light: #6C757D;
            --gray-light: #F8F9FA;
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
            overflow-x: hidden;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--gray-light);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
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
        }

        nav.scrolled .logo {
            font-size: 1.5rem;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            margin-right: 2rem;
        }

        .nav-links a {
            color: var(--light);
            text-decoration: none;
            font-weight: 500;
            position: relative;
            padding: 0.5rem 0;
            transition: all 0.3s ease;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--accent);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .profile {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: var(--light);
            margin-left: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            background-size: cover;
            background-position: center;
        }

        .profile:hover {
            transform: scale(1.1);
            border-color: var(--accent);
        }

        /* Main Content */
        .main-content {
            max-width: 800px;
            margin: 6rem auto 3rem;
            padding: 0 1rem;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .form-header h1 {
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            font-size: 2.5rem;
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

        .form-header p {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        /* Form Styles */
        .form-container {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 15px 40px rgba(0,0,0,0.05);
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

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: var(--gray-light);
            border: none;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 0 3px rgba(78, 205, 196, 0.2);
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
        }

        /* Button Styles */
        .btn {
            padding: 0.8rem 1.8rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        }

        .btn-outline {
            background: transparent;
            color: var(--dark);
            border: 2px solid var(--dark);
        }

        .btn-outline:hover {
            background: rgba(41, 47, 54, 0.05);
            transform: translateY(-3px);
        }

        /* Alert Styles */
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 1;
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

        .alert i {
            font-size: 1.2rem;
        }

        /* Special Form Elements */
        .disabled-field {
            background-color: var(--gray-light);
            color: var(--text-light);
            cursor: not-allowed;
            border: none;
        }

        .form-note {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-top: 0.5rem;
            font-style: italic;
        }

        /* Role Badge */
        .role-badge {
            background: var(--gradient-accent);
            color: var(--dark);
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .main-content {
                margin-top: 5rem;
                padding: 0 1rem;
            }

            .form-header h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 576px) {
            .form-header h1 {
                font-size: 1.8rem;
            }

            .form-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav>
    <a href="mainpage.php" class="logo">FitFlex</a>
    <div class="nav-links">
        <a href="mainpage.php">Home</a>
        <a href="explore.php">Classes</a>
    </div>
    <div class="user-profile" style="display: flex; align-items: center; gap: 1rem;">
        <span style="color: white; font-weight: 500;"><?= htmlspecialchars($user['username']) ?></span>
        <?php if ($user['role'] === 'admin'): ?>
            <span class="role-badge">Admin</span>
        <?php endif; ?>
        <div class="profile" onclick="window.location.href='userpage.php'" style="background-image: url('assets/images/profileicon.png');"></div>
    </div>
</nav>

<main class="main-content">
    <div class="form-header">
        <h1>Edit Your Profile</h1>
        <p>Update your personal fitness information</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <p><?= htmlspecialchars($success) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="form-container">
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" class="disabled-field"
                           value="<?= htmlspecialchars($user['username']) ?>"
                           disabled>
                    <p class="form-note">Username cannot be changed</p>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name"
                           value="<?= htmlspecialchars($user['first_name']) ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name"
                           value="<?= htmlspecialchars($user['last_name']) ?>"
                           required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($user['email']) ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone"
                           value="<?= htmlspecialchars($user['phone']) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth"
                           value="<?= htmlspecialchars($user['date_of_birth']) ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3"><?=
                    htmlspecialchars($user['address'])
                    ?></textarea>
            </div>

            <div class="form-actions">
                <a href="userpage.php" class="btn btn-outline">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
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