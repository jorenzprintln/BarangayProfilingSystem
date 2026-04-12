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
        $imageWidth = 210;
        $imageHeight = 297;
        $x = ($this->w - $imageWidth) / 2;
        $y = ($this->h - $imageHeight) + 20;
        $this->Image('app/Views/forms/assets/doc_bg1.png', $x, $y, $imageWidth);

        $this->Image('app/Views/forms/assets/brgy_logo.png', 35, 20, 25.4, 25.4);
        $this->Image('app/Views/forms/assets/city_logo.png', $this->w - 60, 20, 25.4, 25.4);

        // Add header text
        $this->SetFont('Times', '', 13.5);
        $this->Cell(0, 1, 'Republic of the Philippines', 0, 1, 'C');
        $this->Cell(0, 10, 'Department of Interior of Local Government', 0, 1, 'C');

        $this->SetFont('Times', 'B', 13.5);
        $this->Cell(0, 1, 'OFFICE OF THE BARANGAY COUNCIL', 0, 1, 'C');

        $this->SetFont('Times', '', 13.5);
        $this->Cell(0, 10, 'Imelda Village, Barangay 36-A', 0, 1, 'C');
        $this->Cell(0, 1, 'Tacloban City', 0, 1, 'C');
        $this->Ln(10);
    }

}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Title - Bold and larger
$pdf->SetFont('Times', 'B', 14);
$pdf->Cell(0, 10, 'CERTIFICATION', 0, 1, 'C');
$pdf->Ln(5);

// "TO WHOM IT MAY CONCERN" - Regular
$pdf->SetFont('Times', '', 12);
$pdf->Cell(0, 10, 'TO WHOM IT MAY CONCERN:', 0, 1, 'L');
$pdf->Ln(5);

// Main paragraph with mixed formatting
$pdf->SetFont('Times', '', 12);
$pdf->Write(6, '          This is to certify that ');

// Name in BOLD and uppercase
$pdf->SetFont('Times', 'B', 12);
$pdf->Write(6, strtoupper($constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name'] . ' ' . ($constituent['suffix'] ?? '')));

// Continue with regular font
$pdf->SetFont('Times', '', 12);
$pdf->Write(6, ', ' . strtolower($constituent['sex']) . ', of legal age, ' . strtolower($constituent['civil_status']) . ', Filipino and a resident of Barangay 36-A Imelda Village, Tacloban City, Leyte is a ');

// "SOLO PARENT" in BOLD
$pdf->SetFont('Times', 'B', 12);
$pdf->Write(6, 'SOLO PARENT');

// Continue regular
$pdf->SetFont('Times', '', 12);
$pdf->Write(6, ' with the following dependents:');
$pdf->Ln(10);

// Dependents list - Regular font
$pdf->SetFont('Times', '', 12);
$pdf->SetX(30);
$pdf->MultiCell(0, 6, $dependents, 0, 'L');
$pdf->Ln(5);

// Purpose paragraph
$pdf->SetFont('Times', '', 12);
$pdf->Write(6, '          This certification is being issued upon the request of the above-named person for the purpose of ');

// Purpose in BOLD and uppercase
$pdf->SetFont('Times', 'B', 12);
$pdf->Write(6, strtoupper($purpose));

$pdf->SetFont('Times', '', 12);
$pdf->Write(6, '.');
$pdf->Ln(10);

// Date paragraph
$pdf->SetFont('Times', '', 12);
$pdf->Write(6, '          Given this ');

// Date in BOLD
$pdf->SetFont('Times', 'B', 12);
$pdf->Write(6, $date);

$pdf->SetFont('Times', '', 12);
$pdf->Write(6, ' at Barangay 36-A, Tacloban City, Leyte.');
$pdf->Ln(20);

// Signature Line - Bold and underlined for name
$pdf->SetFont('Times', 'UB', 12);
$pdf->Cell(0, 6, 'HON. ' . strtoupper($punongBarangay['full_name']), 0, 1, 'R');

// Title - Regular
$pdf->SetFont('Times', '', 12);
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