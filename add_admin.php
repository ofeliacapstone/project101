<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Power Admin') { header('Location: login.php'); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Admin</title>
  <link rel="stylesheet" href="style.css">
  <style>
  :root {
    --bg-color: #e0e0e0;
    --container-bg: #fff;
    --text-color: #333;
    --input-bg: #f9f9f9;
    --input-border: #ddd;
    --button-bg: linear-gradient(to right, #0056b3, #007bff);
    --button-text: #fff;
    --gold-accent: #FFD700;
    --light-blue: #ADD8E6;
  }

  body {
   
    font-family: sans-serif;
    margin: 0;
    padding: 0;
    background: var(--bg-color);
    color: var(--text-color);
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }
  body { font-family: sans-serif; margin: 0; padding: 0; background: var(--bg-color); color: var(--text-color); }

  .container {

    width: 90%;
    max-width: 800px;
    background-color: var(--container-bg);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: auto;
  }
  
  
  h2 {
    color: #0056b3;
    text-align: center;
    margin-bottom: 25px;
  }
  input[type="text"],
  input[type="email"],
  input[type="password"],
  input[type="date"],
  input[type="number"],
  select {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid var(--input-border);
    border-radius: 6px;
    background-color: var(--input-bg);
    color: var(--text-color);
  }
  button {
    background: var(--button-bg);
    color: var(--button-text);
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  button:hover {
    background: linear-gradient(to right, #007bff, #0056b3);
  }
  #darkModeToggle {
    position: fixed;
    top: 20px;
    right: 20px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 24px;
    color: #555;
  }
  .progress-tracker {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin-bottom: 25px;
  }
  .progress-tracker .step {
    background-color: #ddd;
    color: #333;
    padding: 12px 18px;
    border-radius: 25px;
    font-size: 15px;
    font-weight: 500;
    z-index: 1;
    transition: background-color 0.3s ease, color 0.3s ease;
  }
  .progress-tracker .step.active {
    background: linear-gradient(135deg, var(--gold-accent), #FFB300);
    color: #fff;
    box-shadow: 0 2px 5px rgba(255, 215, 0, 0.5);
  }
  .progress-bar {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 6px;
    background-color: #f0f0f0;
    transform: translateY(-50%);
    z-index: 0;
  }
  .progress-bar .progress {
    height: 100%;
    width: 0;
    background: linear-gradient(to right, var(--gold-accent), #FFB300);
    border-radius: 3px;
    transition: width 0.4s ease;
  }
  .form-step {
    display: none;
  }
  .form-step.active-step {
    display: block;
  }
  body.dark-mode {
    --bg-color: #121212;
    --container-bg: #1e1e1e;
    --text-color: #e0e0e0;
    --input-bg: #333;
    --input-border: #555;
    --button-bg: linear-gradient(to right, #003366, #002244);
  }
  body.dark-mode .container {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
  }
  body.dark-mode input[type="text"],
  body.dark-mode input[type="email"],
  body.dark-mode input[type="password"],
  body.dark-mode input[type="date"],
  body.dark-mode input[type="number"],
  body.dark-mode select {
    background-color: var(--input-bg);
    border-color: var(--input-border);
    color: var(--text-color);
  }
  body.dark-mode #darkModeToggle {
    color: #eee;
  }
  .error-input {
    border: 2px solid red;
    color: red;
  }
  .error-message {
    color: red;
    font-size: 0.9em;
    margin-top: -10px;
    margin-bottom: 10px;
  }
  @media (max-width: 768px) {
    .container {
      padding: 20px;
    }
    .progress-tracker .step {
      font-size: 13px;
      padding: 10px 15px;
    }
  }
</style>

  <link rel="stylesheet" href="assets/admin_theme.css">
</head>
<body>
  <button id="darkModeToggle">&#9788;</button>
  <?php include 'power_admin_header.php'; ?>
  <main class="main">
  <div class="container">
    <h2>Add New Admin</h2>  
    <div class="progress-tracker">
      <div class="step active" data-step="1">Personal</div>
      <div class="step" data-step="2">Contact</div>
      <div class="step" data-step="3">Family</div>
      <div class="step" data-step="4">Admin</div>
      <div class="progress-bar">
        <div class="progress"></div>
      </div>
    </div>
    <form id="adminForm" method="POST" action="process_add_admin.php">
      <!-- Step 1: Personal Information -->
      <div id="step1" class="form-step active-step" data-step="1">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>
        <span class="error-message" id="firstNameError"></span>
        <label for="middle_name">Middle Name:</label>
        <input type="text" id="middle_name" name="middle_name">
        <span class="error-message" id="middleNameError"></span>
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>
        <span class="error-message" id="lastNameError"></span>
        <label for="birth_date">Birth Date:</label>
        <input type="date" id="birth_date" name="birth_date" required>
        <span class="error-message" id="birthDateError"></span>
        <button type="button" class="next-step">Next</button>
      </div>
      <!-- Step 2: Contact Information -->
      <div id="step2" class="form-step" data-step="2">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <span class="error-message" id="emailError"></span>
        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" required>
        <span class="error-message" id="phoneError"></span>
        <label for="current_address">Current Address:</label>
        <input type="text" id="current_address" name="current_address" required>
        <span class="error-message" id="currentAddressError"></span>
        <label for="permanent_address">Permanent Address:</label>
        <input type="text" id="permanent_address" name="permanent_address" required>
        <span class="error-message" id="permanentAddressError"></span>
        <button type="button" class="previous-step">Previous</button>
        <button type="button" class="next-step">Next</button>
      </div>
      <!-- Step 3: Family Information -->
      <div id="step3" class="form-step" data-step="3">
        <label for="mother_name">Mother's Name:</label>
        <input type="text" id="mother_name" name="mother_name" required>
        <span class="error-message" id="motherNameError"></span>
        <label for="father_name">Father's Name:</label>
        <input type="text" id="father_name" name="father_name" required>
        <span class="error-message" id="fatherNameError"></span>
        <button type="button" class="previous-step">Previous</button>
        <button type="button" class="next-step">Next</button>
      </div>
      <!-- Step 4: Admin Information -->
      <div id="step4" class="form-step" data-step="4">
        <label for="unit">Unit:</label>
        <select id="unit" name="unit" required>
          <option value="Dormitory Admin">Dormitory</option>
          <option value="Guidance Admin">Guidance</option>
          <option value="Registrar Admin">Registrar</option>
          <option value="Scholarship Admin">Scholarship</option>
        </select>
        <span class="error-message" id="unitError"></span>
        <label for="user_id">User ID:</label>
<input type="text" id="user_id" name="user_id" required>
<span class="error-message" id="userIdError"></span>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <span class="error-message" id="passwordError"></span>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" required>
        <span class="error-message" id="confirmPasswordError"></span>
        <button type="button" class="previous-step">Previous</button>
        <button type="submit">Add Admin</button>
      </div>
    </form>
  </div>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
    const steps = document.querySelectorAll(".form-step");
    let currentStep = 0;                                                    
        
    function showStep(index) {
      steps.forEach((step, i) => {
        step.classList.toggle("active-step", i === index);
      });
      updateProgressTracker(index);
    }
    function updateProgressTracker(step) {
      document.querySelectorAll(".progress-tracker .step").forEach((element, index) => {
        element.classList.toggle("active", index <= step);
      });
      const progress = document.querySelector(".progress-bar .progress");
      if (progress) {
        progress.style.width = `${(step / (steps.length - 1)) * 100}%`;
      }
    }
    const unitSelect = document.getElementById("unit");
const userIdInput = document.getElementById("user_id");
if (unitSelect && userIdInput) {
  unitSelect.addEventListener("change", function () {
    userIdInput.value = ""; // Para hindi na mag-auto-generate, nililinis lang natin yung field
  });
}
    function attachValidation(selector, pattern, errorSelector, message) {
      const input = document.querySelector(selector);
      const errorElement = document.querySelector(errorSelector);
      if (input && errorElement) {
        input.addEventListener("input", function () {
          if (!pattern.test(input.value)) {
            errorElement.textContent = message;
            input.classList.add("error-input");
          } else {
            errorElement.textContent = "";
            input.classList.remove("error-input");
          }
        });
      }
    }
    attachValidation("#email", /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/, "#emailError", "Invalid email format.");
    attachValidation("#phone", /^[0-9]{10,}$/, "#phoneError", "Phone number must be at least 10 digits.");
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirm_password");
    const passwordError = document.getElementById("passwordError");
    const confirmPasswordError = document.getElementById("confirmPasswordError");
    if (passwordInput) {
      passwordInput.addEventListener("input", function () {
        passwordError.textContent = this.value.length < 8 ? "Password must be at least 8 characters." : "";
        this.classList.toggle("error-input", this.value.length < 8);
      });
    }
    if (confirmPasswordInput) {
      confirmPasswordInput.addEventListener("input", function () {
        confirmPasswordError.textContent = this.value !== passwordInput.value ? "Passwords do not match." : "";
        this.classList.toggle("error-input", this.value !== passwordInput.value);
      });
    }
    const darkModeToggle = document.getElementById("darkModeToggle");
    if (darkModeToggle) {
      darkModeToggle.addEventListener("click", function () {
        document.body.classList.toggle("dark-mode");
      });
    }
    showStep(currentStep);
    });
    document.querySelectorAll(".next-step").forEach(button => {
    button.addEventListener("click", function () {
      const currentStep = this.closest(".form-step");
      const currentStepIndex = parseInt(currentStep.dataset.step);
      const nextStep = document.getElementById(`step${currentStepIndex + 1}`);
      if (nextStep) {
        currentStep.classList.remove("active-step");
        nextStep.classList.add("active-step");
        updateProgressTracker(currentStepIndex + 1);
      }
    });
    });
    document.querySelectorAll(".previous-step").forEach(button => {
    button.addEventListener("click", function () {
      const currentStep = this.closest(".form-step");
      const currentStepIndex = parseInt(currentStep.dataset.step);
      const previousStep = document.getElementById(`step${currentStepIndex - 1}`);
      if (previousStep) {
        currentStep.classList.remove("active-step");
        previousStep.classList.add("active-step");
        updateProgressTracker(currentStepIndex - 1);
      }
    });
    });
</script>


  </main>
</body>
</html>