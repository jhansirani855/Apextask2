<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST['content'];
    $username = $_SESSION['username'];

    $servername = "localhost";
    $username_db = "root";
    $password = "";
    $dbname = "blog"; 

    $conn = new mysqli($servername, $username_db, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO thoughts (username, content) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $content);

    if ($stmt->execute()) {
        header("Location: thought.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Thought</title>
</head>
<body>
    <form action="create_t.php" method="post">
        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea>
        <button type="submit">Create Thought</button>
    </form>
    <a href="thought.php">Back to Thoughts</a>
</body>
</html>
