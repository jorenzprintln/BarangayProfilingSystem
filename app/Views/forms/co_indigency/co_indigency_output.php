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
        $imageWidth = 210;
        $imageHeight = 297;
        $x = ($this->w - $imageWidth) / 2;
        $y = ($this->h - $imageHeight) + 20;
        $this->Image('app/Views/forms/assets/doc_bg1.png', $x, $y, $imageWidth);
        $this->Image('app/Views/forms/assets/brgy_logo.png', 35, 20, 25.4, 25.4);
        $this->Image('app/Views/forms/assets/city_logo.png', $this->w - 60, 20, 25.4, 25.4);
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

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Times', 'I', 8);
        $this->Cell(0, 10, 'Not valid without the seal of the Barangay', 0, 1, 'R');
    }

    public function indigencyCertificate($constituent, $purpose, $age, $date, $punongBarangay)
    {
        $this->AddPage();
        $this->SetFont('Times', 'B', 14);
        $this->Cell(0, 1, 'C E R T I F I C A T E  O F  I N D I G E N C Y', 0, 1, 'C');
        $this->Ln(10);
        $this->SetFont('Times', '', 12);
        $this->Cell(0, 10, 'TO WHOM IT MAY CONCERN', 0, 1, 'L');
        $this->MultiCell($this->w, 10, '', 0, 'J');
        $this->Write(10, '           THIS IS TO CERTIFY that ');
        $this->SetFont('Times', 'B', 12);
        $this->Write(10, strtoupper($constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name'] . ' ' . ($constituent['suffix'] ?? '')));
        $this->SetFont('Times', '', 12);
        $this->Write(10, ', ');
        $this->Write(10, $age['age']);
        $this->Write(10, ' years old, ');
        $this->Write(10, $constituent['citizenship']);
        $this->Write(10, ', is a resident of Barangay 36-A, Imelda Village, Tacloban City, Leyte, is one of the bonafide residents in our barangay.');
        $this->Ln(10);
        $this->Write(10, '           THIS CERTIFICATION is being issued upon the request of the above-mentioned person for the purpose of ');
        $this->SetFont('Times', 'B', 12);
        $this->Write(10, strtoupper($purpose));
        $this->SetFont('Times', '', 12);
        $this->Ln(10);

        $this->Write(10, '           Issued this ');
        $this->SetFont('Times', 'B', 12);   
        $this->Write(10, $date);
        $this->SetFont('Times', '', 12);
        $this->Write(10, ' at the office of the Punong Barangay, Barangay 36-A, Tacloban City, Leyte.');

        $this->Ln(50);

        // FIXED: Extract and display Punong Barangay name on the right
        $punongBarangayName = '';
        if (is_array($punongBarangay) && isset($punongBarangay['full_name'])) {
            $punongBarangayName = strtoupper($punongBarangay['full_name']);
        } elseif (is_string($punongBarangay)) {
            $punongBarangayName = strtoupper($punongBarangay);
        }

        $this->SetFont('Times', 'UB', 12);
        $this->Cell(0, 6, 'HON. ' . $punongBarangayName, 0, 1, 'R');
        $this->SetFont('Times', '', 12);
        $this->Cell(0, 6, 'Punong Barangay', 0, 1, 'R');
    }
}

// Get the file path from the saved filename
$filePath = 'public/forms/' . $filename;

$pdf = new PDF();
$pdf->indigencyCertificate($constituent, $purpose, $age, $date, $punongBarangay);

// Save the PDF to the file system
$pdf->Output('F', $filePath);

// Also display the PDF to the user (optional)
ob_end_clean();
$pdf->Output('I', 'Certificate of Indigency.pdf');
exit;

// Display a success message to the user
echo "<div style='text-align: center; margin-top: 20px;'>";
echo "<h3>Certificate generated successfully!</h3>";
echo "<p>Your certificate has been saved as: <strong>$filename</strong></p>";
echo "<p>You can download it <a href='/$filePath' target='_blank'>here</a>.</p>";
echo "</div>";