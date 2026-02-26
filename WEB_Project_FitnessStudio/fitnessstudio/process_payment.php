<?php
session_start();
require __DIR__ . '/includes/crypto.php';
require __DIR__ . '/includes/db.php';

// Check if user is logged in and has selected a class
if (!isset($_SESSION['user_id'], $_SESSION['selected_class_id'])) {
    header('Location: userpage.php');
    exit;
}

// Validate and sanitize input
$pan = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
$cvc = $_POST['cvc'] ?? '';
$mon = (int)($_POST['expire_month'] ?? 0);
$year = (int)($_POST['expire_year'] ?? 0);

// Basic validation
if (empty($pan) || empty($cvc) || $mon < 1 || $mon > 12 || $year < date('Y')) {
    header('Location: payment.php?error=invalid_input');
    exit;
}

// Encrypt the card number
$enc_pan = encrypt($pan);

// Look up in bank_accounts
$stmt = $conn->prepare("
  SELECT id
  FROM bank_accounts
  WHERE card_number = ?
  AND cvc = ?
  AND expire_month = ?
  AND expire_year = ?
  AND user_id = ?  -- Ensure the card belongs to the current user
");
$stmt->bind_param('ssisi', $enc_pan, $cvc, $mon, $year, $_SESSION['user_id']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    // If valid, insert into bookings
    $ins = $conn->prepare("
      INSERT INTO bookings (user_id, class_id, booked_at)
      VALUES (?, ?, NOW())
      ON DUPLICATE KEY UPDATE booked_at = NOW()
    ");
    $ins->bind_param('ii', $_SESSION['user_id'], $_SESSION['selected_class_id']);

    if ($ins->execute()) {
        // Remove from saved_classes if exists
        $del = $conn->prepare("
          DELETE FROM saved_classes
          WHERE user_id = ?
          AND class_id = ?
        ");
        $del->bind_param('ii', $_SESSION['user_id'], $_SESSION['selected_class_id']);
        $del->execute();
        $del->close();

        // Get class details for success message
        $classStmt = $conn->prepare("SELECT name FROM classes WHERE class_id = ?");
        $classStmt->bind_param('i', $_SESSION['selected_class_id']);
        $classStmt->execute();
        $class = $classStmt->get_result()->fetch_assoc();
        $classStmt->close();

        // Redirect with success message
        unset($_SESSION['selected_class_id']);
        $_SESSION['success'] = 'Payment successful! You\'re now booked for ' . htmlspecialchars($class['name'] ?? 'the class') . '.';
        header('Location: userpage.php');
        exit;
    } else {
        // Handle database error
        error_log("Booking failed: " . $conn->error);
        header('Location: payment.php?error=db_error');
        exit;
    }
} else {
    // If invalid, redirect back with error
    header('Location: payment.php?error=invalid_card');
    exit;
}
?>