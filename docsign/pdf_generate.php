<?php

require_once('tcpdf/tcpdf.php');

// Create a new TCPDF instance
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator('Your Name');
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Sample PDF');
$pdf->SetSubject('Sample PDF Document');
$pdf->SetKeywords('PDF, Sample, DocuSign');

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', 'B', 12);

// Add some content
$pdf->Cell(0, 10, 'karido cyberpunk', 0, 1, 'C');

$localFilePath = 'C:\xampp/htdocs/Docsign/pdf/sample.pdf';


$pdf->Output($localFilePath, 'F');

echo 'PDF created successfully as "sample.pdf"';


?>