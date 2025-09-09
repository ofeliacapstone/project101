@@ -1,257 +1,263 @@
<?php 
session_start(); // Start the session
require_once __DIR__ . '/csrf.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_services_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errorMessage = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $errorMessage = "Invalid request. Please refresh and try again.";
    } else {
    $userId = trim($_POST['user_id']);
    $password = $_POST['password'];
    $rememberMe = isset($_POST['remember_me']);

    // SQL query to fetch user information
    $stmt = $conn->prepare("SELECT password_hash, role FROM users WHERE user_id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($passwordHash, $role);
        $stmt->fetch();

        if (password_verify($password, $passwordHash)) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['role'] = $role;

            if ($rememberMe) {
                setcookie("user_id", $userId, time() + (86400 * 30), "/"); // Save user for 30 days
            }

            // Redirect based on user role
            switch ($role) {
                case 'Power Admin':
                    header("Location: admin_dashboard.php");
                    break;
                case 'Student':
                    header("Location: student_dashboard.php");
                    break;
                case 'Faculty':
                    header("Location: faculty_dashboard.php");
                    break;
                case 'Scholarship Admin':
                    header("Location: scholarship_admin_dashboard.php");
                    break;
                case 'Guidance Admin':
                    header("Location: guidance_admin_dashboard.php");
                    break;
                case 'Dormitory Admin':
                    header("Location: admin_dormitory_dashboard.php");
                    break;
                case 'Registrar Admin':
                    header("Location: registrar_dashboard.php");
                    break;
                default:
                    $errorMessage = "Unauthorized role.";
                    session_destroy();
                    exit();
            }
            exit();
        } else {
            $errorMessage = "Invalid password.";
        }
    } else {
        $errorMessage = "User ID does not exist.";
    }
    $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --primary-color: #0077b6;
            --secondary-color: #00b4d8;
            --accent-color: #90e0ef;
            --background-color: #f8f9fa;
            --text-color: #1a1a1a;
            --light-text-color: #ffffff;
            --error-color: #e63946;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('assets/neust.jpg') no-repeat center center/cover;
            transition: background 0.3s ease-in-out;
            color: var(--text-color);
        }

        .container {
            width: 400px;
            background: rgba(255, 255, 255, 0.7);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: all 0.3s;
            transform: translateY(10px);
            opacity: 0;
            animation: fadeSlide .45s ease forwards;
        }

        @keyframes fadeSlide { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .container:hover {
            transform: scale(1.02);
        }

        .icon {
            font-size: 60px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .input-group {
            position: relative;
            width: 100%;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }

        input:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        .toggle-password:hover {
            color: var(--primary-color);
        }

        .error {
            color: var(--error-color);
            font-size: 14px;
            margin-top: 10px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .remember-me input {
            width: auto;
            margin-right: 10px;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            border: none;
            background: var(--primary-color);
            color: var(--light-text-color);
            font-size: 16px;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.3s;
        }

        button:hover {
            background: var(--secondary-color);
        }

        .spinner {
            width: 16px; height: 16px; border: 2px solid rgba(255,255,255,.6); border-top-color: #fff; border-radius: 50%; display: inline-block; margin-right: 8px; animation: spin .8s linear infinite; vertical-align: -2px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="container">
        <i class="fas fa-user-circle icon"></i>
        <h2>Login</h2>
        <form method="POST" action="" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">
            <div class="input-group">
                <input type="text" name="user_id" placeholder="User ID" value="<?php echo isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : ''; ?>" required>
            </div>
            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="fas fa-eye toggle-password" id="togglePassword"></i>
            </div>
            <div class="remember-me">
                <input type="checkbox" name="remember_me" id="remember_me">
                <label for="remember_me">Remember Me</label>
            </div>
            <span class="error"><?php echo $errorMessage; ?></span>
            <button type="submit" id="loginBtn">Login</button>
        </form>
        <div style="margin-top:10px; display:flex; justify-content: space-between; align-items:center;">
            <a href="register.php">Create account</a>
            <a href="reset_password.php">Forgot password?</a>
        </div>
    </div>
    <script>
        // Loading state on submit
        document.getElementById('loginForm').addEventListener('submit', function(){
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span>Signing in...';
        });
        document.getElementById("togglePassword").addEventListener("click", function () {
            let passwordInput = document.getElementById("password");
            this.classList.toggle("fa-eye-slash");
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
        });
    </script>
</body>