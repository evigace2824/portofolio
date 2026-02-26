<?php
session_start();
require_once __DIR__ . '/includes/db.php';
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitFlex - Our Expert Trainers</title>
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
            background: linear-gradient(135deg, rgba(41, 47, 54, 0.9) 0%, rgba(78, 205, 196, 0.7) 100%);
            background-size: cover;
            background-position: center;
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            max-width: 700px;
            text-shadow: 0 1px 5px rgba(0,0,0,0.1);
        }

        .btn {
            background: var(--gradient-primary);
            color: var(--light);
            padding: 1rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--light);
            box-shadow: none;
            margin-left: 1rem;
        }

        .btn-outline:hover {
            background: rgba(255,255,255,0.1);
        }

        /* Trainers Section */
        .trainers-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, #ffffff 0%, #f7f7f7 100%);
            position: relative;
        }

        .trainers-section::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: var(--gradient-primary);
            border-radius: 50%;
            opacity: 0.1;
            z-index: 0;
        }

        .trainers-title {
            font-size: 2.8rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
            color: var(--dark);
        }

        .trainers-title::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 6px;
            background: var(--gradient-accent);
            bottom: -15px;
            left: 0;
            border-radius: 5px;
        }

        .trainer-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            height: 100%;
            background: white;
            position: relative;
            z-index: 1;
            margin-bottom: 2rem;
        }

        .trainer-card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .trainer-card .card-img-top {
            height: 300px;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .trainer-card:hover .card-img-top {
            transform: scale(1.1);
        }

        .trainer-card .card-body {
            padding: 2rem;
        }

        .trainer-card .card-title {
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: var(--dark);
            font-size: 1.4rem;
        }

        .trainer-card .specialization {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 1rem;
            display: block;
        }

        .trainer-card .bio {
            color: var(--text-light);
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .trainer-card .rating {
            color: #FFC107;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .trainer-card .btn-trainer {
            background: var(--gradient-accent);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .trainer-card .btn-trainer:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(78, 205, 196, 0.4);
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

            .trainers-title {
                font-size: 2.5rem;
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

            .hero p {
                font-size: 1.1rem;
            }

            .trainers-title {
                font-size: 2.2rem;
            }

            .trainer-card .card-img-top {
                height: 250px;
            }
        }

        @media (max-width: 576px) {
            .hero h1 {
                font-size: 2rem;
                margin-bottom: 1rem;
            }

            .hero p {
                font-size: 1rem;
                margin-bottom: 1.5rem;
            }

            .trainers-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav>
    <div class="logo">FitFlex</div>
    <div class="nav-links">
        <a href="mainpage.php#footer">Contact</a>
    </div>
    <div class="profile" id="profileIcon" onclick="window.location.href='<?= $isLoggedIn ? 'userpage.php' : 'login.php' ?>'" style="background-image: url('assets/images/profileicon.png');"></div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1 class="fade-in">Meet Our Expert Trainers</h1>
        <p>Professional guidance to help you achieve your fitness goals</p>
        <a href="#trainers" class="btn">View Trainers</a>
    </div>
</section>

<!-- Trainers Section -->
<section class="trainers-section" id="trainers">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <h2 class="trainers-title">Our Trainers</h2>
                </div>
                <div class="row">
                    <?php
                    $sql = "SELECT * FROM trainers ORDER BY full_name";
                    $res = $conn->query($sql);
                    if ($res && $res->num_rows) {
                        while ($t = $res->fetch_assoc()):
                            $filename = $t['image_url'];
                            $fsPath = __DIR__ . '/uploads/profile_pictures/' . $filename;
                            $webPath = 'uploads/profile_pictures/' . $filename;
                            $imgSrc = file_exists($fsPath) ? $webPath : 'assets/images/default_trainer.png';
                            ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="trainer-card">
                                    <img src="<?= htmlspecialchars($imgSrc) ?>" class="card-img-top" alt="<?= htmlspecialchars($t['full_name']) ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($t['full_name']) ?></h5>
                                        <span class="specialization"><?= htmlspecialchars($t['specialization']) ?></span>
                                        <p class="bio"><?= htmlspecialchars($t['bio']) ?></p>
                                        <div class="rating mb-3">
                                            <?php
                                            $stars = round($t['rating']);
                                            for ($i = 0; $i < 5; $i++) {
                                                echo $i < $stars ? '★' : '☆';
                                            }
                                            ?>
                                            <span class="ms-2">(<?= number_format($t['rating'], 1) ?>)</span>
                                        </div>
                                        <button class="btn btn-trainer w-100">View Profile</button>
                                    </div>
                                </div>
                            </div>
                        <?php
                        endwhile;
                    } else {
                        echo '<div class="col-12"><div class="alert alert-info">No trainers found.</div></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer id="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <div class="footer-logo">FitFlex</div>
                <p class="text">Helping you achieve your fitness goals since 2015.</p>
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
                        <li><a href="#">News & Articles</a></li>
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
                    <p class="text mb-3">Subscribe to get updates on new classes and offers.</p>
                    <form class="newsletter">
                        <input type="email" class="form-control mb-2" placeholder="Your email">
                        <button type="submit" class="btn btn-primary w-100">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 FitFlex. All rights reserved.</p>
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