<?php
global $conn;
session_start();

require_once __DIR__ . '/includes/db.php';

// Initialize form fields
$fields = [
    'first_name'       => '',
    'last_name'        => '',
    'username'         => '',
    'email'            => '',
    'password'         => '',
    'confirm_password' => '',
    'phone'            => '',
    'address'          => '',
    'date_of_birth'    => '',
    'profile_picture'  => null,
];

$errors = array_fill_keys(array_keys($fields), '');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) pull in all the text fields:
    foreach ($fields as $key => $val) {
        if ($key === 'profile_picture') continue;
        $fields[$key] = trim($_POST[$key] ?? '');
    }

    // 2) *** robust file‐upload comes here ***
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        // Create uploads directory if it doesn't exist
        $uploadDir = __DIR__ . '/uploads/profile_pictures/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // build a safe, unique filename
        $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $newName = uniqid('avatar_') . '.' . $ext;
        $dest = $uploadDir . $newName;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $dest)) {
            // save the **web‐path** in your DB field
            $fields['profile_picture'] = 'uploads/profile_pictures/' . $newName;
        } else {
            $errors['profile_picture'] = 'Failed to upload profile picture.';
        }
    }


    // Validation
    if ($fields['first_name'] === '') {
        $errors['first_name'] = 'Please enter your first name.';
    }

    if ($fields['last_name'] === '') {
        $errors['last_name'] = 'Please enter your last name.';
    }

    if ($fields['username'] === '') {
        $errors['username'] = 'Please choose a username.';
    } else {
        // Check uniqueness
        $check = $conn->prepare("SELECT user_id FROM users WHERE username = ? LIMIT 1");
        $check->bind_param("s", $fields['username']);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $errors['username'] = 'This username is already taken.';
        }
        $check->close();
    }

    if ($fields['email'] === '') {
        $errors['email'] = 'Please enter your email address.';
    } elseif (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } else {
        // Check uniqueness
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
        $check->bind_param("s", $fields['email']);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $errors['email'] = 'This email is already registered.';
        }
        $check->close();
    }

    if ($fields['password'] === '') {
        $errors['password'] = 'Please enter a password.';
    } elseif (strlen($fields['password']) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }

    if ($fields['confirm_password'] === '') {
        $errors['confirm_password'] = 'Please confirm your password.';
    } elseif ($fields['password'] !== $fields['confirm_password']) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if ($fields['phone'] !== '' && !preg_match('/^[0-9\-\+\s]+$/', $fields['phone'])) {
        $errors['phone'] = 'Please enter a valid phone number.';
    }

    if ($fields['date_of_birth'] !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $fields['date_of_birth']);
        if (!$d || $d->format('Y-m-d') !== $fields['date_of_birth']) {
            $errors['date_of_birth'] = 'Please enter a valid date (YYYY-MM-DD).';
        }
    }

    // If no errors: insert
    if (!array_filter($errors)) {
        $hash = password_hash($fields['password'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("
          INSERT INTO users
            (username, email, password_hash,
             first_name, last_name, phone,
             address, profile_picture, date_of_birth, role)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'customer')
        ");
        $stmt->bind_param(
            "sssssssss",
            $fields['username'],
            $fields['email'],
            $hash,
            $fields['first_name'],
            $fields['last_name'],
            $fields['phone'],
            $fields['address'],
            $fields['profile_picture'],
            $fields['date_of_birth']
        );

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['role'] = 'customer';
            $_SESSION['username'] = $fields['username'];
            $_SESSION['first_name'] = $fields['first_name'];
            header("Location: userpage.php");
            exit;
        } else {
            die("DB error: " . $stmt->error);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - FitFlex</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            --error: #e74c3c;
            --success: #2ecc71;
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

        /* Hero Section */
        .hero-container {
            display: flex;
            min-height: 100vh;
        }

        .hero-left {
            flex: 1;
            background:
                    radial-gradient(ellipse at 20% 30%, #1a0a17 0%, #0a0e17 70%),
                    repeating-linear-gradient(
                            45deg,
                            transparent,
                            transparent 2px,
                            rgba(78, 205, 196, 0.03) 3px,
                            rgba(78, 205, 196, 0.03) 4px
                    ),
                    repeating-linear-gradient(
                            -45deg,
                            transparent,
                            transparent 2px,
                            rgba(255, 107, 107, 0.03) 3px,
                            rgba(255, 107, 107, 0.03) 4px
                    );
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            color: white;
        }

        .hero-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                    url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400"><path d="M0,200 Q100,50 200,200 T400,200" stroke="%234ECDC4" stroke-width="0.5" fill="none" opacity="0.15" transform="rotate(15 200 200)"/><path d="M0,200 Q100,350 200,200 T400,200" stroke="%234ECDC4" stroke-width="0.5" fill="none" opacity="0.15" transform="rotate(-5 200 200)"/></svg>');
            background-size: 400px 400px;
            background-position: center;
            mix-blend-mode: overlay;
            z-index: 1;
        }

        .hero-left::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                    radial-gradient(circle at 70% 30%, rgba(255, 107, 107, 0.2) 0%, transparent 25%),
                    radial-gradient(circle at 30% 70%, rgba(78, 205, 196, 0.2) 0%, transparent 25%);
            z-index: 2;
            animation: pulse 15s infinite alternate;
        }

        @keyframes pulse {
            0% { opacity: 0.7; }
            50% { opacity: 1; }
            100% { opacity: 0.7; }
        }

        .hero-content {
            position: relative;
            z-index: 3;
            max-width: 500px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }
        .hero-title {
            font-size: 2.8rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-text {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        /* Form Section */
        .form-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background: white;
        }

        .form-container {
            width: 100%;
            max-width: 450px;
        }

        .form-title {
            font-size: 2rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            margin-bottom: 2rem;
            color: var(--dark);
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 3rem;
            border: 1px solid var(--gray-light);
            border-radius: 50px;
            font-size: 1rem;
            transition: all 0.3s;
            background: var(--light);
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(78, 205, 196, 0.2);
            outline: none;
        }

        textarea.form-control {
            border-radius: 15px;
            min-height: 100px;
            padding: 1rem;
        }

        .error {
            color: var(--error);
            font-size: 0.8rem;
            margin-top: 0.5rem;
            margin-left: 1rem;
        }

        /* Profile Picture Upload */
        .profile-upload {
            text-align: center;
            margin-bottom: 2rem;
        }

        .profile-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent);
            margin-bottom: 1rem;
            display: none;
            background: var(--gray-light);
        }

        .upload-label {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: var(--gradient-accent);
            color: var(--dark);
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(78, 205, 196, 0.3);
        }

        .upload-label:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(78, 205, 196, 0.4);
        }

        .upload-input {
            display: none;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 107, 107, 0.4);
            background: linear-gradient(135deg, #FF8E53 0%, #FF6B6B 100%);
        }

        /* Login Link */
        .login-link {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-light);
        }

        .login-link a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s;
        }

        .login-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Logo */
        .logo {
            position: absolute;
            top: 2rem;
            left: 2rem;
            font-size: 1.8rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            z-index: 10;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 768px) {
            .hero-container {
                flex-direction: column;
            }

            .hero-left {
                padding: 4rem 2rem;
                text-align: center;
            }

            .hero-title {
                font-size: 2rem;
            }

            .form-section {
                padding: 2rem 1.5rem;
            }

            .logo {
                top: 1rem;
                left: 1rem;
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.8rem;
            }

            .form-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
<a href="mainpage.php" class="logo">FitFlex</a>

<div class="hero-container">
    <div class="hero-left">
        <div class="hero-content">
            <h1 class="hero-title">Join Our Fitness Community</h1>
            <p class="hero-text">Sign up today to access personalized workouts, track your progress, and join a community of fitness enthusiasts.</p>
        </div>
    </div>

    <div class="form-section">
        <div class="form-container">
            <h2 class="form-title">Create Account</h2>
            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data">
                <!-- Personal Information -->
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" class="form-control" name="first_name" placeholder="First Name" value="<?= htmlspecialchars($fields['first_name']) ?>" required>
                    <div class="error"><?= $errors['first_name'] ?></div>
                </div>

                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($fields['last_name']) ?>" required>
                    <div class="error"><?= $errors['last_name'] ?></div>
                </div>

                <div class="form-group">
                    <i class="fas fa-calendar"></i>
                    <input type="date" class="form-control" name="date_of_birth" placeholder="Date of Birth" value="<?= htmlspecialchars($fields['date_of_birth']) ?>">
                    <div class="error"><?= $errors['date_of_birth'] ?></div>
                </div>

                <!-- Profile Picture -->
                <div class="profile-upload">
                    <img id="profile-preview" class="profile-preview" src="#" alt="Profile Preview">
                    <label for="profile-upload" class="upload-label">
                        <i class="fas fa-camera"></i> Choose Profile Picture
                    </label>
                    <input type="file" id="profile-upload" name="profile_picture" class="upload-input" accept="image/*">
                    <div class="error"><?= $errors['profile_picture'] ?></div>
                </div>

                <!-- Contact Information -->
                <div class="form-group">
                    <i class="fas fa-phone"></i>
                    <input type="tel" class="form-control" name="phone" placeholder="Phone Number" value="<?= htmlspecialchars($fields['phone']) ?>">
                    <div class="error"><?= $errors['phone'] ?></div>
                </div>

                <div class="form-group">
                    <i class="fas fa-map-marker-alt"></i>
                    <textarea class="form-control" name="address" placeholder="Address"><?= htmlspecialchars($fields['address']) ?></textarea>
                </div>

                <!-- Account Information -->
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" class="form-control" name="username" placeholder="Username" value="<?= htmlspecialchars($fields['username']) ?>" required>
                    <div class="error"><?= $errors['username'] ?></div>
                </div>

                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" class="form-control" name="email" placeholder="Email Address" value="<?= htmlspecialchars($fields['email']) ?>" required>
                    <div class="error"><?= $errors['email'] ?></div>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>

                    <div class="error"><?= $errors['password'] ?></div>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                    <div class="error"><?= $errors['confirm_password'] ?></div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Sign Up
                </button>
            </form>

            <div class="login-link">
                Already have an account? <a href="login.php">Log in here</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Profile picture preview
    document.getElementById('profile-upload').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profile-preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>