<?php
session_start();
require_once __DIR__ . '/includes/db.php';

// Check if user is logged in and has selected a class
if (!isset($_SESSION['user_id']) || !isset($_SESSION['selected_class_id'])) {
    header('Location: userpage.php');
    exit;
}

// Get class details
$classId = $_SESSION['selected_class_id'];
$class = [];
$stmt = $conn->prepare("SELECT class_id, name, category FROM classes WHERE class_id = ?");
$stmt->bind_param("i", $classId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $class = $result->fetch_assoc();
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - FitFlex</title>
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
        .payment-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            margin-top: 90px;
        }

        /* Payment Container */
        .payment-container {
            background: white;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            width: 100%;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .payment-container::before {
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

        .payment-container::after {
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

        .payment-header {
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
            z-index: 1;
        }

        .payment-header h2 {
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-size: 2.2rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
        }

        .payment-header p {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        .payment-icon {
            font-size: 3rem;
            color: var(--accent);
            margin-bottom: 1.5rem;
        }

        /* Class Info */
        .class-info {
            background: rgba(78, 205, 196, 0.1);
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            position: relative;
            z-index: 1;
            border-left: 5px solid var(--accent);
        }

        .class-icon {
            font-size: 2.5rem;
            color: var(--accent);
        }

        .class-details h3 {
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-weight: 700;
            font-size: 1.4rem;
        }

        .class-details p {
            color: var(--text-light);
        }

        /* Payment Form */
        .payment-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark);
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 0.9rem 1.2rem;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            background-color: var(--gray-light);
            border: none;
        }

        input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(78, 205, 196, 0.2);
        }

        /* Error Message */
        .alert-error {
            background: #ffebee;
            color: var(--primary-dark);
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid var(--primary-dark);
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 1;
        }

        .alert-error i {
            font-size: 1.2rem;
        }

        /* Submit Button */
        .btn-pay {
            grid-column: span 2;
            width: 100%;
            padding: 1.1rem;
            border: none;
            background: var(--gradient-primary);
            color: white;
            font-weight: 700;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.1rem;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .btn-pay:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        }

        /* Card Icons */
        .card-icons {
            display: flex;
            gap: 10px;
            margin-top: 0.5rem;
        }

        .card-icon {
            color: var(--text-light);
            font-size: 1.8rem;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .payment-main {
                padding: 1.5rem;
                margin-top: 80px;
            }

            .payment-container {
                padding: 2rem;
            }

            .payment-form {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: span 1;
            }

            .class-info {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .payment-header h2 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            nav {
                padding: 0 1.5rem;
            }

            .payment-container {
                padding: 1.5rem;
            }

            .payment-header h2 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav>
    <a href="mainpage.php" class="logo">FitFlex</a>
</nav>

<div class="payment-main">
    <div class="payment-container">
        <div class="payment-header">
            <div class="payment-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <h2>Complete Your Booking</h2>
            <p>Secure payment for your fitness class</p>
        </div>

        <?php if (!empty($_GET['error'])): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>Payment failed - please check your card details and try again</span>
            </div>
        <?php endif; ?>

        <div class="class-info">
            <div class="class-icon">
                <i class="fas fa-dumbbell"></i>
            </div>
            <div class="class-details">
                <h3><?= htmlspecialchars($class['name'] ?? 'Fitness Class') ?></h3>
                <p><?= htmlspecialchars($class['category'] ?? '') ?></p>
                <p>Class ID: #<?= htmlspecialchars($class['class_id'] ?? '') ?></p>
            </div>
        </div>

        <form method="POST" action="process_payment.php" class="payment-form">
            <div class="form-group full-width">
                <label>Card Number</label>
                <input name="card_number" type="text" maxlength="19" placeholder="4111 1111 1111 1111" pattern="[\d ]{13,19}" required>
                <div class="card-icons">
                    <i class="fab fa-cc-visa card-icon"></i>
                    <i class="fab fa-cc-mastercard card-icon"></i>
                    <i class="fab fa-cc-amex card-icon"></i>
                    <i class="fab fa-cc-discover card-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label>CVC</label>
                <input name="cvc" type="text" maxlength="3" placeholder="123" pattern="\d{3}" required>
            </div>

            <div class="form-group">
                <label>Expiry Month</label>
                <input name="expire_month" type="number" min="1" max="12" placeholder="MM" required>
            </div>

            <div class="form-group">
                <label>Expiry Year</label>
                <input name="expire_year" type="number" min="<?= date('Y') ?>" max="<?= date('Y') + 10 ?>" placeholder="YYYY" required>
            </div>

            <button type="submit" class="btn-pay">
                <i class="fas fa-lock"></i> Pay $49.99
            </button>
        </form>
    </div>
</div>

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

    // Format card number with spaces
    document.querySelector('input[name="card_number"]').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s+/g, '');
        if (value.length > 0) {
            value = value.match(new RegExp('.{1,4}', 'g')).join(' ');
        }
        e.target.value = value;
    });
</script>
</body>
</html>