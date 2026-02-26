<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitFlex - Ready to rise? Book your first class today.</title>
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
            height: 90vh;
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

        .hero-video {
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
            background: linear-gradient(135deg, rgba(41, 47, 54, 0.8) 0%, rgba(78, 205, 196, 0.4) 100%);
            z-index: -1;
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
        .view-all {
            background: var(--gradient-primary);
            color: var(--light);
            padding: 1rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(255,107,107,0.3);
            transition: all 0.3s ease;
        }
        .view-all:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255,107,107,0.4);
        }


        /* Why Choose Us */
        .why-choose {
            background-color: var(--light);
            border-radius: 30px;
            padding: 4rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }

        .why-choose::before {
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

        .why-choose::after {
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

        .why-choose h2 {
            font-size: 2.5rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .why-choose p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
            color: var(--text-dark);
        }

        .why-choose-img {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
            transition: all 0.4s ease;
        }

        .why-choose-img:hover {
            transform: rotate(-2deg) scale(1.02);
        }

        .why-choose-img img {
            width: 100%;
            height: auto;
            display: block;
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

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 3rem;
            }

            .section {
                padding: 4rem 0;
            }

            .why-choose {
                padding: 3rem;
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
                height: 80vh;
                padding-top: 80px;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .why-choose {
                padding: 2rem;
            }

            .why-choose h2 {
                font-size: 2rem;
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

            .section {
                padding: 3rem 0;
            }

            footer {
                padding: 3rem 0 1.5rem;
            }
        }

        /* Navbar Scroll Effect */
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
        nav.classList.add('scrolled');
        } else {
              nav.classList.remove('scrolled');
          }
        });

        /* ===== NEW STYLES FOR SPECIFIED SECTIONS ===== */

        /* Popular Classes - Unique Style */
        .popular-classes-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            padding: 6rem 0;
            position: relative;
            overflow: hidden;
        }

        .popular-classes-section::before {
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

        .popular-classes-container {
            position: relative;
            z-index: 1;
        }

        .popular-classes-title {
            font-size: 2.8rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
            color: var(--dark);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .popular-classes-title::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 6px;
            background: var(--gradient-accent);
            bottom: -15px;
            left: 0;
            border-radius: 5px;
        }

        .popular-classes-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }

        .popular-class-option {
            height: 250px;
            border-radius: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            font-weight: 600;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            background-size: cover;
            background-position: center;
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform-style: preserve-3d;
        }

        .popular-class-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.3) 100%);
            z-index: 1;
            transition: all 0.5s ease;
        }

        .popular-class-option:hover {
            transform: translateY(-15px) rotateX(10deg);
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        }

        .popular-class-option:hover::before {
            background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.2) 100%);
        }

        .popular-class-option span {
            position: relative;
            z-index: 2;
            color: white;
            text-align: center;
            padding: 1.5rem;
            font-size: 1.3rem;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
            width: 100%;
            transform: translateZ(30px);
        }

        /* Workout Types - Unique Style */
        .workout-types-section {
            background: var(--dark);
            color: var(--light);
            padding: 6rem 0;
            position: relative;
        }

        .workout-types-section::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 10px;
            background: var(--gradient-accent);
        }

        .workout-types-title {
            font-size: 2.8rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
            color: var(--light);
        }

        .workout-types-title::after {
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

        .workout-types-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            perspective: 1000px;
        }

        .workout-type-option {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
            background-size: cover;
            background-position: center;
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform-style: preserve-3d;
        }

        .workout-type-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            transition: all 0.5s ease;
        }

        .workout-type-option:hover {
            transform: scale(1.1) rotateY(20deg);
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
        }

        .workout-type-option:hover::before {
            background: rgba(0,0,0,0.3);
        }

        .workout-type-option span {
            position: relative;
            z-index: 2;
            color: white;
            text-align: center;
            font-size: 1.2rem;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
            transform: translateZ(30px);
        }

        /* Trainers & Nutrition - Unique Style */
        .trainers-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, #ffffff 0%, #f7f7f7 100%);
            position: relative;
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
            background: var(--gradient-primary);
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 5px;
        }

        .trainer-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
            height: 100%;
            background: white;
            position: relative;
            z-index: 1;
        }

        .trainer-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(255,255,255,0) 60%, rgba(255,255,255,1) 100%);
            z-index: 2;
        }

        .trainer-card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .trainer-card .card-img-top {
            height: 250px;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .trainer-card:hover .card-img-top {
            transform: scale(1.1);
        }

        .trainer-card .card-body {
            position: relative;
            z-index: 3;
            background: white;
        }

        .trainer-card .card-title {
            font-weight: 800;
            margin-bottom: 0.8rem;
            color: var(--dark);
            font-size: 1.3rem;
        }

        /* Fitness Tips & Advice - Unique Style */
        .tips-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            position: relative;
        }

        .tips-title {
            font-size: 2.8rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
            color: var(--dark);
        }

        .tips-title::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 6px;
            background: var(--gradient-accent);
            bottom: -15px;
            left: 0;
            border-radius: 5px;
        }

        .tip-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.5s ease;
            height: 100%;
            background: white;
            position: relative;
        }

        .tip-card:hover {
            transform: translateY(-10px) rotate(-1deg);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .tip-card .card-img-top {
            height: 200px;
            object-fit: cover;
            transition: all 0.8s ease;
        }

        .tip-card:hover .card-img-top {
            transform: scale(1.05) rotate(1deg);
        }

        .tip-card .card-body {
            padding: 2rem;
        }

        .tip-card .card-title {
            font-weight: 800;
            margin-bottom: 1rem;
            color: var(--dark);
            font-size: 1.4rem;
            position: relative;
        }

        .tip-card .card-title::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 3px;
            background: var(--gradient-primary);
            bottom: -8px;
            left: 0;
        }

        .tip-card .meta {
            display: flex;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0,0,0,0.1);
        }

        /* Responsive adjustments for new sections */
        @media (max-width: 768px) {
            .popular-classes-title,
            .workout-types-title,
            .trainers-title,
            .tips-title {
                font-size: 2.2rem;
            }

            .popular-class-option {
                height: 200px;
            }

            .workout-type-option {
                width: 150px;
                height: 150px;
            }
        }

        @media (max-width: 576px) {
            .popular-classes-title,
            .workout-types-title,
            .trainers-title,
            .tips-title {
                font-size: 2rem;
            }

            .popular-class-option {
                height: 180px;
            }

            .workout-type-option {
                width: 130px;
                height: 130px;
            }
        }


        /* — Map Section — */
        .map-section {
            padding: 4rem 0;
            text-align: center;
            background: var(--light);
        }
        .map-container {

            width: 80%;
            max-width: 600px;
            margin: 0 auto;
            padding-bottom: 40%;
            position: relative;
            height: 0;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

    </style>
</head>
<body>
<!-- Navbar -->
<nav>
    <div class="logo">FitFlex</div>
    <div class="nav-links">
        <a href="#footer">Contact</a>
    </div>
    <div
            class="profile"
            id="profileIcon"
            style="cursor: pointer; background-image: url('assets/images/profileicon.png'); background-size: cover; background-position: center;"
            onclick="window.location.href='<?php echo $isLoggedIn ? 'userpage.php' : 'login.php'; ?>'">
    </div>
</nav>

<!-- Hero Section with Video Background -->
<section class="hero" style="position:relative; height:90vh; overflow:hidden;">
    <video class="hero-video"
           autoplay muted loop playsinline
           style="
           position:absolute;
           top:50%; left:50%;
           width:auto; height:100%;
           min-width:100%;
           transform:translate(-50%,-50%);
           object-fit:cover;
         ">
        <source src="assets/videos/gym-workout.mp4" type="video/mp4">
        <!-- Fallback message -->
        Your browser doesn't support HTML5 video.
    </video>

    <!-- Dark overlay so text stays legible -->
    <div class="hero-overlay"
         style="position:absolute; inset:0; background:linear-gradient(135deg, rgba(41, 47, 54, 0.8) 0%, rgba(78, 205, 196, 0.4) 100%);"></div>

    <!-- Headline on top -->
    <div class="container">
        <h1 class="fade-in" style="
            position:relative;
            z-index:1;
            color:#fff;
            font-size:4rem;
            font-weight:800;
            font-family:'Montserrat', sans-serif;
            margin-bottom:1.5rem;
            text-shadow:0 2px 10px rgba(0,0,0,0.2);
            line-height:1.2;
          ">
            Ready to rise? Book your first class today.
        </h1>
        <div class="d-flex justify-content-center gap-3">
            <a href="explore.php" class="btn">Explore Classes</a>
            <a href="<?php echo $isLoggedIn ? 'userpage.php' : 'login.php'; ?>" class="btn btn-outline">Get Started</a>
        </div>
    </div>
</section>

<!-- Popular Classes -->
<section class="popular-classes-section">
    <div class="container popular-classes-container">
        <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
            <h2 class="popular-classes-title">Popular Classes</h2>
            <a href="explore.php" class="view-all btn">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="popular-classes-options">
            <!-- Yoga -->
            <div class="popular-class-option" style="background-image: url('assets/images/yoga.jpg')">
                <span>Yoga<br><small>Mind & Body</small></span>
            </div>

            <!-- HIIT -->
            <div class="popular-class-option" style="background-image: url('assets/images/hiit.jpg')">
                <span>HIIT<br><small>High Intensity</small></span>
            </div>

            <!-- Pilates -->
            <div class="popular-class-option" style="background-image: url('assets/images/pilates.jpg')">
                <span>Pilates<br><small>Core Strength</small></span>
            </div>


            <!-- Zumba -->
            <div class="popular-class-option" style="background-image: url('assets/images/zumba.jpg')">
                <span>Zumba<br><small>Dance Fitness</small></span>
            </div>

            <!-- CrossFit -->
            <div class="popular-class-option" style="background-image: url('assets/images/crossfit.jpg')">
                <span>CrossFit<br><small>Strength Training</small></span>
            </div>
        </div>
    </div>
</section>

<!-- Workout Types -->
<section class="workout-types-section">
    <div class="container">
        <h2 class="workout-types-title text-center mb-5">Workout Types</h2>
        <div class="workout-types-container">
            <!-- Strength Training -->
            <div class="workout-type-option" style="background-image: url('assets/images/strength.jpg')">
                <span>Strength</span>
            </div>

            <!-- Cardio -->
            <div class="workout-type-option" style="background-image: url('assets/images/cardio.jpg')">
                <span>Cardio</span>
            </div>

            <!-- Functional -->
            <div class="workout-type-option" style="background-image: url('assets/images/functional.jpg')">
                <span>Functional</span>
            </div>

            <!-- Recovery -->
            <div class="workout-type-option" style="background-image: url('assets/images/recovery.jpg')">
                <span>Recovery</span>
            </div>

            <!-- Boxing -->
            <div class="workout-type-option" style="background-image: url('assets/images/boxing.jpg')">
                <span>Boxing</span>
            </div>

            <!-- Dance -->
            <div class="workout-type-option" style="background-image: url('assets/images/dance.jpg')">
                <span>Dance</span>
            </div>
        </div>
    </div>
</section>

<!-- Trainers & Nutrition -->
<section class="trainers-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="trainers-title">Trainers & Nutrition</h2>
            <a href="trainers.php" class="btn btn-primary">View All Trainers</a>
        </div>
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="trainer-card">
                    <img src="assets/images/trainer1.jpg" class="card-img-top" alt="Personal Trainer">
                    <div class="card-body">
                        <h5 class="card-title">Alex Johnson</h5>
                        <p class="card-text"><i class="fas fa-map-marker-alt"></i> Strength Specialist</p>
                        <div class="meta">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                            <span class="ms-2">4.5</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="trainer-card">
                    <img src="assets/images/nutrition1.jpg" class="card-img-top" alt="Nutrition Plan">
                    <div class="card-body">
                        <h5 class="card-title">Clean Eating Plan</h5>
                        <p class="card-text"><i class="fas fa-map-marker-alt"></i> Balanced Diet</p>
                        <div class="meta">
                            <i class="fas fa-utensils text-primary"></i>
                            <span class="ms-2">Meal Plans</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="trainer-card">
                    <img src="assets/images/trainer2.jpg" class="card-img-top" alt="Yoga Instructor">
                    <div class="card-body">
                        <h5 class="card-title">Sarah Williams</h5>
                        <p class="card-text"><i class="fas fa-map-marker-alt"></i> Yoga & Mindfulness</p>
                        <div class="meta">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <span class="ms-2">5.0</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="trainer-card">
                    <img src="assets/images/nutrition2.jpg" class="card-img-top" alt="Protein Shakes">
                    <div class="card-body">
                        <h5 class="card-title">Protein Power</h5>
                        <p class="card-text"><i class="fas fa-map-marker-alt"></i> Post-Workout</p>
                        <div class="meta">
                            <i class="fas fa-utensils text-primary"></i>
                            <span class="ms-2">Recovery</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fitness Tips and Advice -->
<section class="tips-section">
    <div class="container">
        <h2 class="tips-title mb-5">Fitness Tips & Advice</h2>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="tip-card">
                    <img src="assets/images/tip1.jpg" class="card-img-top" alt="Fitness Tip">
                    <div class="card-body">
                        <h5 class="card-title">Morning Routine</h5>
                        <p class="card-text">Start your day right with these energizing morning exercises.</p>
                        <div class="meta">
                            <small><i class="far fa-calendar-alt"></i> Today</small>
                            <small class="ms-3"><i class="far fa-user"></i> Noah Brown</small>
                            <small class="ms-3"><i class="far fa-comment"></i> 6 Comments</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="tip-card">
                    <img src="assets/images/tip2.jpg" class="card-img-top" alt="Fitness Tip">
                    <div class="card-body">
                        <h5 class="card-title">Hydration Guide</h5>
                        <p class="card-text">Discover why proper hydration is key to your fitness success.</p>
                        <div class="meta">
                            <small><i class="far fa-calendar-alt"></i> 9 days ago</small>
                            <small class="ms-3"><i class="far fa-user"></i> Ethan Kim</small>
                            <small class="ms-3"><i class="far fa-comment"></i> 10 Comments</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section">
    <div class="container">
        <div class="why-choose">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h2 class="mb-4">Why choose FitFlex?</h2>
                    <p class="mb-4">We craft personalized fitness experiences tailored to your goals. Our team of expert trainers works tirelessly to ensure every aspect of your fitness journey is perfect, from customized workouts to nutritional guidance.</p>
                    <a href="#" class="btn btn-outline-primary rounded-pill">Read More</a>
                </div>
                <div class="col-md-6">
                    <div class="why-choose-img">
                        <img src="assets/images/why-choose.jpg" class="img-fluid rounded" alt="Happy Members">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <h2>Find Us Here</h2>
    <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7522200.660921432!2d68.161191925!3d22.988882900000007!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e87225d325437%3A0x7e037ccb7818e3fc!2sFitFlex%20Gym!5e0!3m2!1sen!2s!4v1748473142029!5m2!1sen!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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
        <div class="text-center mt-5 pt-3 border-top border-secondary">
            <p class="text-muted mb-0">&copy; 2025 FitFlex. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.querySelectorAll('.search-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            alert('Search functionality would go here!');
        });
    });
</script>

<script>
    document.getElementById('profileIcon').addEventListener('click', function() {
        <?php if ($isLoggedIn): ?>
        window.location.href = 'userpage.php';
        <?php else: ?>
        window.location.href = 'login.php';
        <?php endif; ?>
    });
</script>

</body>
</html>