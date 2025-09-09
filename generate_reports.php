<?php

include 'config.php'; 
session_start();
include 'guidance_admin_header.php';


// Function to generate report data
function generateReport($reportType, $startDate, $endDate) {
    global $conn;
    $reportData = [];

    switch ($reportType) {
        case 'guidance_requests':
            $query = "SELECT * FROM appointments WHERE appointment_date BETWEEN ? AND ?";
            break;
        case 'appointments':
            $query = "SELECT * FROM appointments WHERE appointment_date BETWEEN ? AND ?";
            break;
        case 'student_progress':
            $query = "SELECT students.*, performance.progress FROM students JOIN performance ON students.student_id = performance.student_id WHERE performance.date BETWEEN ? AND ?";
            break;
        case 'resource_usage':
            $query = "SELECT * FROM resource_access WHERE access_date BETWEEN ? AND ?";
            break;
        default:
            return $reportData;
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $reportData[] = $row;
    }

    return $reportData;
}

// Handle report generation request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reportType = $_POST['report_type'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    $reportData = generateReport($reportType, $startDate, $endDate);

    if (!empty($reportData)) {
        // Export report to PDF or CSV
        if (isset($_POST['export_pdf'])) {
            // Include PDF generation library
            require('fpdf/fpdf.php');

            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 12);

            // Add report title and date range
            $pdf->Cell(0, 10, 'Report: ' . ucfirst(str_replace('_', ' ', $reportType)), 0, 1, 'C');
            $pdf->Cell(0, 10, 'Date Range: ' . $startDate . ' to ' . $endDate, 0, 1, 'C');
            $pdf->Ln(10);

            // Add table headers
            $headers = array_keys($reportData[0]);
            foreach ($headers as $header) {
                $pdf->Cell(40, 10, ucfirst($header), 1);
            }
            $pdf->Ln();

            // Add table data
            foreach ($reportData as $dataRow) {
                foreach ($dataRow as $cell) {
                    $pdf->Cell(40, 10, $cell, 1);
                }
                $pdf->Ln();
            }

            $pdf->Output('D', 'report.pdf');
            exit();
        } elseif (isset($_POST['export_csv'])) {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename="report.csv"');

            $output = fopen('php://output', 'w');
            fputcsv($output, array_keys($reportData[0]));

            foreach ($reportData as $dataRow) {
                fputcsv($output, $dataRow);
            }

            fclose($output);
            exit();
        }
    } else {
        $error_message = "No data found for the selected criteria.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        

       

        .container {
            margin-left: 500px;
            
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333333;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin-bottom: 8px;
            text-align: left;
            width: 100%;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #dddddd;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-top: 15px;
        }
    </style>
</head>
<body>
   
    <div class="container">
        <h2>Generate Reports</h2>
        <form method="POST">
            <label for="report_type">Report Type:</label>
            <select id="report_type" name="report_type" required>
                <option value="guidance_requests">Guidance Requests</option>
                <option value="appointments">Appointments</option>
                <option value="student_progress">Student Progress</option>
                <option value="resource_usage">Resource Usage</option>
            </select>
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>
            <button type="submit" name="export_pdf">Export to PDF</button>
            <button type="submit" name="export_csv">Export to CSV</button>
        </form>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>