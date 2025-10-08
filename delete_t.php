<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blog";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['thought_id'])) {
    $thought_id = $_POST['thought_id'];

    $sql = "UPDATE users SET current_thought = NULL WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $thought_id);

    if ($stmt->execute()) {
        header("Location: thought.php");
    } else {
        echo "Error deleting thought: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
