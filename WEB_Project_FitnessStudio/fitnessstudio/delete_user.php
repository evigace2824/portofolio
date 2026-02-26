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
    header('Location: admin.php?tab=users');
    exit();
}
$id = (int)$_GET['id'];

// Prevent deleting yourself
if ($id === $_SESSION['user_id']) {
    $_SESSION['error'] = "You cannot delete your own account.";
    header('Location: admin.php?tab=users');
    exit();
}

// Delete user
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['success'] = "User deleted successfully.";
} else {
    $_SESSION['error'] = "User not found or could not be deleted.";
}
$stmt->close();

header('Location: admin.php?tab=users');
exit();
