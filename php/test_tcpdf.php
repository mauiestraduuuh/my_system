<?php
require_once('../tcpdf/tcpdf.php');  // Correct relative path

// Create a new PDF document
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->Write(0, 'Hello, TCPDF!');
$pdf->Output('test.pdf', 'I'); // Display the PDF in the browser
?>
