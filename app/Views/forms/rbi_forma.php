<?php
require 'vendor/fpdf186/fpdf.php';

class PDF extends FPDF
{
    public function __construct()
    {
        parent::__construct('L', 'mm', [215.9, 330.2]);
    }

    public function header()
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 10, 'RBI Form A (Revised 2024)', 0, 1, 'L');
    }

    public function footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 5);
        $this->Cell(0, 10, 'This is a computer-generated document, and may contain more than one page.', 0, 0, 'L');
        $this->Cell(0, 10, $this->PageNo(), 0, 0, 'C');
    }

    public function addHouseholdDetails($household, $members)
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 10, 'RECORDS OF BARANGAY INHABITANTS BY HOUSEHOLD', 0, 1, 'C');

        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 10, 'REGION:', 0, '0', 'L');
        $this->SetX(72);
        $this->Cell(0, 10, '__________________________________________', 0, 0, 'L');
        $this->SetX(72);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 10, $household['region'] ?? '', 0, 1, 'L');

        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 2, 'PROVINCE:', 0, '0', 'L');
        $this->SetX(72);
        $this->Cell(0, 2, '__________________________________________', 0, 0, 'L');
        $this->SetX(72);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 2, $household['province'] ?? '', 0, 1, 'L');

        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 10, 'CITY/MUNICIPALITY:', 0, '0', 'L');
        $this->SetX(72);
        $this->Cell(0, 10, '__________________________________________', 0, 0, 'L');
        $this->SetX(72);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 10, $household['city_municipality'] ?? '', 0, 1, 'L');

        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 2, 'BARANGAY:', 0, '0', 'L');
        $this->SetX(72);
        $this->Cell(0, 2, '__________________________________________', 0, 0, 'L');
        $this->SetX(72);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 2, $household['barangay_code'] . ' ' . $household['barangay_name'] ?? '', 0, 1, 'L');

        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 10, 'HOUSEHOLD ADDRESS:', 0, '0', 'L');
        $this->SetX(72);
        $this->Cell(0, 10, '__________________________________________', 0, 0, 'L');
        $this->SetX(72);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 10, $household['city_municipality'] ?? '', 0, 1, 'L');

        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 2, 'NO. OF HOUSEHOLD MEMBERS:', 0, '0', 'L');
        $this->SetX(72);
        $this->Cell(0, 2, '__________________________________________', 0, 0, 'L');
        $this->SetX(72);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 2, $household['city_municipality'] ?? '', 0, 1, 'L');
        $this->Ln(10);



        // ADD TABLE HERE
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(100, 5, 'NAME', 1, 0, 'C');
        $this->Cell(30, 20, 'Place of Birth', 1, 0, 'C');
        $this->Cell(25, 20, 'Date of Birth', 1, 0, 'C');
        $this->Cell(10, 20, 'Age', 1, 0, 'C');
        $this->Cell(10, 20, 'Sex', 1, 0, 'C');
        $this->Cell(25, 20, 'Civil Status', 1, 0, 'C');
        $this->Cell(30, 20, 'Citizenship', 1, 0, 'C');
        $this->Cell(40, 20, 'Occupation', 1, 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->MultiCell(40, 4, 'Indicate if Labor/employed, Unemployed, PWD, OFW, Solo Parent, Out of School Youth (OSY), Out of School Children (OSC) and/or IP', 1, 'C');

        // Sub-columns for Name
        $this->SetFont('Arial', 'B', 10);
        $this->SetX(10);
        $this->SetY(81);
        $this->Cell(25, 15, 'LAST NAME', 1, 0, 'C');
        $this->Cell(35, 15, 'FIRST NAME', 1, 0, 'C');
        $this->Cell(30, 15, 'MIDDLE NAME', 1, 0, 'C');
        $this->Cell(10, 15, 'EXT', 1, 0, 'C');

        // Populate table with actual data
        $this->SetFont('Arial', '', 8);
        $this->SetY(96);
        foreach ($members as $member) {
            $this->SetX(10);
            $this->Cell(25, 5, $member['last_name'], 1, 0, 'C');
            $this->Cell(35, 5, $member['first_name'], 1, 0, 'C');
            $this->Cell(30, 5, $member['middle_name'], 1, 0, 'C');
            $this->Cell(10, 5, $member['suffix'] ?? '', 1, 0, 'C');
            $this->Cell(30, 5, $member['place_of_birth'], 1, 0, 'C');
            $this->Cell(25, 5, $member['date_of_birth'], 1, 0, 'C');
            $this->Cell(10, 5, $member['age'], 1, 0, 'C');
            $this->Cell(10, 5, $member['sex'], 1, 0, 'C');
            $this->Cell(25, 5, $member['civil_status'], 1, 0, 'C');
            $this->Cell(30, 5, $member['citizenship'], 1, 0, 'C');
            $this->Cell(40, 5, $member['occupation'], 1, 0, 'C');
            $this->Cell(40, 5, $member['classification'], 1, 1, 'L');
        }
    }

    public function addSignatories($barangaySecretary, $punongBarangay, $headOfHousehold)
    {
        // Ensure the entire block stays together
        if ($this->GetY() + 60 > $this->PageBreakTrigger) {
            $this->AddPage();
        }
        $this->Ln(5);
        $this->SetFont('Arial', '', 11);
        $this->Cell(0, 10, 'Prepared by:', 0, 0, 'L');
        $this->SetX(125);
        $this->Cell(0, 10, 'Certified Correct:', 0, 0, 'L');
        $this->SetX(240);
        $this->Cell(0, 10, 'Validated by:', 0, 1, 'L');
        $this->Cell(1, 10, '____________________________________', 0, '0', 'L');
        $this->Cell(0, 10, '____________________________________', 0, '0', 'C');
        $this->Cell(0, 10, '____________________________________', 0, '0', 'R');
        $this->SetFont('Arial', 'B', 11);
        $this->SetX(48);
        $this->Cell(1, 10, strtoupper($headOfHousehold), 0, 0, 'C'); // Head of Household
        $this->SetX(163);
        $this->Cell(1, 10, strtoupper($barangaySecretary), 0, 0, 'C'); // Barangay Secretary
        $this->SetX(280);
        $this->Cell(1, 10, strtoupper($punongBarangay), 0, 1, 'C'); // Punong Barangay

        $this->SetFont('Arial', 'B', 11);
        $this->SetX(18);
        $this->Cell(0, 1, 'Name of Household/Head Member', 0, 0, 'L');

        $this->SetX(147);
        $this->Cell(0, 1, 'Barangay Secretary', 0, 0, 'L');

        $this->SetX(264);
        $this->Cell(0, 1, 'Punong Barangay', 0, 1, 'L');
        $this->SetFont('Arial', '', 11);
        $this->SetX(23);
        $this->Cell(0, 8, '(Signature over Printed Name)', 0, 0, 'L');

        $this->SetX(138);
        $this->Cell(0, 8, '(Signature over Printed Name)', 0, 0, 'L');

        $this->SetX(253);
        $this->Cell(0, 8, '(Signature over Printed Name)', 0, 1, 'L');

        $this->Ln(5);

        $this->SetFont('Arial', '', 8);
        $this->MultiCell(0, 3, 'I hereby certify that the above information are true and correct to the best of my knowledge. I understand that for the Barangay to carry out its mandate pursuant to Section 394 (d)(6) of the Local Government Code of 1991, they must necessarily process my personal information for easy identification of inhabitants, as a tool in planning, and as an updated reference in the number of inhabitants of the Barangay. Therefore, I grant my consent and recognize the authority of the Barangay to process my personal information, subject to the provision of the Philippine Data Privacy Act of 2012.', 0, 'J');
    }
}

// Get the file path from the saved filename
$filePath = 'public/forms/' . $filename;

// Generate PDF with actual data
$pdf = new PDF();
$pdf->AddPage();
$pdf->addHouseholdDetails($data['household'], $data['members']);
$pdf->addSignatories($data['barangaySecretary'], $data['punongBarangay'], $data['headOfHousehold']); // Pass the new data

// Save the PDF to the file system
$pdf->Output('F', $filePath);

// Also display the PDF to the user (optional)
$pdf->Output('I', 'RBI_Form_A.pdf');

// Display a success message to the user
echo "<div style='text-align: center; margin-top: 20px;'>";
echo "<h3>RBI Form A generated successfully!</h3>";
echo "<p>Your form has been saved as: <strong>$filename</strong></p>";
echo "<p>You can download it <a href='/$filePath' target='_blank'>here</a>.</p>";
echo "</div>";