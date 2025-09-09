<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);

    // Fetch user email
    $query = "SELECT email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $email = $user['email'];
        $subject = "Payment Method Form";
        $message = "Dear Applicant,\n\nPlease fill out the following form to complete your payment process:\n\n[Payment Form Link]\n\nThank you.";
        $headers = "From: admin@dormitory.com";

        if (mail($email, $subject, $message, $headers)) {
            echo json_encode(['success' => true, 'message' => 'Payment form sent successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send payment form.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }
}
?>