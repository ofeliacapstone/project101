<?php 
session_start();
require_once __DIR__ . '/csrf.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_services_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$successMessage = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $errors[] = "Invalid request. Please refresh and try again.";
    } else {
    $studentId = $_POST['user_id'];
    $firstName = $_POST['first_name'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $birthDate = $_POST['birthDate'];
    $nationality = $_POST['nationality'];
    $religion = $_POST['religion'];
    $biologicalSex = $_POST['biologicalSex'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $currentAddress = $_POST['currentAddress'];
    $permanentAddress = $_POST['permanentAddress'];
    $motherName = $_POST['motherName'];
    $motherWork = $_POST['motherWork'];
    $motherContact = $_POST['motherContact'];
    $fatherName = $_POST['fatherName'];
    $fatherWork = $_POST['fatherWork'];
    $fatherContact = $_POST['fatherContact'];
    $siblingsCount = $_POST['siblingsCount'];
     $rawPassword = $_POST['password'] ?? '';

    // Basic server-side validations
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Please enter a valid email address."; }
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $rawPassword)) { $errors[] = "Password must be 8+ chars with upper, lower, and a number."; }
    if (empty($studentId) || empty($firstName) || empty($lastName) || empty($birthDate) || empty($biologicalSex) || empty($year = $_POST['year']) || empty($section = $_POST['section']) || empty($course = $_POST['course'])) {
        $errors[] = "Please complete all required fields.";
    }
    $password = password_hash($rawPassword, PASSWORD_DEFAULT);

    $role = "Student"; // Always Student
    $year = $_POST['year'];
    $section = $_POST['section'];
    $course = $_POST['course'];
    $department = NULL;

    $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? OR email = ?");
    $checkStmt->bind_param("ss", $studentId, $email);
    $checkStmt->execute();
    $checkStmt->store_result();
    if (!$errors) {
        $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? OR email = ?");
        if ($checkStmt) {
            $checkStmt->bind_param("ss", $studentId, $email);
            $checkStmt->execute();
            $checkStmt->store_result();
            if ($checkStmt->num_rows > 0) {
                $errors[] = "User ID or Email already exists.";
            }
            $checkStmt->close();
        } else {
            $errors[] = "System error. Please try again later.";
        }
    }
      if (!$errors) {
        $stmt = $conn->prepare("INSERT INTO users 
        (user_id, first_name, middle_name, last_name, birth_date, nationality, religion, biological_sex, email, phone, current_address, permanent_address, role, year, section, course, department, password_hash, mother_name, mother_work, mother_contact, father_name, father_work, father_contact, siblings_count) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
         if ($stmt) {
            $stmt->bind_param("sssssssssssssssssssssssss", $studentId, $firstName, $middleName, $lastName, $birthDate, $nationality, $religion, $biologicalSex, $email, $phone, $currentAddress, $permanentAddress, $role, $year, $section, $course, $department, $password, $motherName, $motherWork, $motherContact, $fatherName, $fatherWork, $fatherContact, $siblingsCount);
            if ($stmt->execute()) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $errors[] = "Error saving record: " . $stmt->error;
            }
            $stmt->close();
        } else {
               $errors[] = "System error. Please try again later.";
        }

            }

              }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
             font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
             min-height: 100vh;
            background: linear-gradient(135deg, #003366 0%, #00B8D4 100%);
            margin: 0;
        }
        .container {
              width: 440px;
            background: rgba(255, 255, 255, 0.96);
            padding: 26px;
            border-radius: 14px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.25);
            animation: fadeIn .45s ease-in-out;
            border: 1px solid rgba(0,0,0,.06);
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
         .progress-bar { display: grid; grid-template-columns: repeat(4,1fr); gap: 8px; margin-bottom: 16px; }
        .step { height: 6px; background: rgba(2,44,86,.18); border-radius: 999px; overflow: hidden; position: relative; }
        .step.active::after, .step.done::after { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, #00B8D4, #003366); animation: fill .35s ease forwards; }
        @keyframes fill { from { transform: translateX(-100%);} to { transform: translateX(0);} }
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
        }
        .error { color: #b91c1c; font-size: 12px; display: none; margin-top: 4px; }
        .password-requirements { font-size: 12px; color: #6b7280; }
        .password-requirements span {
            display: block;
        }
          button { padding: 12px; margin-top: 10px; border: none; background: linear-gradient(135deg, #FFD166, #ffc84a); color: #1b2735; cursor: pointer; border-radius: 10px; transition: transform .15s ease, filter .2s ease; width: 100%; font-weight: 700; }
        button:hover { filter: brightness(1.05); transform: translateY(-1px); }
        input, select { width: 100%; padding: 12px; margin: 6px 0 2px; border: 1px solid #cfd8dc; border-radius: 10px; box-sizing: border-box; background: rgba(255,255,255,.98); font-size: 15px; }
        input:focus, select:focus { border-color: #00B8D4; outline: none; box-shadow: 0 0 0 4px rgba(0,184,212,.18); }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        p {
            text-align: center;
            margin-top: 20px;
        }
          p a { color: #003366; text-decoration: none; }
        p a:hover { text-decoration: underline; }
        .input-wrap{position:relative}
        .eye{position:absolute; right:12px; top:50%; transform:translateY(-50%); background:transparent; border:0; color:#6b7280; cursor:pointer}
        .eye:hover{color:#475569}
    </style>
</head>
<body>
    <div class="container">
        <div class="progress-bar">
            <div class="step active">1</div>
            <div class="step">2</div>
            <div class="step">3</div>
            <div class="step">4</div>
        </div>
        <form id="registrationForm" method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">
            <div class="form-step active">
                <h2>Personal Information</h2>
                <input type="text" id="studentId" name="user_id" placeholder="Student ID" required>
                <input type="text" id="firstName" name="first_name" placeholder="First Name" required>
                <input type="text" id="middleName" name="middleName" placeholder="Middle Name">
                <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
                <input type="date" id="birthDate" name="birthDate" required>
                <input type="text" id="nationality" name="nationality" placeholder="Nationality" required>
                <input type="text" id="religion" name="religion" placeholder="Religion" required>
                <select id="biologicalSex" name="biologicalSex" required>
                    <option value="">Select Biological Sex</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <!-- Role is always student -->
                <input type="hidden" id="role" name="role" value="Student">
                <div id="studentFields">
                    <input type="number" id="year" name="year" placeholder="Year" required>
                    <input type="text" id="section" name="section" placeholder="Section" required>
                    <input type="text" id="course" name="course" placeholder="Course" required>
                </div>
                <span class="error">All fields are required.</span>
                <button type="button" class="next">Next</button>
            </div>
            <div class="form-step">
                <h2>Contact Information</h2>
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="text" id="phone" name="phone" placeholder="Phone Number" required>
                <input type="text" id="currentAddress" name="currentAddress" placeholder="Current Address" required>
                <input type="text" id="permanentAddress" name="permanentAddress" placeholder="Permanent Address" required>
                <span class="error">All fields are required.</span>
                <button type="button" class="prev">Previous</button>
                <button type="button" class="next">Next</button>
            </div>
            <div class="form-step">
                <h2>Family Information</h2>
                <input type="text" id="motherName" name="motherName" placeholder="Mother's Name" required>
                <input type="text" id="motherWork" name="motherWork" placeholder="Mother's Work" required>
                <input type="text" id="motherContact" name="motherContact" placeholder="Mother's Contact Number" required>
                <input type="text" id="fatherName" name="fatherName" placeholder="Father's Name" required>
                <input type="text" id="fatherWork" name="fatherWork" placeholder="Father's Work" required>
                <input type="text" id="fatherContact" name="fatherContact" placeholder="Father's Contact Number" required>
                <input type="number" id="siblingsCount" name="siblingsCount" placeholder="Number of Siblings" required>
                <span class="error">All fields are required.</span>
                <button type="button" class="prev">Previous</button>
                <button type="button" class="next">Next</button>
            </div>
            <div class="form-step">
                <h2>Account Security</h2>
                 <div class="input-wrap">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <button type="button" class="eye fa-solid fa-eye" aria-label="Show password" data-eye="password"></button>
                </div>
                <div class="password-requirements">
                    <span>✔ Minimum 8 characters</span>
                    <span>✔ At least one uppercase letter</span>
                    <span>✔ At least one lowercase letter</span>
                    <span>✔ At least one number</span>
                </div>
                   <div class="input-wrap">
                    <input type="password" id="confirmPassword" placeholder="Confirm Password" required>
                    <button type="button" class="eye fa-solid fa-eye" aria-label="Show password" data-eye="confirmPassword"></button>
                </div>
                <span class="error" id="passwordMatchError">Passwords do not match.</span>
                <button type="button" class="prev">Previous</button>
                <button type="submit">Submit</button>
            </div>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let currentStep = 0;
            const formSteps = document.querySelectorAll(".form-step");
            const steps = document.querySelectorAll(".step");
            const nextButtons = document.querySelectorAll(".next");
            const prevButtons = document.querySelectorAll(".prev");
            
            function updateStep() {
                formSteps.forEach((step, index) => {
                    step.classList.toggle("active", index === currentStep);
                    steps[index].classList.toggle("active", index === currentStep);
                    steps[index].classList.toggle("done", index < currentStep);
                });
            }

            nextButtons.forEach(btn => btn.addEventListener("click", () => {
                if (currentStep < formSteps.length - 1) {
                    currentStep++;
                    updateStep();
                }
            }));
            
            prevButtons.forEach(btn => btn.addEventListener("click", () => {
                if (currentStep > 0) {
                    currentStep--;
                    updateStep();
                }
            }));
            
            document.getElementById("registrationForm").addEventListener("submit", function (e) {
                const password = document.getElementById("password").value;
                const confirmPassword = document.getElementById("confirmPassword").value;
                const passwordError = document.getElementById("passwordMatchError");
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
                
                if (!passwordRegex.test(password)) {
                    alert("Password must be at least 8 characters long and include uppercase, lowercase, and a number.");
                    e.preventDefault();
                }
                
                if (password !== confirmPassword) {
                    passwordError.style.display = "block";
                    e.preventDefault();
                } else {
                    passwordError.style.display = "none";
                }
            });

            // Show/hide password
            document.querySelectorAll('.eye').forEach(function(btn){
                btn.addEventListener('click', function(){
                    const id = btn.getAttribute('data-eye');
                    const input = document.getElementById(id);
                    if (!input) return;
                    input.type = input.type === 'password' ? 'text' : 'password';
                    btn.classList.toggle('fa-eye-slash');
                });
            });
        });
    </script>
</body>
</html>