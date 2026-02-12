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

    public function generateCertificate($bname, $tob, $bo, $location, $date, $punongBarangay)
    {
        $this->AddPage();
        $this->SetFont('Times', 'B', 16);
        $this->Cell(0, 10, 'C E R T I F I C A T E', 0, 1, 'C');
        $this->Ln(10); // Add a line break

        $this->SetFont('Times', '', 14);
        $this->Cell(0, 10, 'TO WHOM IT MAY CONCERN:', 0, 1, 'L');

        // Split the text and write with mixed formatting
        $this->Write(10, "           THIS IS TO CERTIFY THAT ");
        $this->SetFont('Times', 'B', 14); // Switch to bold
        $this->Write(10, '"' . strtoupper($bname) . '"');
        $this->SetFont('Times', '', 14); // Switch back to normal
        $this->Write(10, ", registered in the name of ");
        $this->SetFont('Times', 'B', 14); // Switch to bold
        $this->Write(10, strtoupper($bo));
        $this->SetFont('Times', '', 14); // Switch back to normal
        $this->Write(10, ", proprietor, located at ");
        $this->SetFont('Times', 'B', 14); // Switch to bold
        $this->Write(10, strtoupper($location));
        $this->Write(10, ", BRGY. 36-A, IMELDA VILLAGE, TACLOBAN CITY");
        $this->SetFont('Times', '', 14); // Switch back to normal
        $this->Write(10, ", has been operating ");
        $this->SetFont('Times', 'B', 14); // Switch to bold
        $this->Write(10, strtoupper($tob));
        $this->SetFont('Times', '', 14); // Switch back to normal        
        $this->Write(10, " business in this barangay.");
        $this->Ln(15); // Add a line break

        $this->Write(10, "           THIS CERTIFICATION is being issued upon the request of the interested party for whatever legal purpose it may serve.");


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
$pdf->generateCertificate($bname, $tob, $bo, $location, $date, $punongBarangay);

// Save the PDF to the file system
$pdf->Output('F', $filePath);

// Also display the PDF to the user (optional)
ob_end_clean();
$pdf->Output('I', 'Barangay_Business_Certificate.pdf');
exit;

// Display a success message to the user
echo "<div style='text-align: center; margin-top: 20px;'>";
echo "<h3>Certificate generated successfully!</h3>";
echo "<p>Your certificate has been saved as: <strong>$filename</strong></p>";
echo "<p>You can download it <a href='/$filePath' target='_blank'>here</a>.</p>";
echo "</div>";