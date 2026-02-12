<?php
if (ob_get_level() == 0) ob_start();
require 'vendor/fpdf186/fpdf.php';

class PDF extends FPDF
{
    public function __construct()
    {
        parent::__construct('P', 'mm', [215.9, 330.2]);
        // Set 1-inch margins (25.4 mm)
        $this->SetMargins(25.4, 25.4, 25.4);
    }

    public function Header()
    {
        // Center the background image on the page
        $imageWidth = 210; // Width of the image in mm
        $imageHeight = 297; // Height of the image in mm
        $x = ($this->w - $imageWidth) / 2;
        $y = ($this->h - $imageHeight) + 20;
        $this->Image('app/Views/forms/assets/doc_bg1.png', $x, $y, $imageWidth);


        $this->Image('app/Views/forms/assets/brgy_logo.png', 35, 20, 25.4, 25.4);

        // Add right logo with size set to 1 inch (25.4 mm)
        $this->Image('app/Views/forms/assets/city_logo.png', $this->w - 60, 20, 25.4, 25.4);

        // Add header text
        $this->SetFont('Arial', '', 13.5);
        $this->Cell(0, 1, 'Republic of the Philippines', 0, 1, 'C');
        $this->Cell(0, 10, 'Department of Interior of Local Government', 0, 1, 'C');

        $this->SetFont('Arial', 'B', 13.5);
        $this->Cell(0, 1, 'OFFICE OF THE BARANGAY COUNCIL', 0, 1, 'C');

        $this->SetFont('Arial', '', 13.5);
        $this->Cell(0, 10, 'Imelda Village, Barangay 36-A', 0, 1, 'C');
        $this->Cell(0, 1, 'Tacloban City', 0, 1, 'C');
        $this->Ln(10); // Add a line break
    }

}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'CERTIFICATION', 0, 1, 'C');
$pdf->Ln(5);
// Certificate Content
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'TO WHOM IT MAY CONCERN:', 0, 1, 'L');
$pdf->Ln(5);

$pdf->MultiCell(0, 6, 'This is to certify that ' . strtoupper($constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name']) . ', ' . strtolower($constituent['sex']) . ', of legal age, ' . strtolower($constituent['civil_status']) . ', Filipino and a resident of Barangay 36-A Imelda Village, Tacloban City, Leyte is a SOLO PARENT with the following dependents:', 0, 'J');
$pdf->Ln(5);

// List dependents
$pdf->SetX(30);
$pdf->MultiCell(0, 6, $dependents, 0, 'L');
$pdf->Ln(5);

$pdf->MultiCell(0, 6, 'This certification is being issued upon the request of the above-named person for the purpose of: ' . $purpose, 0, 'J');
$pdf->Ln(5);

$pdf->MultiCell(0, 6, 'Given this ' . $date . ' at Barangay Bolbok, Lipa City, Batangas, Philippines.', 0, 'J');
$pdf->Ln(20);

// Signature Line for Punong Barangay
$pdf->SetFont('Arial', 'UB', 12);
$pdf->Cell(0, 6, strtoupper($punongBarangay['full_name']), 0, 1, 'R');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 6, 'Punong Barangay', 0, 1, 'R');

// Save the PDF to file
$directory = 'public/forms';
if (!file_exists($directory)) {
    mkdir($directory, 0777, true);
}

$filePath = $directory . '/' . $filename;
$pdf->Output('F', $filePath);
ob_end_clean();
$pdf->Output('I', $filename);
exit;
?>