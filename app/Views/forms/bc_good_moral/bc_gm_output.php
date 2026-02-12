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

    public function goodMoralCertificate($constituent, $purpose, $date, $punongBarangay)
    {
        $this->AddPage();
        $this->SetFont('Times', 'B', 14);
        $this->Cell(0, 1, 'CERTIFICATE OF GOOD MORAL CHARACTER', 0, 1, 'C');
        $this->Ln(10);
        $this->SetFont('Times', '', 12);
        $this->Cell(0, 10, 'TO WHOM IT MAY CONCERN', 0, 1, 'L');
        $this->MultiCell($this->w, 10, '', 0, 'J');
        $this->Write(10, '           THIS IS TO CERTIFY that ');
        $this->SetFont('Times', 'B', 12);
        $this->Write(10, strtoupper($constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name']));
        $this->SetFont('Times', '', 12);
        $this->Write(10, ', of legal age, Filipino, is a bonafide resident of Barangay 36-A, Imelda Village, Tacloban City, Leyte, is personally known to me and is of ');
        $this->SetFont('Times', 'B', 12);
        $this->Write(10, 'GOOD MORAL CHARACTER');
        $this->SetFont('Times', '', 12);
        $this->Write(10, ', as of the date of this certificate.');
        $this->Ln(10);

        $this->Write(10, '          This certification is issued to serve as a valid requirement for the purpose of ');
        $this->SetFont('Times', 'B', 12);
        $this->Write(10, strtoupper($purpose));
        $this->SetFont('Times', '', 12);
        $this->Write(10, ' and should be used only for its intended purposes. Any misuse of this document is subject to appropriate legal action under existing laws.');
        $this->Ln(10);

        $this->Write(10, '            ISSUED this ');
        $this->SetFont('Times', 'B', 12);
        $this->Write(10, $date);
        $this->SetFont('Times', '', 12);
        $this->Write(10, ' at the office of the Punong Barangay, Barangay 36-A, Tacloban City, Leyte');

        $this->Ln(30);
        
        $this->SetFont('Times', 'UB', 12);
        $this->Cell(0, 1, $punongBarangay['full_name'] ?? '                                                                    ', 0, 1, 'L');
        $this->SetFont('Times', '', 12);
        $this->Cell(0, 10, 'Punong Barangay', 0, 1, 'L');
        
        
        
    }
}

// Get the file path from the saved filename
$filePath = 'public/forms/' . $filename;

$pdf = new PDF();
$pdf->goodMoralCertificate($constituent, $purpose, $date, $punongBarangay);

// Save the PDF to the file system
$pdf->Output('F', $filePath);

// Also display the PDF to the user (optional)
ob_end_clean();
$pdf->Output('I', 'Certificate of Good Moral Character.pdf');
exit;

// Display a success message to the user
echo "<div style='text-align: center; margin-top: 20px;'>";
echo "<h3>Certificate generated successfully!</h3>";
echo "<p>Your certificate has been saved as: <strong>$filename</strong></p>";
echo "<p>You can download it <a href='/$filePath' target='_blank'>here</a>.</p>";
echo "</div>";


