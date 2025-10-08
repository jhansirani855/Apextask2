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

$sql_user_role = "SELECT role FROM users WHERE username=?";
$stmt_user_role = $conn->prepare($sql_user_role);
$stmt_user_role->bind_param("s", $_SESSION['username']);
$stmt_user_role->execute();
$result_user_role = $stmt_user_role->get_result();
//$user_role = $result_user_role->fetch_assoc()['role'];
$user_role = array();
$stmt_user_role->close();

$sql_thoughts = "SELECT id, username, current_thought, thought_timestamp, role, profile_pic 
                 FROM users 
                 WHERE current_thought IS NOT NULL 
                 ORDER BY FIELD(role, 'admin', 'user') DESC, thought_timestamp DESC";
$result_thoughts = $conn->query($sql_thoughts);

if (!$result_thoughts) {
    die("Error fetching thoughts: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Thoughts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #8D58BF; 
            color: #333;
        }
        header {
            background: #ffffff;
            padding: 20px 10%;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        h1 {
            margin: 0;
            font-size: 32px; 
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 1400px;
            margin: 30px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        .thought {
            display: flex;
            align-items: flex-start;
            padding: 20px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 15px;
            background: #f9f9f9;
            border-radius: 10px;
            position: relative;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }
        .thought:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            transform: scale(1.02);
        }
        .thought img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 20px;
            object-fit: cover;
        }
        .thought p {
            margin: 0;
            font-size: 20px; 
            color: #555;
        }
        .thought p strong {
            color: #8D58BF;
            font-size: 20px; 
        }
        .thought p small {
            font-size: 14px; 
            color: #777;
        }
        .thought .admin-tag {
            display: inline-block;
            background-color: #e74c3c;
            color: #fff;
            padding: 2px 6px;
            font-size: 12px;
            margin-left: 10px;
            border-radius: 4px;
        }
        .thought form {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .thought button {
            background-color: transparent;
            border: none;
            cursor: pointer;
        }
        .thought button i {
            font-size: 20px;
            color: #e74c3c;
            transition: color 0.3s ease;
        }
        .thought button:hover i {
            color: #c0392b;
        }
        .btn-back {
            display: inline-block;
            padding: 12px 24px;
            border: 1px solid #8D58BF;
            background-color: #fff;
            border-radius: 8px;
            font-size: 20px; 
            text-align: center;
            text-decoration: none;
            color: #8D58BF;
            margin-top: 25px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #8D58BF;
            color: #fff;
        }
    </style>
</head>
<body>
    <header>
        <h1>View Thoughts</h1>
    </header>
    <div class="container">
        <div class="thoughts-list">
            <?php if ($result_thoughts->num_rows > 0): ?>
                <?php while ($row = $result_thoughts->fetch_assoc()): ?>
                    <?php 
                        $thought_timestamp = new DateTime($row['thought_timestamp']);
                        $formatted_date = $thought_timestamp->format('F j, Y \a\t g:i A');
                    ?>
                    <div class="thought">
                        <img src="<?php echo !empty($row['profile_pic']) ? htmlspecialchars($row['profile_pic']) : 'images/default-profile.png'; ?>" alt="Profile Picture">
                        <div>
                            <p>
                                <strong><?php echo htmlspecialchars($row['username']); ?></strong>
                                <?php if ($row['role'] === 'admin'): ?>
                                    <span class="admin-tag">Admin</span>
                                <?php endif; ?>
                            </p>
                            <p><?php echo htmlspecialchars($row['current_thought']); ?></p>
                            <p><small>Posted on: <?php echo htmlspecialchars($formatted_date); ?></small></p>

                            <?php if ($user_role === 'admin'): ?>
                                <form action="delete_t.php" method="post">
                                    <input type="hidden" name="thought_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit"><i class="fas fa-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No thoughts to display.</p>
            <?php endif; ?>
        </div>

        <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    </div>
</body>
</html>
