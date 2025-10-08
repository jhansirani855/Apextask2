<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blog";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully!";
$errors = array();
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors [] = "Invalid email format.";
    }

    if (count($errors) === 0) {
        $sql = "SELECT id, username, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $errors[] = "Database prepare statement failed: " . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                /*
                if (password_verify($password, $user['password'])) {
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_id'] = $user['id']; 
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $errors[] = "Invalid email or password.";
                }
                $errors[] = "No user found with this email.";
            }
        
            $stmt->close();
        }
    }

    $conn->close();
}
?>
*/
if (md5($password) === $user['password']) {
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_id'] = $user['id']; 
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $errors[] = "Invalid email or password.";
                }
            } else {
                $errors[] = "No user found with this email.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            width: 100%;
            max-width: 400px;
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            animation: fadeInUp 1s ease-in-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            text-align: center;
            color: #8D58BF;
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: 600;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-group {
            box-sizing: border-box;
        }

        .form-group input {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 30px;
            box-sizing: border-box;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
            width: 100%;
        }

        .form-group input:focus {
            border-color: #8D58BF;
            box-shadow: 0 0 10px rgba(141, 88, 191, 0.1);
        }

        input[type="submit"] {
            display: inline-block;
            padding: 10px 20px;
            border: 1px solid #8D58BF;
            background-color: #fff;
            border-radius: 8px;
            font-size: 16px; 
            text-align: center;
            text-decoration: none;
            color: #8D58BF;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="submit"]:hover {
            background-color: #8D58BF;
            color: #fff;
        }

        a {
            text-align: center;
            color: #8D58BF;
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }

        a:hover {
            text-decoration: underline;
        }

        .error, .success {
            font-size: 14px;
            padding: 10px;
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form id="loginForm" action="login.php" method="post">
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
                <input type="email" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars(isset($_POST['email']) ? $_POST['email'] : '', ENT_QUOTES); ?>" required>
            </div>

            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <input type="submit" value="Login">

            <a href="register.php">Don't have an account? Register here</a>
        </form>
    </div>
</body>
</html>
