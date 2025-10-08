<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username_db = "root";
$password = "";
$dbname = "blog";

$conn = new mysqli($servername, $username_db, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if username is provided
if (!isset($_GET['username']) || empty($_GET['username'])) {
    die("Error: No username specified for deletion.");
}

$username_to_delete = $_GET['username'];
$is_self_deletion = ($username_to_delete === $_SESSION['username']);

// Fetch current user's role (PHP 5.2 compatible)
$stmt_role = $conn->prepare("SELECT role FROM users WHERE username=?");
$stmt_role->bind_param("s", $_SESSION['username']);
$stmt_role->execute();
$stmt_role->bind_result($current_user_role);
$stmt_role->fetch();
$stmt_role->close();

// Only admin can delete others
if (!$is_self_deletion && $current_user_role !== 'admin') {
    die("Error: You do not have permission to delete other users.");
}

// Fetch profile picture (optional cleanup)
$profilePicPath = '';
$stmt_pic = $conn->prepare("SELECT profile_pic FROM users WHERE username=?");
$stmt_pic->bind_param("s", $username_to_delete);
$stmt_pic->execute();
$stmt_pic->bind_result($profilePicPath);
$stmt_pic->fetch();
$stmt_pic->close();

// Delete user
$stmt_del = $conn->prepare("DELETE FROM users WHERE username=?");
$stmt_del->bind_param("s", $username_to_delete);

if ($stmt_del->execute()) {
    if (!empty($profilePicPath) && file_exists($profilePicPath)) {
        unlink($profilePicPath);
    }

    $stmt_del->close();
    $conn->close();

    if ($is_self_deletion) {
        session_unset();
        session_destroy();
        header("Location: login.php?msg=account_deleted");
        exit();
    } else {
        header("Location: usermanager.php?msg=user_deleted");
        exit();
    }
} else {
    echo "Error deleting user: " . $stmt_del->error;
    $stmt_del->close();
    $conn->close();
}
?>
