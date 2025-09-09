<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agree_policy'])) {
    $_SESSION['dormitory_policy_accepted'] = true;
    // Redirect to the application form page, change filename if needed
    header('Location: apply_room_form.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dormitory Agreement Policy</title>
    <style>
        .policy-content {
            height: 300px;
            overflow-y: scroll;
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 15px;
            background: #f9f9f9;
        }
    </style>
    <script>
        window.onload = function() {
            var policyBox = document.getElementById('policyBox');
            var agreeCheckbox = document.getElementById('agree');
            var proceedBtn = document.getElementById('proceedBtn');
            policyBox.addEventListener('scroll', function() {
                if ((policyBox.scrollTop + policyBox.clientHeight) >= policyBox.scrollHeight) {
                    agreeCheckbox.disabled = false;
                }
            });
            agreeCheckbox.addEventListener('change', function() {
                proceedBtn.disabled = !this.checked;
            });
        }
    </script>
</head>
<body>
    <h2>Dormitory Agreement Policy</h2>
    <div class="policy-content" id="policyBox">
        <strong>Welcome to the [School Name] Dormitory.</strong><br>
        To ensure a safe, comfortable, and productive living environment, all residents must agree to the following terms:<br><br>
        <ol>
            <li><strong>Respect and Conduct</strong>
                <ul>
                    <li>All residents must show respect for fellow students, staff, and property at all times.</li>
                    <li>Violent, abusive, or disruptive behavior will not be tolerated.</li>
                </ul>
            </li>
            <li><strong>Cleanliness and Maintenance</strong>
                <ul>
                    <li>Residents are responsible for keeping their rooms and common areas clean.</li>
                    <li>Any damages must be reported immediately. Residents may be held liable for intentional damages.</li>
                </ul>
            </li>
            <li><strong>Curfew and Visitation</strong>
                <ul>
                    <li>Curfew hours must be strictly observed. Unauthorized visitors are not allowed.</li>
                </ul>
            </li>
            <li><strong>Prohibited Items and Activities</strong>
                <ul>
                    <li>The possession or use of illegal substances, alcohol, weapons, or hazardous materials is strictly prohibited.</li>
                    <li>Gambling and other unlawful activities are not allowed.</li>
                </ul>
            </li>
            <li><strong>Safety and Emergency</strong>
                <ul>
                    <li>Fire exits and equipment must not be tampered with.</li>
                    <li>In case of emergency, follow dormitory safety protocols.</li>
                </ul>
            </li>
            <li><strong>Privacy and Security</strong>
                <ul>
                    <li>Respect the privacy of others. Do not enter another resident’s room without permission.</li>
                </ul>
            </li>
            <li><strong>Compliance</strong>
                <ul>
                    <li>Violation of this policy may result in disciplinary action, including revocation of dormitory privileges.</li>
                </ul>
            </li>
        </ol>
        <br>
        By proceeding, you acknowledge that you have read, understood, and agree to abide by the Dormitory Agreement Policy.
        <br><em>Last Updated: June 2025</em>
    </div>
    <form method="post">
        <input type="checkbox" name="agree_policy" id="agree" disabled required>
        <label for="agree">I have read and agree to the Dormitory Agreement Policy.</label><br><br>
        <button type="submit" id="proceedBtn" disabled>Proceed to Application</button>
    </form>
</body>
</html>