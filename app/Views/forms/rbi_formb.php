<?php

require 'vendor/fpdf186/fpdf.php';

class PDF extends FPDF
{
    public function __construct()
    {
        parent::__construct('P', 'mm', 'A4'); // Set page size to A4
    }

    public function header()
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, 'RBI Form B (Revised 2024)', 0, 1, 'L'); // Combined into one line
        $this->Cell(0, 10, 'INDIVIDUAL RECORDS OF BARANGAY INHABITANT', 0, 1, 'C'); // 2nd row
        $this->Ln(2);

        $this->SetFont('Arial', '', 8);
        $this->Cell(50, 11, '          REGION          :       _______________________________________     CITY/MUN      :      ________________________________________', 0, 0, 'L'); // 3rd row (centered)
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, '       VIII                                                              TACLOBAN CITY', 0, '1', 'L');
        $this->SetFont('Arial', '', 8);
        $this->Cell(50, 11, '          PROVINCE     :       ______________________________________     BARANGAY      :      ________________________________________', 0, 0, 'L'); // 3rd row (centered)
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, '    LEYTE                                                                      36-A', 0, '1', 'L');

        $this->Ln(5);
    }


    public function addConstituentDetails($constituent, $barangaySecretary, $household)
    {
        $this->SetFont('Arial', '', 10);

        // Start the box
        $this->SetDrawColor(0, 0, 0); // Black border
        $this->SetLineWidth(0.5);
        $this->Rect(10, $this->GetY(), 190, 230); // Adjust height (150) as needed

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, 'PERSONAL INFORMATION', 0, 1, 'C');

        $this->SetX(15); // Add padding inside the box
        $this->SetFont('Arial', '', 12);
        $this->SetLineWidth(0.2); // Set thin border line width
        $this->Cell(50, 6, htmlspecialchars($constituent['psn'] ?? ''), 1, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(60, 6, '(PhilSys Card No.)', 0, 1, 'C');
        $this->Ln(2);


        $this->SetX(15); // Add padding inside the box
        $this->SetFont('Arial', '', 12);
        $this->Cell(50, 6, htmlspecialchars($constituent['last_name']), 1, 0, 'C');
        $this->SetFont('Arial', '', 10);
        $this->setX(67);
        $this->SetFont('Arial', '', 12);
        $this->Cell(20, 6, htmlspecialchars($constituent['suffix'] ?? ''), 1, 0, 'C');
        $this->setX(89);
        $this->SetFont('Arial', '', 12);
        $this->Cell(60, 6, htmlspecialchars($constituent['first_name']), 1, 0, 'C');
        $this->setX(151);
        $this->SetFont('Arial', '', 12);
        $this->Cell(45, 6, htmlspecialchars($constituent['middle_name']), 1, 1, 'C');


        $this->SetFont('Arial', '', 10);
        $this->Cell(60, 6, '(Last Name)', 0, 0, 'C');
        $this->Cell(10, 6, '(Suffix, eg., Jr., I, II, III)', 0, 0, 'C');
        $this->Cell(77, 6, '(First Name)', 0, 0, 'C');
        $this->Cell(33, 6, '(Middle Name)', 0, 1, 'C');
        $this->Ln(2);


        $sex = substr($constituent['sex'], 0, 1);
        $this->SetX(15); // Add padding inside the box
        $this->SetFont('Arial', '', 12);
        $birthDate = date('m/d/Y', strtotime($constituent['birthdate']));
        $this->Cell(30, 6, htmlspecialchars($birthDate), 1, 0, 'C');
        $this->SetFont('Arial', '', 10);
        $this->setX(47);
        $this->SetFont('Arial', '', 12);
        $this->Cell(55, 6, htmlspecialchars($constituent['birthplace']), 1, 0, 'C');
        $this->setX(104);
        $this->SetFont('Arial', '', 12);
        $this->Cell(10, 6, htmlspecialchars($sex), 1, 0, 'C');
        $this->setX(116);
        $this->SetFont('Arial', '', 12);
        $this->Cell(30, 6, htmlspecialchars($constituent['civil_status']), 1, 0, 'C');
        $this->setX(148);
        $this->SetFont('Arial', '', 12);
        $this->Cell(48, 6, htmlspecialchars($constituent['religion']), 1, 1, 'C');


        $this->SetFont('Arial', '', 10);
        $this->Cell(40, 6, '(Birth Date: mm/dd/yyyy)', 0, 0, 'C');
        $this->Cell(55, 6, '(Birth Place)', 0, 0, 'C');
        $this->Cell(18, 6, '(Sex)', 0, 0, 'C');
        $this->Cell(27, 6, '(Civil Status)', 0, 0, 'C');
        $this->Cell(50, 6, '(Religion)', 0, 1, 'C');
        $this->Ln(2);


        $this->SetX(15); // Add padding inside the box
        $this->SetFont('Arial', '', 12);
        $this->Cell(131, 6, htmlspecialchars('36-A IMELDA VILLAGE, TACLOBAN CITY 6500, LEYTE'), 1, 0, 'C');
        $this->setX(148);
        $this->Cell(48, 6, $constituent['citizenship'] === '1' ? 'FILIPINO' : 'OTHERS', 1, 1, 'C');


        $this->SetFont('Arial', '', 10);
        $this->setX(60);
        $this->Cell(40, 6, '(Residence Address)', 0, 0, 'C');
        $this->setX(148);
        $this->Cell(50, 6, '(Citizenship)', 0, 1, 'C');
        $this->Ln(2);

        $this->SetX(15); // Add padding inside the box
        $this->SetFont('Arial', '', 12);
        $this->Cell(58, 6, htmlspecialchars($constituent['occupation']), 1, 0, 'C');
        $this->setX(75);
        $this->Cell(48, 6, htmlspecialchars($constituent['contact']), 1, 0, 'C');
        $this->setX(125);
        $this->Cell(71, 6, strtolower($constituent['email']), 1, 1, 'C');


        $this->SetFont('Arial', '', 10);
        $this->setX(25);
        $this->Cell(40, 6, '(Profession/Occupation)', 0, 0, 'C');

        $this->setX(79);
        $this->Cell(40, 6, '(Contact Number)', 0, 0, 'C');
        $this->setX(135);
        $this->Cell(50, 6, '(E-mail Address)', 0, 1, 'C');
        $this->Ln(2);


        $this->SetFont('Arial', '', 8);
        $this->Ln(5);
        $this->SetX(10);
        $this->Cell(50, 6, 'HIGHEST EDUCATIONAL ATTAINMENT:', 0, 0);

        // Checkboxes on the same line
        $this->SetX(65);
        $this->addCheckbox('ELEMENTARY', $constituent['education_attainment'] == '4', 22);
        $this->addCheckbox('HIGH SCHOOL', $constituent['education_attainment'] == '6', 22);
        $this->addCheckbox('COLLEGE', $constituent['education_attainment'] == '10', 16);
        $this->addCheckbox('POST GRAD', $constituent['education_attainment'] == '11', 20);
        $this->addCheckbox('VOCATIONAL', $constituent['education_attainment'] == '9', 22);
        $this->Ln(5); // Add line break after the row of checkboxes

        $this->SetX(80);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(50, 5, 'Please specify:', 0, 0);
        $this->SetX(100);
        $this->addCheckbox('Graduate', $constituent['is_graduate'] == '1', 15);
        $this->addCheckbox('Under Graduate', $constituent['is_graduate'] == '0', 0);
        $this->Ln(10);

        $this->SetFont('Arial', '', 9);
        $this->SetX(15);
        $this->MultiCell(180, 4, "I hereby certify that the above information is true and correct to the best of my knowledge. I understand that for the Barangay to carry out its mandate pursuant to Section 394 (d)(6) of the Local Government Code of 1991, they must necessarily process my personal information for easy identification of inhabitants, as a tool in planning, and as an updated reference in the number of inhabitants of the Barangay. Therefore, I grant my consent and recognize the authority of the Barangay to process my personal information, subject to the provision of the Philippine Data Privacy Act of 2012.", 0, 'J');
        $this->Ln(15);

        $this->SetX(25);
        $this->SetFont('Arial', '', 10);
        $this->Cell(50, 5, '_________________________                                   ______________________________________', 0, 0);
        $this->Cell(50, 5, '', 0, 0);

        $this->SetFont('Arial', 'B', 12);
        $this->SetX(25);
        $this->Cell(50, 4, date('F j, Y'), 0, 0, 'C');
        $this->SetX(121);
        $this->Cell(50, 4, strtoupper($constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name']), 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $this->SetX(25);
        $this->Cell(50, 5, 'Date Accomplished', 0, 0, 'C');
        $this->SetX(121);
        $this->Cell(50, 5, 'Name/Signature of Person Accomplishing the Form', 0, 0, 'C');
        $this->Ln(15);

        $this->SetX(10);
        $this->Cell(50, 5, 'Attested By:', 0, 0, 'C');


        // Add boxes for thumbmarks
        $this->SetX(25);
        $this->Cell(30, 58, '________________________________', 0, 0, 'L'); // 
        $this->SetFont('Arial', 'B', 12);
        $this->SetX(29);
        $secretaryName = $barangaySecretary
            ? strtoupper($barangaySecretary)
            : '';
        $this->Cell(50, 55, htmlspecialchars($secretaryName), 0, 0, 'C');
        $this->SetX(115);
        $this->SetFont('Arial', '', 10);
        $this->Cell(30, 30, '', 1, 0, 'R'); // Left box
        $this->SetX(150);
        $this->Cell(30, 30, '', 1, 1, 'R'); // Right box
        $this->SetX(39);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(30, 5, 'Barangay Secretary', 0, 0, 'C');



        $this->SetFont('Arial', '', 9);
        $this->SetX(105);
        $this->Cell(50, 5, 'Left Thumbmark', 0, 0, 'C');
        $this->SetX(140);
        $this->Cell(50, 5, 'Right Thumbmark', 0, 1, 'C');
        $this->Ln(15);

        $this->SetFont('Arial', 'B', 9);
        $this->SetX(25);
        $this->Cell(50, 7, 'Household Number:', 0, 0, 'L');
        $this->SetX(60);
        $this->Cell(50, 7, htmlspecialchars($household ?? ''), 1, 1, 'L');

        $this->SetFont('Arial', 'I', 8);
        $this->SetX(25);
        $this->Cell(50, 6, 'Note: The household number shall be filled up by the Barangay Secretary.');
    }

    private function addCheckbox($label, $checked, $widthAfter = 0)
    {
        // Draw checkbox with no line break (0 as last parameter)
        $this->Cell(5, 5, $checked ? '[X]' : '[ ]', 0, 0);
        $this->Cell($widthAfter, 5, $label, 0, 0);
    }
}

// Get the file path from the saved filename
$filePath = 'public/forms/' . $filename;

// Generate PDF with actual data
$pdf = new PDF();
$pdf->AddPage();
$pdf->addConstituentDetails($data['constituent'], $data['barangaySecretary'], $data['household']);

// Save the PDF to the file system
$pdf->Output('F', $filePath);

// Also display the PDF to the user (optional)
$pdf->Output('I', 'RBI_Form_B.pdf');

// Display a success message to the user
echo "<div style='text-align: center; margin-top: 20px;'>";
echo "<h3>RBI Form B generated successfully!</h3>";
echo "<p>Your form has been saved as: <strong>$filename</strong></p>";
echo "<p>You can download it <a href='/$filePath' target='_blank'>here</a>.</p>";
echo "</div>";