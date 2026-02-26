<?php
session_start();
require_once __DIR__ . '/includes/db.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Must be admin (since agents don't exist in your schema)
if ($_SESSION['role'] !== 'admin') {
    header('Location: userpage.php');
    exit;
}

// Must have class id
if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('Location: admin.php');
    exit;
}
$classId = (int)$_GET['id'];

// First, delete any bookings associated with this class (due to foreign key constraints)
$stmt = $conn->prepare("
    DELETE FROM bookings
    WHERE class_id = ?
");
$stmt->bind_param('i', $classId);
$stmt->execute();
$stmt->close();

// Then delete any saved classes references
$stmt = $conn->prepare("
    DELETE FROM saved_classes
    WHERE class_id = ?
");
$stmt->bind_param('i', $classId);
$stmt->execute();
$stmt->close();

// Finally, delete the class itself
$stmt = $conn->prepare("
    DELETE FROM classes
    WHERE class_id = ?
");
$stmt->bind_param('i', $classId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['success'] = "Class deleted successfully.";
} else {
    $_SESSION['error'] = "Class not found or could not be deleted.";
}
$stmt->close();

header('Location: admin.php');
exit;
?>