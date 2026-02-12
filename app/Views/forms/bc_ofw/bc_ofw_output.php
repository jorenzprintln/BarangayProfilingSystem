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

    public function generateCertificate($fullName, $date, $punongBarangay)
    {
        $this->AddPage();
        $this->SetFont('Times', 'B', 16);
        $this->Cell(0, 10, 'C E R T I F I C A T I O N', 0, 1, 'C');
        $this->Ln(10);
        $this->SetFont('Times', '', 14);
        $this->Cell(0, 10, 'TO WHOM IT MAY CONCERN:', 0, 1, 'L');
        $this->MultiCell($this->w, 10, '', 0, 'J');
        $this->Write(10, '          THIS IS TO CERTIFY THAT ');
        $this->SetFont('Times', 'B', 14);
        $this->Write(10, strtoupper($fullName));
        $this->SetFont('Times', '', 14);
        $this->Write(10, ' IS STILL A RESIDENT OF BRGY 36-A IMELDA VILLAGE, TACLOBAN CITY. HOWEVER, HE IS CURRENTLY WORKING ABROAD (OFW).');

        $this->SetFont('Times', '', 14);

        $this->Ln(15); // Add a line break
        // Add the new content with the date
        $this->Write(10, "           ISSUED this ");
        $this->SetFont('Times', 'B', 14); // Switch to bold
        $this->Write(10, $date);
        $this->SetFont('Times', '', 14); // Switch back to normal
        $this->Write(10, " at the office of the Punong Barangay, Barangay 36-A, Tacloban City, Leyte.");

        $this->Ln(50); // Add a line break

        $this->SetFont('Times', 'UB', 14);
        $this->SetX($this->w - 100); // Indent the text
        $punongBarangayName = is_array($punongBarangay) ? strtoupper(implode(' ', $punongBarangay)) : strtoupper($punongBarangay);
        $this->Cell(0, 10, "HON. {$punongBarangayName}", 0, 0, 'R');
        $this->Ln(5); // Add a small line break
        $this->SetFont('Times', '', 14);
        $this->SetX($this->w - 100);
        $this->Cell(0, 10, 'Punong Barangay', 0, 0, 'R');

    }
}

// Get the file path from the saved filename
$filePath = 'public/forms/' . $filename;

// Generate the PDF using variables passed from the controller
$pdf = new PDF();
$pdf->generateCertificate($fullName, $date, $punongBarangay);

// Save the PDF to the file system
$pdf->Output('F', $filePath);

// Also display the PDF to the user (optional)
ob_end_clean();
$pdf->Output('I', 'Barangay_Certificate_OFW.pdf');
exit;

// Display a success message to the user
echo "<div style='text-align: center; margin-top: 20px;'>";
echo "<h3>Certificate generated successfully!</h3>";
echo "<p>Your certificate has been saved as: <strong>$filename</strong></p>";
echo "<p>You can download it <a href='/$filePath' target='_blank'>here</a>.</p>";
echo "</div>";