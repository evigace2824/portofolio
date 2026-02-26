<?php
session_start();
require_once __DIR__ . '/includes/db.php';

// --- Handle "Book Class" ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_class'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    if ($_SESSION['role'] !== 'customer') {
        $_SESSION['error'] = 'Only customers can book classes.';
        header('Location: userpage.php');
        exit;
    }
    $_SESSION['selected_class_id'] = (int)$_POST['class_id'];
    header('Location: payment.php');
    exit;
}

// --- Handle "Remove Saved Class" ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_class'])) {
    $userId  = $_SESSION['user_id'];
    $classId = (int)$_POST['class_id'];
    $stmt = $conn->prepare("
        DELETE FROM saved_classes
         WHERE user_id = ?
           AND class_id = ?
    ");
    $stmt->bind_param("ii", $userId, $classId);
    $stmt->execute();
    $stmt->close();
    header("Location: userpage.php");
    exit();
}

// --- Log out ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// --- Ensure user is logged in ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$userId   = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? '';

// --- Fetch user info ---
$user = [];
if ($stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?")) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// --- Get saved classes ---
$savedClasses = [];
if ($stmt = $conn->prepare("
    SELECT
      c.class_id,
      c.name         AS class_name,
      c.category     AS class_category,
      c.description  AS class_description,
      c.image_url    AS class_image,
      s.saved_at     AS saved_date
    FROM saved_classes s
    JOIN classes c ON s.class_id = c.class_id
    WHERE s.user_id = ?
    ORDER BY s.saved_at DESC
")) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $savedClasses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// --- Get booked classes ---
$bookedClasses = [];
if ($stmt = $conn->prepare("
    SELECT
      c.class_id,
      c.name         AS class_name,
      c.category     AS class_category,
      c.description  AS class_description,
      c.image_url    AS class_image,
      b.booked_at    AS booking_date
    FROM bookings b
    JOIN classes c ON b.class_id = c.class_id
    WHERE b.user_id = ?
    ORDER BY b.booked_at DESC
")) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $bookedClasses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account – FitFlex</title>
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
            margin-top: 90px;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* User Profile Section */
        .user-profile-section {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid var(--accent);
            margin-right: 2rem;
        }

        .profile-info h2 {
            font-size: 2rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .profile-info p {
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        .profile-meta {
            display: flex;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
        }

        .meta-item i {
            color: var(--accent);
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

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Class Cards */
        .class-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .class-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .class-info {
            flex: 1;
        }

        .class-info h3 {
            color: var(--dark);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .class-details {
            display: flex;
            gap: 1.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .class-detail {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .class-detail i {
            color: var(--accent);
        }

        .class-image {
            width: 150px;
            height: 100px;
            border-radius: 10px;
            overflow: hidden;
            margin-left: 1.5rem;
        }

        .class-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .class-actions {
            display: flex;
            gap: 1rem;
            margin-left: 1.5rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background: rgba(255, 107, 107, 0.1);
        }

        .no-classes {
            text-align: center;
            padding: 3rem;
            color: var(--text-light);
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-avatar {
                margin-right: 0;
                margin-bottom: 1.5rem;
            }

            .profile-meta {
                justify-content: center;
            }

            .class-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .class-image {
                margin: 1.5rem 0 0 0;
                width: 100%;
            }

            .class-actions {
                margin: 1.5rem 0 0 0;
                width: 100%;
                justify-content: flex-end;
            }
        }

        @media (max-width: 768px) {
            .tabs {
                flex-direction: column;
                border-bottom: none;
            }

            .tab {
                border-bottom: 2px solid var(--gray-light);
            }

            .tab.active::after {
                display: none;
            }

            .class-details {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        /* Animation for tab switching */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
    <div class="profile" style="background-image: url('<?= htmlspecialchars($user['avatar'] ?? 'assets/images/profileicon.png') ?>')"></div>
</nav>

<!-- Main Content -->
<div class="main-content">
    <div class="container">
        <!-- User Profile Section -->
        <section class="user-profile-section">
            <div class="profile-header">
                <img
                        src="<?= htmlspecialchars($user['avatar'] ?? 'assets/images/profile.jpg') ?>"
                        class="profile-avatar"
                        alt="Profile Picture"
                >
                <div class="profile-info">
                    <h2><?= htmlspecialchars($username) ?></h2>
                    <p>Fitness Enthusiast</p>
                    <div class="profile-meta">
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Tirana, AL</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Joined <?= date('F Y') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <a href="editprofile.php" class="btn btn-primary">
                <i class="fas fa-user-edit"></i> Edit Profile
            </a>
            <a href="?logout=1" class="btn btn-outline" style="margin-left: 1rem;">
                <i class="fas fa-sign-out-alt"></i> Log Out
            </a>
        </section>

        <!-- Tabs -->
        <div class="tabs">
            <div class="tab active" data-tab="saved">Saved Classes</div>
            <div class="tab" data-tab="booked">Booked Classes</div>
        </div>

        <!-- Saved Classes Tab -->
        <div class="tab-content active" id="saved">
            <?php if (count($savedClasses)): ?>
                <?php foreach ($savedClasses as $c): ?>
                    <div class="class-card">
                        <div class="class-info">
                            <h3><?= htmlspecialchars($c['class_name']) ?></h3>
                            <div class="class-details">
                                <div class="class-detail">
                                    <i class="fas fa-tag"></i>
                                    <span><?= htmlspecialchars($c['class_category']) ?></span>
                                </div>
                                <div class="class-detail">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>Saved on <?= date('M j, Y', strtotime($c['saved_date'])) ?></span>
                                </div>
                                <div class="class-detail">
                                    <i class="fas fa-info-circle"></i>
                                    <span><?= htmlspecialchars($c['class_description']) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="class-image">
                            <img
                                    src="assets/images/<?= htmlspecialchars($c['class_image']) ?>"
                                    alt="<?= htmlspecialchars($c['class_name']) ?>"
                            >
                        </div>
                        <div class="class-actions">
                            <form method="post" style="display:inline">
                                <input type="hidden" name="class_id" value="<?= $c['class_id'] ?>">
                                <button
                                        type="submit"
                                        name="remove_class"
                                        class="btn btn-outline"
                                        onclick="return confirm('Remove this saved class?');"
                                >
                                    <i class="fas fa-trash-alt"></i> Remove
                                </button>
                            </form>
                            <form method="post" style="display:inline">
                                <input type="hidden" name="class_id" value="<?= $c['class_id'] ?>">
                                <button type="submit" name="book_class" class="btn btn-primary">
                                    <i class="fas fa-ticket-alt"></i> Book Now
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-classes">
                    <h3>No Saved Classes Yet</h3>
                    <p>You haven't saved any classes. Explore our classes and save your favorites!</p>
                    <a href="explore.php" class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-search"></i> Explore Classes
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Booked Classes Tab -->
        <div class="tab-content" id="booked">
            <?php if (count($bookedClasses)): ?>
                <?php foreach ($bookedClasses as $c): ?>
                    <div class="class-card">
                        <div class="class-info">
                            <h3><?= htmlspecialchars($c['class_name']) ?></h3>
                            <div class="class-details">
                                <div class="class-detail">
                                    <i class="fas fa-tag"></i>
                                    <span><?= htmlspecialchars($c['class_category']) ?></span>
                                </div>
                                <div class="class-detail">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>Booked on <?= date('M j, Y', strtotime($c['booking_date'])) ?></span>
                                </div>
                                <div class="class-detail">
                                    <i class="fas fa-info-circle"></i>
                                    <span><?= htmlspecialchars($c['class_description']) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="class-image">
                            <img
                                    src="assets/images/<?= htmlspecialchars($c['class_image']) ?>"
                                    alt="<?= htmlspecialchars($c['class_name']) ?>"
                            >
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-classes">
                    <h3>No Booked Classes Yet</h3>
                    <p>You haven't booked any classes yet. Book your first class today!</p>
                    <a href="explore.php" class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-ticket-alt"></i> Book a Class
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Tab functionality
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