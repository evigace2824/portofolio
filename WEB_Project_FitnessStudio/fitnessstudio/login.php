<?php
session_start();

// Database connection
$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';
$dbName = 'fitflex_db';
$dbPort = 3306;

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($conn->connect_error) {
    die('DB connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error_message = 'Please enter both username and password.';
    } else {
        $stmt = $conn->prepare("SELECT user_id, username, password_hash, role FROM users WHERE username = ? LIMIT 1");

        if ($stmt === false) {
            $error_message = 'Database error: ' . $conn->error;
        } else {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Role-based redirection
                if ($user['role'] === 'admin') {
                    header('Location: admin.php');
                    exit();
                } else {
                    header('Location: userpage.php');
                    exit();
                }
            } else {
                $error_message = 'Invalid username or password.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FitFlex</title>
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

        .error {
            color: var(--error);
            font-size: 0.8rem;
            margin-top: 0.5rem;
            margin-left: 1rem;
        }
        .password-group {
            position: relative;
        }
        .password-group .show-pass {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        .password-group .show-pass .hide {
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

        /* Options */
        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1.5rem 0;
            font-size: 0.9rem;
        }

        .options label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
        }

        .options a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .options a:hover {
            text-decoration: underline;
        }

        /* Signup Link */
        .signup-link {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-light);
        }

        .signup-link a {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.8rem 2rem;
            background: var(--gradient-accent);
            color: var(--dark);
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(78, 205, 196, 0.3);
        }

        .signup-link a:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(78, 205, 196, 0.4);
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
            text-decoration: none;
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

            .options {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
<a href="mainpage.php" class="logo">FitFlex</a>

<div class="hero-container">
    <div class="hero-left">
        <div class="hero-content">
            <h1 class="hero-title">Welcome Back to FitFlex</h1>
            <p class="hero-text">Sign in to access your personalized workouts, track your progress, and continue your fitness journey with our community.</p>
        </div>
    </div>

    <div class="form-section">
        <div class="form-container">
            <h2 class="form-title">Member Login</h2>

            <?php if ($error_message): ?>
                <div class="error" style="text-align: center; margin-bottom: 1.5rem;"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" class="form-control" name="username" placeholder="Username" required
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>

                <div class="form-group password-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                    <span class="show-pass">
    <i class="fas fa-eye view"></i>
    <i class="fas fa-eye-slash hide"></i>
  </span>
                </div>


                <div class="options">
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="forgot_password.php">Forgot password?</a>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <div class="signup-link">
                <p>Don't have an account?</p>
                <a href="signup.php">Join FitFlex</a>
            </div>
        </div>
    </div>
</div>
<script>
    document.querySelectorAll('.show-pass').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const input = toggle.closest('.password-group').querySelector('input');
            const viewIcon = toggle.querySelector('.view');
            const hideIcon = toggle.querySelector('.hide');
            if (input.type === 'password') {
                input.type = 'text';
                viewIcon.style.display = 'none';
                hideIcon.style.display = 'block';
            } else {
                input.type = 'password';
                viewIcon.style.display = 'block';
                hideIcon.style.display = 'none';
            }
        });
    });
</script>


</body>
</html>