<?php
session_start();
require_once __DIR__ . '/includes/db.php';  // Database connection

// User must be logged in and be an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$adminId = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get admin's details
$admin = [];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param('i', $adminId);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get all classes for management
$classes = [];
$stmt = $conn->prepare("SELECT * FROM classes ORDER BY created_at DESC");
$stmt->execute();
$classes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$counter = 1;

// Get all trainers
$trainers = [];
$stmt = $conn->prepare("SELECT * FROM trainers ORDER BY created_at DESC");
$stmt->execute();
$trainers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$trainerCounter = 1;

// Get all users for management
$users = [];
$stmt = $conn->prepare("SELECT user_id, username, email, role, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$userCounter = 1;

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FitFlex</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
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

        .badge-admin {
            background: var(--gradient-accent);
            color: var(--dark);
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            margin-left: 1rem;
        }

        /* Main Content */
        .main-content {
            margin-top: 90px;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 2px solid var(--gray-light);
            margin-bottom: 2rem;
        }

        .tab {
            padding: 1rem 2rem;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-light);
            position: relative;
            transition: all 0.3s ease;
        }

        .tab.active {
            color: var(--primary-dark);
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--gradient-primary);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .tab-content.active {
            display: block;
        }
        /* center‐static banner */
        .center-static {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
        }

        .static-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            padding: 2rem;
            max-width: 600px;
            text-align: center;
        }

        .static-card h3 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.75rem;
            color: var(--primary-dark);
            margin-bottom: 0.5rem;
        }

        .static-card .quote {
            font-size: 1rem;
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        .static-img {
            width: 100%;
            border-radius: 12px;
            object-fit: cover;
            max-height: 250px;
        }


        /* Admin Cards */
        .admin-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        .admin-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .admin-card-title {
            font-size: 1.5rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            color: var(--dark);
        }

        .add-btn {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
            background: linear-gradient(135deg, #FF8E53 0%, #FF6B6B 100%);
        }

        /* Tables */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th {
            background-color: rgba(78, 205, 196, 0.1);
            color: var(--dark);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--accent);
        }

        .admin-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-light);
            vertical-align: middle;
        }

        .admin-table tr:hover {
            background-color: rgba(78, 205, 196, 0.05);
        }

        .action-btn {
            padding: 0.6rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 0.5rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .edit-btn {
            background: var(--gradient-accent);
            color: var(--dark);
            border: none;
            box-shadow: 0 4px 15px rgba(78, 205, 196, 0.3);
        }

        .edit-btn:hover {
            background: linear-gradient(135deg, #45B7D1 0%, #4ECDC4 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(78, 205, 196, 0.4);
            color: white;
        }

        .delete-btn {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .delete-btn:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .stat-icon.accent {
            background: var(--gradient-accent);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            color: var(--dark);
            line-height: 1;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* Image Thumbnails */
        .thumbnail {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        /* Role Badges */
        .role-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-block;
        }

        .role-admin {
            background: var(--gradient-accent);
            color: var(--dark);
        }

        .role-customer {
            background: var(--gradient-primary);
            color: white;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .admin-table {
                display: block;
                overflow-x: auto;
            }
        }

        @media (max-width: 768px) {
            nav {
                padding: 0 1.5rem;
            }

            .tabs {
                flex-wrap: wrap;
            }

            .tab {
                padding: 0.8rem 1.2rem;
                font-size: 0.9rem;
            }

            .admin-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .action-btn {
                margin-bottom: 0.5rem;
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Animation for tab switching */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav>
    <a href="mainpage.php" class="logo">FitFlex</a>
    <div class="nav-links">
        <a href="explore.php">Explore Classes</a>
        <a href="mainpage.php#footer">Contact</a>
    </div>
    <div class="user-profile">
        <span><?= htmlspecialchars($username) ?></span>
        <span class="badge-admin">ADMIN</span>
    </div>
    <div
            class="profile"
            onclick="window.location.href='?logout=1'"
            style="background-image: url('<?= htmlspecialchars($admin['avatar'] ?? 'assets/images/profileicon.png') ?>')"
    ></div>
</nav>

<!-- Main Content -->
<div class="main-content">
    <div class="container">
        <div class="tabs">
            <div class="tab active" data-tab="dashboard">Dashboard</div>
            <div class="tab" data-tab="classes">Classes</div>
            <div class="tab" data-tab="trainers">Trainers</div>
            <div class="tab" data-tab="users">Users</div>
        </div>

        <!-- Dashboard Tab -->
        <div class="tab-content active" id="dashboard">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>


                    <div>
                        <div class="stat-value"><?= count($users) ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= count($classes) ?></div>
                        <div class="stat-label">Fitness Classes</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon accent">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= count($trainers) ?></div>
                        <div class="stat-label">Trainers</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>


                    <div>
                        <div class="stat-value">
                            <?php
                            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings");
                            $stmt->execute();
                            $result = $stmt->get_result()->fetch_assoc();
                            echo $result['count'];
                            $stmt->close();
                            ?>
                        </div>

                        <div class="stat-label">Class Bookings</div>

                    </div>
                </div>

                <div class="center-static">
                    <div class="static-card">
                        <h3>Stay Motivated!</h3>
                        <p class="quote">“The only bad workout is the one you didn’t do.”</p>
                        <img src="assets/images/motivation.jpg" alt="Keep pushing!" class="static-img">
                    </div>
                </div>

            </div>


        </div>

        <!-- Classes Tab -->
        <div class="tab-content" id="classes">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Manage Classes</h3>
                    <a href="add_class.php" class="add-btn">
                        <i class="fas fa-plus"></i> Add Class
                    </a>
                </div>

                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Class</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($classes as $class): ?>
                        <tr>
                            <td><?= $counter++ ?></td>
                            <td><?= htmlspecialchars($class['name']) ?></td>
                            <td><?= htmlspecialchars($class['category']) ?></td>
                            <td>
                                <?php if ($class['image_url']): ?>
                                    <img src="assets/images/<?= htmlspecialchars($class['image_url']) ?>" class="thumbnail" alt="Class thumbnail">
                                <?php endif; ?>
                            </td>
                            <td><?= date('M j, Y', strtotime($class['created_at'])) ?></td>
                            <td>
                                <a href="edit_class.php?id=<?= $class['class_id'] ?>" class="action-btn edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete_class.php?id=<?= $class['class_id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this class?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Trainers Tab -->
        <div class="tab-content" id="trainers">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Manage Trainers</h3>
                    <a href="add_trainer.php" class="add-btn">
                        <i class="fas fa-plus"></i> Add Trainer
                    </a>
                </div>

                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Rating</th>
                        <th>Image</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($trainers as $trainer): ?>
                        <tr>
                            <td><?= $trainerCounter++ ?></td>
                            <td><?= htmlspecialchars($trainer['full_name']) ?></td>
                            <td><?= htmlspecialchars($trainer['specialization'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($trainer['rating']) ?>/5.0</td>
                            <td>
                                <?php if ($trainer['image_url']): ?>
                                    <img src="assets/images/<?= htmlspecialchars($trainer['image_url']) ?>" class="thumbnail" alt="Trainer thumbnail">
                                <?php endif; ?>
                            </td>
                            <td><?= date('M j, Y', strtotime($trainer['created_at'])) ?></td>
                            <td>
                                <a href="edit_trainer.php?id=<?= $trainer['trainer_id'] ?>" class="action-btn edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete_trainer.php?id=<?= $trainer['trainer_id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this trainer?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Users Tab -->
        <div class="tab-content" id="users">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Manage Users</h3>
                    <a href="add_user.php" class="add-btn">
                        <i class="fas fa-plus"></i> Add User
                    </a>
                </div>

                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $userCounter++ ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="role-badge <?= $user['role'] === 'admin' ? 'role-admin' : 'role-customer' ?>">
                                    <?= strtoupper(htmlspecialchars($user['role'])) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="action-btn edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php if ($user['user_id'] != $adminId): ?>
                                    <a href="delete_user.php?id=<?= $user['user_id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab switching functionality
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab, .tab-content').forEach(el => {
                el.classList.remove('active');
            });
            tab.classList.add('active');
            document.getElementById(tab.dataset.tab).classList.add('active');
        });
    });

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