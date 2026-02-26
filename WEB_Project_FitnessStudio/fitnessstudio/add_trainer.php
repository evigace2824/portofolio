<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/includes/db.php';

// Only admins can add trainers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        if (empty(trim($_POST['full_name'])) || empty(trim($_POST['specialization']))) {
            throw new Exception("Name and specialization are required.");
        }
        // Prepare data
        $fullName       = trim($_POST['full_name']);
        $specialization = trim($_POST['specialization']);
        $bio            = trim($_POST['bio'] ?? '');
        $rating         = is_numeric($_POST['rating']) ? floatval($_POST['rating']) : 0.0;

        // Image upload (optional)
        $imageUrl = null;
        if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmp      = $_FILES['image']['tmp_name'];
            $original = basename($_FILES['image']['name']);
            $ext      = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
                throw new Exception("Invalid image format. Use JPG, PNG, or GIF.");
            }
            $filename = uniqid('trainer_', true) . ".{$ext}";
            $dest     = __DIR__ . '/assets/images/' . $filename;
            if (!move_uploaded_file($tmp, $dest)) {
                throw new Exception("Failed to save uploaded image.");
            }
            $imageUrl = $filename;
        }

        // Insert into database
        $stmt = $conn->prepare("
            INSERT INTO trainers
               (full_name, specialization, bio, image_url, rating, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param('sssdi',
            $fullName,
            $specialization,
            $bio,
            $imageUrl,
            $rating
        );
        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }

        $_SESSION['success'] = "Trainer added successfully.";
        header('Location: admin.php?tab=trainers');
        exit();

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: add_trainer.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Add Trainer – FitFlex</title>
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

        .form-header h2 {
            font-size: 2.2rem;
            color: var(--dark);
            margin-bottom: 1rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            position: relative;
            display: inline-block;
        }

        .form-header h2::after {
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
        input[type="number"],
        input[type="file"],
        textarea,
        select {
            width: 100%;
            padding: 0.9rem 1.2rem;
            border: none;
            border-radius: 12px;
            transition: all 0.3s;
            font-size: 0.9rem;
            background-color: var(--gray-light);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(78, 205, 196, 0.2);
        }

        /* File Input Style */
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            font-size: 100px;
            opacity: 0;
            right: 0;
            top: 0;
            cursor: pointer;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.9rem 1.2rem;
            background-color: var(--gray-light);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-input-label:hover {
            background-color: #e9ecef;
        }

        .file-input-text {
            color: var(--text-light);
            font-size: 0.9rem;
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

            .form-header h2 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            .form-container {
                padding: 1.5rem;
            }

            .form-header h2 {
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
            <h2><i class="fas fa-user-plus"></i> Add Trainer</h2>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>

            <div class="form-group">
                <label for="specialization">Specialization *</label>
                <input type="text" id="specialization" name="specialization" required>
            </div>

            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio"></textarea>
            </div>

            <div class="form-group">
                <label for="rating">Rating (0.0–5.0)</label>
                <input type="number" step="0.1" min="0" max="5" id="rating" name="rating" value="0.0">
            </div>

            <div class="form-group">
                <label for="image">Profile Image</label>
                <div class="file-input-wrapper">
                    <label class="file-input-label">
                        <span class="file-input-text">Choose an image...</span>
                        <i class="fas fa-upload"></i>
                    </label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
            </div>

            <button type="submit" class="btn"><i class="fas fa-check"></i> Create Trainer</button>
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