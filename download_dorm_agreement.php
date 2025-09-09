<?php
session_start();
require_once 'config.php';
require_once __DIR__ . '/includes/DormAgreementService.php';

$service = new DormAgreementService($conn);
$agreementId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$agreement = $agreementId > 0 ? $service->getAgreementById($agreementId) : $service->getActiveAgreement();

if (!$agreement) {
    http_response_code(404);
    echo 'Agreement not found';
    exit;
}

require_once __DIR__ . '/fpdf182/fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,utf8_decode($agreement['title']),0,1,'C');
$pdf->Ln(4);
$pdf->SetFont('Arial','',11);

$content = $agreement['content'];
$lines = explode("\n", $content);
foreach ($lines as $line) {
    $pdf->MultiCell(0,6,utf8_decode($line));
}

$pdf->Output('D', 'Dormitory_Agreement.pdf');
exit;