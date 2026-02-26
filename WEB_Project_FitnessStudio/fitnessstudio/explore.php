<?php
session_start();

// 1) Database connection
$dbHost   = '127.0.0.1';
$dbPort   = 3306;
$dbUser   = 'root';
$dbPass   = '';
$dbName   = 'fitflex_db';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($conn->connect_error) {
    die('DB connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

// 2) Handle "Save Class" POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_class'])) {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    if ($_SESSION['role'] !== 'customer') {
        $_SESSION['error'] = 'Only customers can save classes.';
        header('Location: explore.php');
        exit;
    }

    $userId  = (int)$_SESSION['user_id'];
    $classId = (int)$_POST['class_id'];

    $stmt = $conn->prepare("
      INSERT IGNORE INTO saved_classes (user_id, class_id)
      VALUES (?, ?)
    ");
    $stmt->bind_param('ii', $userId, $classId);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = 'Class saved!';
    header('Location: explore.php');
    exit;
}

// 3) Fetch all classes
$classes = [];
$sql = "
  SELECT
    class_id,
    name         AS title,
    category     AS intensity,
    description  AS schedule,
    image_url    AS image_file
  FROM classes
  ORDER BY created_at DESC
";
if ($res = $conn->query($sql)) {
    while ($row = $res->fetch_assoc()) {
        $classes[] = $row;
    }
    $res->free();
} else {
    die('Query error: ' . $conn->error);
}

// 4) Build alerts
$alert = '';
if (!empty($_SESSION['success'])) {
    $alert = '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
    unset($_SESSION['success']);
}
if (!empty($_SESSION['error'])) {
    $alert = '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Classes - FitFlex</title>
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

        nav.scrolled .logo {
            font-size: 1.5rem;
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

        /* Hero Section */
        .hero {
            height: 60vh;
            position: relative;
            color: var(--light);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding-top: 90px;
            overflow: hidden;
        }

        .hero-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                    rgba(0, 0, 0, 0.8),
                    rgba(0, 0, 0, 0.4)
            );
            z-index: -1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
            line-height: 1.2;
        }

        /* Section Styling */
        .section {
            padding: 6rem 0;
            position: relative;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
            color: var(--dark);
        }

        .section-title::after {
            content: '';
            position: absolute;
            width: 70px;
            height: 5px;
            background: var(--gradient-primary);
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 5px;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            background-color: var(--light);
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.12);
        }

        .card-img-top {
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .card:hover .card-img-top {
            transform: scale(1.05);
        }

        .card-body {
            padding: 1.8rem;
        }

        .card-title {
            font-weight: 700;
            margin-bottom: 0.8rem;
            color: var(--dark);
            font-size: 1.2rem;
        }

        .card-text {
            color: var(--text-light);
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .meta {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-top: 1rem;
            display: flex;
            align-items: center;
        }

        .meta i {
            margin-right: 5px;
            color: var(--primary);
        }

        /* Buttons */
        .btn {
            background: var(--gradient-primary);
            color: var(--light);
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
            position: relative;
            overflow: hidden;
            display: inline-block;
            text-align: center;
            width: 100%;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        }

        .btn:active {
            transform: translateY(1px);
        }

        /* Alert Messages */
        .alert {
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 50px;
            text-align: center;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Footer */
        footer {
            background-color: var(--dark);
            color: var(--light);
            padding: 5rem 0 2rem;
            position: relative;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 10px;
            background: var(--gradient-primary);
        }

        .footer-logo {
            font-size: 1.8rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-text {
            color: rgba(255,255,255,0.7);
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }

        .footer-links h5 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
            color: var(--light);
        }

        .footer-links h5::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 3px;
            background: var(--gradient-accent);
            bottom: -8px;
            left: 0;
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            display: inline-block;
        }

        .footer-links a:hover {
            color: var(--accent);
            transform: translateX(5px);
        }

        .social-icons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            color: var(--light);
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            background: var(--gradient-primary);
            transform: translateY(-3px);
        }

        .newsletter input {
            border-radius: 50px;
            border: none;
            padding: 0.9rem 1.2rem;
            width: 100%;
            background: rgba(255,255,255,0.1);
            color: var(--light);
            margin-bottom: 1rem;
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }

        .newsletter input:focus {
            outline: none;
            border-color: var(--accent);
            background: rgba(255,255,255,0.15);
        }

        .newsletter input::placeholder {
            color: rgba(255,255,255,0.6);
        }

        .newsletter .btn {
            width: 100%;
            border-radius: 50px;
            padding: 0.9rem;
            font-weight: 600;
            margin-top: 0.5rem;
            background: var(--gradient-accent);
            box-shadow: 0 4px 15px rgba(78, 205, 196, 0.3);
        }

        .newsletter .btn:hover {
            box-shadow: 0 6px 20px rgba(78, 205, 196, 0.4);
        }

        .copyright {
            text-align: center;
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.5);
            font-size: 0.9rem;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 3rem;
            }

            .section {
                padding: 4rem 0;
            }
        }

        @media (max-width: 768px) {
            nav {
                padding: 0 1.5rem;
                height: 80px;
            }

            .nav-links {
                gap: 1.5rem;
            }

            .hero {
                height: 50vh;
                padding-top: 80px;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 576px) {
            .hero h1 {
                font-size: 2rem;
                margin-bottom: 1rem;
            }

            .section {
                padding: 3rem 0;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .card-img-top {
                height: 180px;
            }

            footer {
                padding: 3rem 0 1.5rem;
            }
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav>
    <div class="logo">FitFlex</div>
    <div class="nav-links">
        <a href="mainpage.php">Home</a>
        <a href="#footer">Contact</a>
    </div>
    <div class="profile"
         style="background-image: url('assets/images/profileicon.png');"
         onclick="window.location.href='<?php echo isset($_SESSION['user_id']) ? 'userpage.php' : 'login.php'; ?>'">
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <img src="assets/images/hero-bg.jpg" class="hero-image" alt="Fitness Classes">
    <div class="hero-overlay"></div>
    <div class="container">
        <h1>Explore Our Fitness Classes</h1>
    </div>
</section>

<!-- Classes Section -->
<section class="section">
    <div class="container">
        <?= $alert ?>

        <div class="row">
            <?php if (count($classes)): ?>
                <?php foreach ($classes as $c): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <?php if (!empty($c['image_file'])): ?>
                                <img src="assets/images/<?= htmlspecialchars($c['image_file']) ?>"
                                     class="card-img-top"
                                     alt="<?= htmlspecialchars($c['title']) ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($c['title']) ?></h5>
                                <p class="card-text">
                                    <i class="fas fa-fire text-primary"></i>
                                    <?= htmlspecialchars($c['intensity']) ?>
                                </p>
                                <p class="card-text"><?= htmlspecialchars($c['schedule']) ?></p>

                                <!-- Save form -->
                                <form method="post" class="save-form">
                                    <input type="hidden" name="class_id" value="<?= $c['class_id'] ?>">
                                    <button type="submit" name="save_class" class="btn">
                                        <i class="fas fa-bookmark"></i> Save Class
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>No classes available right now.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Footer -->
<footer id="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <div class="footer-logo">FitFlex</div>
                <p class="footer-text">Helping you achieve your fitness goals since 2023.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <div class="footer-links">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Classes</a></li>
                        <li><a href="#">Our Trainers</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <div class="footer-links">
                    <h5>Support</h5>
                    <ul>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Feedback</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="footer-links">
                    <h5>Newsletter</h5>
                    <p class="footer-text">Subscribe for class updates and fitness tips.</p>
                    <form class="newsletter">
                        <input type="email" placeholder="Your email">
                        <button type="submit" class="btn">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="copyright">
            &copy; 2023 FitFlex. All rights reserved.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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