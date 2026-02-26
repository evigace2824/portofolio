<?php
session_start();
require_once __DIR__ . '/includes/db.php';

// Only admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Validate ID
if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('Location: admin.php?tab=trainers');
    exit();
}
$id = (int)$_GET['id'];

// Delete any relations if needed (e.g., assigned classes)
// Delete trainer record
$stmt = $conn->prepare("DELETE FROM trainers WHERE trainer_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['success'] = "Trainer deleted successfully.";
} else {
    $_SESSION['error'] = "Trainer not found or could not be deleted.";
}
$stmt->close();

// Redirect back to trainers tab
header('Location: admin.php?tab=trainers');
exit();
?>
