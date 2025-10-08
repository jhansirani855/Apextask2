<?php
/*
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

$errors = array();
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $dob = $_POST['dob'];
    $address = trim($_POST['address']);

    // Handle profile picture upload
    $profilePic = '';
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = $_FILES['profile_pic']['name'];
        $fileSize = $_FILES['profile_pic']['size'];
        $fileType = $_FILES['profile_pic']['type'];
        $fileNameCmps = explode('.', $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Validate file type and size
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = "Unsupported file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        } elseif ($fileSize > 2 * 1024 * 1024) { // 2MB limit
            $errors[] = "File size exceeds 2MB.";
        } else {
            $profilePic = 'uploads' . uniqid() . '.' . $fileExtension;
            if (!move_uploaded_file($fileTmpPath, $profilePic)) {
                $errors[] = "Error uploading the file.";
            }
        }
    }

    // Server-Side Validation
    if (empty($email) || empty($phone) || empty($dob) || empty($address)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone number must be 10 digits.";
    }

    if (count($errors) === 0) {
        $sql = "UPDATE users SET email=?, phone=?, dob=?, address=?, profile_pic=? WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $email, $phone, $dob, $address, $profilePic, $username);

        if ($stmt->execute()) {
            $successMessage = "User updated successfully!";
        } else {
            $errors[] = "Error updating user: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
} else {
    if (isset($_GET['username'])) {
        $username = $_GET['username'];

        $sql = "SELECT * FROM users WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();
    } else {
        $errors[] = "Username parameter is missing.";
        $user = null; 
    }
}
?>
*/
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

$errors = array();
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $dob = $_POST['dob'];
    $address = trim($_POST['address']);
    $profilePic = '';

    // Get existing profile_pic
    $sql_get_pic = "SELECT profile_pic FROM users WHERE username=?";
    $stmt_pic = $conn->prepare($sql_get_pic);
    $stmt_pic->bind_param("s", $username);
    $stmt_pic->execute();
    $result_pic = $stmt_pic->get_result();
    $row = $result_pic->fetch_assoc();
    $existingPic = $row['profile_pic'];
    $stmt_pic->close();

    // Handle new profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = $_FILES['profile_pic']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = "Unsupported file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        } else {
            $uploadDir = 'uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = $uploadDir . uniqid('profile_', true) . '.' . $fileExtension;
            if (move_uploaded_file($fileTmpPath, $newFileName)) {
                $profilePic = $newFileName;
            } else {
                $errors[] = "Error uploading the file.";
            }
        }
    } else {
        // Keep existing picture if no new one uploaded
        $profilePic = $existingPic;
    }

    // Validation
    if (empty($email) || empty($phone) || empty($dob) || empty($address)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone number must be 10 digits.";
    }

    if (count($errors) === 0) {
        $sql = "UPDATE users SET email=?, phone=?, dob=?, address=?, profile_pic=? WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $email, $phone, $dob, $address, $profilePic, $username);

        if ($stmt->execute()) {
            $successMessage = "✅ User updated successfully!";
        } else {
            $errors[] = "Error updating user: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
} else {
    // Fetch user data when opening the form
    if (isset($_GET['username'])) {
        $username = $_GET['username'];
        $sql = "SELECT * FROM users WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    } else {
        $errors[] = "Username parameter is missing.";
        $user = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #6D5BBA, #8D58BF);
            color: #fff;
        }

        .container {
            width: 90%;
            max-width: 400px; 
            margin: 0 auto;
            background-color: #fff;
            padding: 20px; 
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease-in-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            text-align: center;
            color: #8D58BF;
            margin-bottom: 10px;
            font-size: 18px;
            font-weight: 600;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 6px; 
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 4px; 
        }

        .form-group label {
            width: 100%;
            text-align: left;
            font-size: 12px;
            color: #333; 
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px; 
            border: 2px solid #ddd;
            border-radius: 25px;
            box-sizing: border-box;
            font-size: 14px; 
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus, .form-group textarea:focus {
            border-color: #8D58BF;
            box-shadow: 0 0 8px rgba(141, 88, 191, 0.1);
        }

        input[type="submit"] {
            cursor: pointer;
            background-color: #8D58BF;
            color: white;
            border: none;
            font-weight: 600;
            font-size: 14px;
            transition: background-color 0.3s ease;
            padding: 10px; 
            border-radius: 25px;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #6D5BBA;
        }

        a {
            text-align: center;
            color: #8D58BF;
            text-decoration: none;
            display: block;
            margin-top: 10px;
            font-size: 12px;
        }

        a:hover {
            text-decoration: underline;
        }

        .error, .success {
            font-size: 12px;
            padding: 8px; 
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        textarea {
            resize: none;
            height: 60px; 
        }

        .form-group input::placeholder, .form-group textarea::placeholder {
            color: #888;
            font-size: 14px; 
        }

        .form-group input[title], .form-group textarea[title] {
            position: relative;
        }

        .form-group input[title]::after, .form-group textarea[title]::after {
            content: attr(title);
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #333;
            color: #fff;
            padding: 5px;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
            display: none;
        }

        .form-group input[title]:hover::after, .form-group textarea[title]:hover::after {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Profile</h2>
        <form action="update.php" method="post" enctype="multipart/form-data">
            <!--
            <input type="hidden" name="username" value="<?php echo htmlspecialchars(isset($user['username']) ? $user['username'] : ''); ?>">
    -->
            <input type="hidden" name="username" value="<?php echo htmlspecialchars(isset($user['username']) ? $user['username'] : ''); ?>">
            <?php if (count($errors) > 0): ?>
                <div class="error">
                    <?php echo implode('<br>', $errors); ?>
                </div>
            <?php endif; ?>

            <?php if ($successMessage): ?>
                <div class="success">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars(isset($user['email']) ? $user['email'] : ''); ?>" placeholder="Enter your email" title="Please enter a valid email address" required>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars(isset($user['phone']) ? $user['phone'] : ''); ?>" placeholder="Enter your phone number" title="Phone number must be 10 digits" required>
            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars(isset($user['dob']) ? $user['dob'] : ''); ?>" title="Select your date of birth" required>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" placeholder="Enter your address" title="Provide your full address" required><?php echo htmlspecialchars(isset($user['address']) ? $user['address'] : ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="profile_pic">Profile Picture:</label>
                <input type="file" id="profile_pic" name="profile_pic">
            </div>

            <input type="submit" value="Update User">

            <a href="usermanager.php">Back to User Manager</a>
        </form>
    </div>
</body>
</html>
