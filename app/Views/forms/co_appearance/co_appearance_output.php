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

    public function generateCertificate($name, $location, $reason, $date, $punongBarangay)
    {
        $this->AddPage();
        $this->SetFont('Times', 'B', 16);
        $this->Cell(0, 10, 'C E R T I F I C A T E', 0, 1, 'C');
        $this->Cell(0, 10, 'O F', 0, 1, 'C');
        $this->Cell(0, 10, 'A P P E A R A N CE', 0, 1, 'C');
        $this->Ln(10); // Add a line break

        $this->SetFont('Times', '', 14);
        $this->Cell(0, 10, 'TO WHOM IT MAY CONCERN:', 0, 1, 'L');

        // Split the text and write with mixed formatting
        $this->Write(10, "           THIS IS TO CERTIFY THAT ");
        $this->SetFont('Times', 'B', 14); // Switch to bold
        $this->Write(10, strtoupper($name));
        $this->SetFont('Times', '', 14); // Switch back to normal
        $this->Write(10, " of ");
        $this->SetFont('Times', 'B', 14); // Switch to bold
        $this->Write(10, strtoupper($location));
        $this->SetFont('Times', '', 14); // Switch back to normal
        $this->Write(10, " has appeared before the Barangay Council of Imelda Village, Barangay 36-A, Tacloban City for the purpose of ");
        $this->SetFont('Times', 'B', 14); // Switch to bold
        $this->Write(10, strtoupper($reason));
        $this->SetFont('Times', '', 14); // Switch back to normal
        $this->Write(10, ".");
        $this->Ln(15); // Add a line break

        // Add the new content with the date
        $this->Write(10, "           ISSUED this ");
        $this->SetFont('Times', 'B', 14); // Switch to bold
        $this->Write(10, $date);
        $this->SetFont('Times', '', 14); // Switch back to normal
        $this->Write(10, " at the office of the Punong Barangay, Barangay 36-A, Tacloban City, Leyte.");

        $this->Ln(50); // Add a line break

        // FIXED: Extract full_name from the punongBarangay array
        $punongBarangayName = '';
        if (is_array($punongBarangay) && isset($punongBarangay['full_name'])) {
            $punongBarangayName = strtoupper($punongBarangay['full_name']);
        } elseif (is_string($punongBarangay)) {
            $punongBarangayName = strtoupper($punongBarangay);
        }

        $this->SetFont('Times', 'UB', 14);
        $this->SetX($this->w - 100); // Indent the text
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
$pdf->generateCertificate($name, $location, $reason, $date, $punongBarangay);

// Save the PDF to the file system
$pdf->Output('F', $filePath);

// Also display the PDF to the user (optional)
ob_end_clean();
$pdf->Output('I', 'Certificate_of_Appearance.pdf');
exit;