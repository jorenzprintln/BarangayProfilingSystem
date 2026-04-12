<?php
error_reporting(0);

require 'vendor/fpdf186/fpdf.php';

class PDF extends FPDF
{
    public function __construct()
    {
        parent::__construct('P', 'mm', [215.9, 330.2]);

        $this->SetMargins(25.4, 25.4, 25.4);
    }

    public function family_information_record($ageCategories, $totalHouseholds, $totalFamilies, $totalRecentFamilies, $totalConstituentsWithSpecifiedClassification, $totalConstituentsByEducationAttainment, $totalConstituentsByOccupation, $punongBarangay, $generatedBy, $formData = [])
    { 
        // Initialize formData if not provided
        if (!is_array($formData)) {
            $formData = [];
        }

        $this->AddPage();
        $imageWidth = 210;
        $imageHeight = 297;
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

        $this->SetFont('Times', 'B', 12);
        $this->Cell(0, 1, 'FAMILY INFORMATION RECORD', 0, 1, 'C');

        $this->SetFont('Times', 'IUB', 12);
        $this->setX(80);
        $this->Cell(0, 10, 'Barangay 36-A', 0, 0, 'L');
        $this->setX(115);
        $this->Cell(0, 10, 'Year: 2025', 0, 1, 'L');

        // Set up table headers
        $this->SetFont('Times', 'B', 12);
        $this->Cell(60, 5, 'AGE RANGE', 1, 0, 'C');
        $this->Cell(35, 5, 'MALE', 1, 0, 'C');
        $this->Cell(35, 5, 'FEMALE', 1, 0, 'C');
        $this->Cell(35, 5, 'TOTAL', 1, 1, 'C');

        // Table data
        $this->SetFont('Times', '', 12);

        $totalMale = 0;
        $totalFemale = 0;
        $totalAll = 0;

        // Map the database age ranges to display format
        $ageRangeMap = [
            '0-4' => '0-4 yrs. old',
            '5-9' => '5-9 yrs. old',
            '10-14' => '10-14 yrs. old',
            '15-19' => '15-19 yrs. old',
            '20-24' => '20-24 yrs. old',
            '25-29' => '25-29 yrs. old',
            '30-34' => '30-34 yrs. old',
            '35-39' => '35-39 yrs. old',
            '40-44' => '40-44 yrs. old',
            '45-49' => '45-49 yrs. old',
            '50-54' => '50-54 yrs. old',
            '55-59' => '55-59 yrs. old',
            '60-64' => '60-64 yrs. old',
            '65-69' => '65-69 yrs. old',
            '70-74' => '70-74 yrs. old',
            '75-79' => '75-79 yrs. old',
            '80 and above' => '80 yrs. old and above'
        ];

        foreach ($ageRangeMap as $dbRange => $displayRange) {
            if (isset($ageCategories[$dbRange])) {
                $male = $ageCategories[$dbRange]['MALE'];
                $female = $ageCategories[$dbRange]['FEMALE'];
                $total = $ageCategories[$dbRange]['total'];

                $totalMale += $male;
                $totalFemale += $female;
                $totalAll += $total;

                $this->Cell(60, 5, $displayRange, 1, 0, 'L');
                $this->Cell(35, 5, $male, 1, 0, 'C');
                $this->Cell(35, 5, $female, 1, 0, 'C');
                $this->Cell(35, 5, $total, 1, 1, 'C');
            } else {
                // Fallback if data is missing
                $this->Cell(60, 5, $displayRange, 1, 0, 'L');
                $this->Cell(35, 5, 0, 1, 0, 'C');
                $this->Cell(35, 5, 0, 1, 0, 'C');
                $this->Cell(35, 5, 0, 1, 1, 'C');
            }
        }

        // Grand total row - use the TOTAL key from the data if available
        $this->SetFont('Times', 'B', 12);
        if (isset($ageCategories['TOTAL'])) {
            $totalMale = $ageCategories['TOTAL']['MALE'];
            $totalFemale = $ageCategories['TOTAL']['FEMALE'];
            $totalAll = $ageCategories['TOTAL']['total'];
        }

        $this->Cell(60, 5, 'GRAND TOTAL', 1, 0, 'L');
        $this->Cell(35, 5, $totalMale, 1, 0, 'C');
        $this->Cell(35, 5, $totalFemale, 1, 0, 'C');
        $this->Cell(35, 5, $totalAll, 1, 1, 'C');


        // Add some spacing
        $this->Ln(8);

        // Calculate dependency categories based on age data
        $youngDepMale = 0;
        $youngDepFemale = 0;
        $youngDepTotal = 0;

        $oldDepMale = 0;
        $oldDepFemale = 0;
        $oldDepTotal = 0;

        $workingMale = 0;
        $workingFemale = 0;
        $workingTotal = 0;

        // Young dependents (0-14)
        foreach (['0-4', '5-9', '10-14'] as $range) {
            if (isset($ageCategories[$range])) {
                $youngDepMale += $ageCategories[$range]['MALE'];
                $youngDepFemale += $ageCategories[$range]['FEMALE'];
                $youngDepTotal += $ageCategories[$range]['total'];
            }
        }

        // Old dependents (65+)
        foreach (['65-69', '70-74', '75-79', '80 and above'] as $range) {
            if (isset($ageCategories[$range])) {
                $oldDepMale += $ageCategories[$range]['MALE'];
                $oldDepFemale += $ageCategories[$range]['FEMALE'];
                $oldDepTotal += $ageCategories[$range]['total'];
            }
        }

        // Working population (15-64)
        foreach (['15-19', '20-24', '25-29', '30-34', '35-39', '40-44', '45-49', '50-54', '55-59', '60-64'] as $range) {
            if (isset($ageCategories[$range])) {
                $workingMale += $ageCategories[$range]['MALE'];
                $workingFemale += $ageCategories[$range]['FEMALE'];
                $workingTotal += $ageCategories[$range]['total'];
            }
        }

        // Set up dependency table headers
        $this->SetFont('Times', 'B', 12);
        $this->Cell(60, 5, 'CATEGORIES', 1, 0, 'C');
        $this->Cell(35, 5, 'MALE', 1, 0, 'C');
        $this->Cell(35, 5, 'FEMALE', 1, 0, 'C');
        $this->Cell(35, 5, 'TOTAL', 1, 1, 'C');

        // Table data
        $this->SetFont('Times', '', 12);

        // Print rows
        $this->Cell(60, 5, 'Young Dependents 0-14', 1, 0, 'L');
        $this->Cell(35, 5, $youngDepMale, 1, 0, 'C');
        $this->Cell(35, 5, $youngDepFemale, 1, 0, 'C');
        $this->Cell(35, 5, $youngDepTotal, 1, 1, 'C');

        $this->Cell(60, 5, 'Old Dependents 65-above', 1, 0, 'L');
        $this->Cell(35, 5, $oldDepMale, 1, 0, 'C');
        $this->Cell(35, 5, $oldDepFemale, 1, 0, 'C');
        $this->Cell(35, 5, $oldDepTotal, 1, 1, 'C');

        $this->Cell(60, 5, 'Working population 15-64', 1, 0, 'L');
        $this->Cell(35, 5, $workingMale, 1, 0, 'C');
        $this->Cell(35, 5, $workingFemale, 1, 0, 'C');
        $this->Cell(35, 5, $workingTotal, 1, 1, 'C');

        $this->SetFont('Times', 'B', 12);
        $this->Cell(60, 5, 'Total', 1, 0, 'L');
        $this->Cell(35, 5, $totalMale, 1, 0, 'C');
        $this->Cell(35, 5, $totalFemale, 1, 0, 'C');
        $this->Cell(35, 5, $totalAll, 1, 1, 'C');

        $totalDependentsMale = $youngDepMale + $oldDepMale;
        $totalDependentsFemale = $youngDepFemale + $oldDepFemale;
        $totalDependents = $youngDepTotal + $oldDepTotal;

        $this->Cell(60, 5, 'Total No. of Dependents', 1, 0, 'L');
        $this->Cell(35, 5, $totalDependentsMale, 1, 0, 'C');
        $this->Cell(35, 5, $totalDependentsFemale, 1, 0, 'C');
        $this->Cell(35, 5, $totalDependents, 1, 1, 'C');

        // Add some spacing
        $this->Ln(8);

        // Headers
        $this->SetFont('Times', 'B', 12);
        $this->Cell(60, 5, 'CATEGORIES', 1, 0, 'C');
        $this->Cell(35, 5, 'MALE', 1, 0, 'C');
        $this->Cell(35, 5, 'FEMALE', 1, 0, 'C');
        $this->Cell(35, 5, 'TOTAL', 1, 1, 'C');

        $this->SetFont('Times', '', 12);

        // Calculate youth data (15-24) from age categories
        $youthMale = 0;
        $youthFemale = 0;

        foreach (['15-19', '20-24'] as $range) {
            if (isset($ageCategories[$range])) {
                $youthMale += $ageCategories[$range]['MALE'];
                $youthFemale += $ageCategories[$range]['FEMALE'];
            }
        }
        $youthTotal = $youthMale + $youthFemale;

        // OSY row
        $osyMale = $totalConstituentsWithSpecifiedClassification['OSY']['MALE'];
        $osyFemale = $totalConstituentsWithSpecifiedClassification['OSY']['FEMALE'];
        $osyTotal = $totalConstituentsWithSpecifiedClassification['OSY']['total'];
        $this->Cell(60, 5, 'OSY', 1, 0, 'L');
        $this->Cell(35, 5, $osyMale, 1, 0, 'C');
        $this->Cell(35, 5, $osyFemale, 1, 0, 'C');
        $this->Cell(35, 5, $osyTotal, 1, 1, 'C');

        // 15-24 yrs old row - use actual data
        $this->Cell(60, 5, '15-24 yrs old', 1, 0, 'L');
        $this->Cell(35, 5, $youthMale, 1, 0, 'C');
        $this->Cell(35, 5, $youthFemale, 1, 0, 'C');
        $this->Cell(35, 5, $youthTotal, 1, 1, 'C');

        // PWD row
        $pwdMale = $totalConstituentsWithSpecifiedClassification['PWD']['MALE'];
        $pwdFemale = $totalConstituentsWithSpecifiedClassification['PWD']['FEMALE'];
        $pwdTotal = $totalConstituentsWithSpecifiedClassification['PWD']['total'];
        $this->Cell(60, 5, 'PWD', 1, 0, 'L');
        $this->Cell(35, 5, $pwdMale, 1, 0, 'C');
        $this->Cell(35, 5, $pwdFemale, 1, 0, 'C');
        $this->Cell(35, 5, $pwdTotal, 1, 1, 'C');

        // Senior Citizen row
        $scMale = $totalConstituentsWithSpecifiedClassification['SC']['MALE'];
        $scFemale = $totalConstituentsWithSpecifiedClassification['SC']['FEMALE'];
        $scTotal = $totalConstituentsWithSpecifiedClassification['SC']['total'];
        $this->Cell(60, 5, 'Senior Citizen', 1, 0, 'L');
        $this->Cell(35, 5, $scMale, 1, 0, 'C');
        $this->Cell(35, 5, $scFemale, 1, 0, 'C');
        $this->Cell(35, 5, $scTotal, 1, 1, 'C');

        // Solo Parent row
        $spMale   = $totalConstituentsWithSpecifiedClassification['SOLOPARENT']['MALE']   ?? 0;
        $spFemale = $totalConstituentsWithSpecifiedClassification['SOLOPARENT']['FEMALE'] ?? 0;
        $spTotal  = $totalConstituentsWithSpecifiedClassification['SOLOPARENT']['total']  ?? 0;
        $this->Cell(60, 5, 'Solo Parent', 1, 0, 'L');
        $this->Cell(35, 5, $spMale,   1, 0, 'C');
        $this->Cell(35, 5, $spFemale, 1, 0, 'C');
        $this->Cell(35, 5, $spTotal,  1, 1, 'C');

        // OSC row
        $oscMale   = $totalConstituentsWithSpecifiedClassification['OSC']['MALE']   ?? 0;
        $oscFemale = $totalConstituentsWithSpecifiedClassification['OSC']['FEMALE'] ?? 0;
        $oscTotal  = $totalConstituentsWithSpecifiedClassification['OSC']['total']  ?? 0;
        $this->Cell(60, 5, 'Out of School Children (OSC)', 1, 0, 'L');
        $this->Cell(35, 5, $oscMale,   1, 0, 'C');
        $this->Cell(35, 5, $oscFemale, 1, 0, 'C');
        $this->Cell(35, 5, $oscTotal,  1, 1, 'C');

        // IP row
        $ipMale   = $totalConstituentsWithSpecifiedClassification['IP']['MALE']   ?? 0;
        $ipFemale = $totalConstituentsWithSpecifiedClassification['IP']['FEMALE'] ?? 0;
        $ipTotal  = $totalConstituentsWithSpecifiedClassification['IP']['total']  ?? 0;
        $this->Cell(60, 5, 'Indigenous People (IP)', 1, 0, 'L');
        $this->Cell(35, 5, $ipMale,   1, 0, 'C');
        $this->Cell(35, 5, $ipFemale, 1, 0, 'C');
        $this->Cell(35, 5, $ipTotal,  1, 1, 'C');

        // OFW row
        $ofwMale   = $totalConstituentsWithSpecifiedClassification['OFW']['MALE']   ?? 0;
        $ofwFemale = $totalConstituentsWithSpecifiedClassification['OFW']['FEMALE'] ?? 0;
        $ofwTotal  = $totalConstituentsWithSpecifiedClassification['OFW']['total']  ?? 0;
        $this->Cell(60, 5, 'OFW', 1, 0, 'L');
        $this->Cell(35, 5, $ofwMale,   1, 0, 'C');
        $this->Cell(35, 5, $ofwFemale, 1, 0, 'C');
        $this->Cell(35, 5, $ofwTotal,  1, 1, 'C');

        // Unemployed row
        $unempMale   = $totalConstituentsWithSpecifiedClassification['UNEMP']['MALE']   ?? 0;
        $unempFemale = $totalConstituentsWithSpecifiedClassification['UNEMP']['FEMALE'] ?? 0;
        $unempTotal  = $totalConstituentsWithSpecifiedClassification['UNEMP']['total']  ?? 0;
        $this->Cell(60, 5, 'Unemployed', 1, 0, 'L');
        $this->Cell(35, 5, $unempMale,   1, 0, 'C');
        $this->Cell(35, 5, $unempFemale, 1, 0, 'C');
        $this->Cell(35, 5, $unempTotal,  1, 1, 'C');

        // Labor/Employed row
        $laborMale   = $totalConstituentsWithSpecifiedClassification['LABOR']['MALE']   ?? 0;
        $laborFemale = $totalConstituentsWithSpecifiedClassification['LABOR']['FEMALE'] ?? 0;
        $laborTotal  = $totalConstituentsWithSpecifiedClassification['LABOR']['total']  ?? 0;
        $this->Cell(60, 5, 'Labor/Employed', 1, 0, 'L');
        $this->Cell(35, 5, $laborMale,   1, 0, 'C');
        $this->Cell(35, 5, $laborFemale, 1, 0, 'C');
        $this->Cell(35, 5, $laborTotal,  1, 1, 'C');

        // Student row — sourced from occupation counts
        $studentMale   = $totalConstituentsByOccupation['Student']['MALE']   ?? 0;
        $studentFemale = $totalConstituentsByOccupation['Student']['FEMALE'] ?? 0;
        $studentTotal  = $totalConstituentsByOccupation['Student']['total']  ?? 0;
        $this->Cell(60, 5, 'Student', 1, 0, 'L');
        $this->Cell(35, 5, $studentMale,   1, 0, 'C');
        $this->Cell(35, 5, $studentFemale, 1, 0, 'C');
        $this->Cell(35, 5, $studentTotal,  1, 1, 'C');

        // Add some spacing
        $this->Ln(8);

        // Headers for Pregnant table
        $this->SetFont('Times', 'B', 12);
        $this->Cell(60, 5, 'PREGNANT', 1, 0, 'C');
        $this->Cell(52.5, 5, 'FEMALE', 1, 0, 'C');
        $this->Cell(52.5, 5, 'TOTAL', 1, 1, 'C');

        $this->SetFont('Times', '', 12);

        $teen1Female = $formData['pregnant_10_14'] ?? 0;
        $this->Cell(60, 5, '10-14 yrs. old', 1, 0, 'L');
        $this->Cell(52.5, 5, $teen1Female, 1, 0, 'C');
        $this->Cell(52.5, 5, $teen1Female, 1, 1, 'C');

        $teen2Female = $formData['pregnant_15_19'] ?? 0;
        $this->Cell(60, 5, '15-19 yrs. old', 1, 0, 'L');
        $this->Cell(52.5, 5, $teen2Female, 1, 0, 'C');
        $this->Cell(52.5, 5, $teen2Female, 1, 1, 'C');

        $adultFemale = $formData['pregnant_20_above'] ?? 0;
        $this->Cell(60, 5, '20-yrs old above', 1, 0, 'L');
        $this->Cell(52.5, 5, $adultFemale, 1, 0, 'C');
        $this->Cell(52.5, 5, $adultFemale, 1, 1, 'C');

        $this->Ln(8);

        // Headers for Categories table
        $this->SetFont('Times', 'B', 12);
        $this->Cell(120, 5, 'CATEGORIES', 1, 0, 'C');
        $this->Cell(45, 5, 'TOTAL', 1, 1, 'C');

        $this->SetFont('Times', '', 12);

        $this->Cell(120, 5, 'Total Population', 1, 0, 'L');
        $this->Cell(45, 5, number_format($totalAll), 1, 1, 'C');

        $this->Cell(120, 5, 'Total No. of Household', 1, 0, 'L');
        $this->Cell(45, 5, number_format($totalHouseholds ?? 0), 1, 1, 'C');

        $this->Cell(120, 5, 'Total No. of Families', 1, 0, 'L');
        $this->Cell(45, 5, number_format($totalFamilies ?? 0), 1, 1, 'C');

        $landArea = 8.6;
        $this->Cell(120, 5, 'Land Area (Imelda Village and JPIC Terminal)', 1, 0, 'L');
        $this->Cell(45, 5, "$landArea ha.", 1, 1, 'C');

        $density = round($totalAll / floatval($landArea), 2);
        $this->Cell(120, 5, 'Population Density', 1, 0, 'L');
        $this->Cell(45, 5, number_format($density, 2) . '/ha.', 1, 1, 'C');

        $this->Cell(120, 5, 'No. of Families Residing 5 yrs. and below', 1, 0, 'L');
        $this->Cell(45, 5, number_format($totalRecentFamilies ?? 0), 1, 1, 'C');

        $this->Ln(8);

        // Headers for Educational Level table
        $this->SetFont('Times', 'B', 12);
        $this->Cell(55, 10, '', 1, 0, 'C');
        $this->Cell(36, 10, 'MALE', 1, 0, 'C');
        $this->Cell(36, 10, 'FEMALE', 1, 0, 'C');
        $this->Cell(38, 10, 'TOTAL', 1, 1, 'C');

        $startY = $this->GetY();
        $this->SetY($startY - 10);
        $this->MultiCell(55, 5, "HIGHEST\nEDUCATIONAL LEVEL", 0, 'C');
        $this->SetY($startY);

        $this->SetFont('Times', '', 12);

        $levels = [
            'Daycare',
            'Nursery',
            'Kinder',
            'Elementary Level',
            'Elementary Graduate',
            'ALS',
            'High School Level',
            'High School Graduate',
            'Junior High School Level',
            'Junior High School Graduate',
            'Senior High School Level',
            'Senior High School Graduate',
            'Vocational',
            'College Level',
            'College Graduate',
            'Post Graduate',
        ];

        $totalMale = 0;
        $totalFemale = 0;

        foreach ($levels as $level) {
            $male = $totalConstituentsByEducationAttainment[$level]['MALE'];
            $female = $totalConstituentsByEducationAttainment[$level]['FEMALE'];
            $total = $male + $female;
            $totalMale += $male;
            $totalFemale += $female;

            $this->Cell(55, 5, $level, 1, 0, 'L');
            $this->Cell(36, 5, $male, 1, 0, 'C');
            $this->Cell(36, 5, $female, 1, 0, 'C');
            $this->Cell(38, 5, number_format($total), 1, 1, 'C');
        }

        $this->SetFont('Times', 'B', 12);
        $this->Cell(55, 5, 'Grand Total', 1, 0, 'L');
        $this->Cell(36, 5, number_format($totalMale), 1, 0, 'C');
        $this->Cell(36, 5, number_format($totalFemale), 1, 0, 'C');
        $this->Cell(38, 5, number_format($totalMale + $totalFemale), 1, 1, 'C');

        $this->Ln(8);

        // Occupation Table
        $this->SetFont('Times', 'B', 12);
        $this->Cell(55, 6, 'OCCUPATION', 1, 0, 'C');
        $this->Cell(36, 6, 'MALE', 1, 0, 'C');
        $this->Cell(36, 6, 'FEMALE', 1, 0, 'C');
        $this->Cell(38, 6, 'TOTAL', 1, 1, 'C');

        $this->SetFont('Times', '', 12);

        // ── UPDATED: added Student and Homemaker/Housewife ──
        $occupations = [
            'Government Employee',
            'Private Employee',
            'Barangay Official',
            'Barangay Volunteers',
            'OFW',
            'Business',
            'Carpenter',
            'Laborer/Construction',
            'Driver',
            'Sari-Sari Store',
            'Self-Employed',
            'Student',
            'Homemaker/Housewife',
        ];

        $totalMale = 0;
        $totalFemale = 0;

        foreach ($occupations as $occupation) {
            $male   = $totalConstituentsByOccupation[$occupation]['MALE']   ?? 0;
            $female = $totalConstituentsByOccupation[$occupation]['FEMALE'] ?? 0;
            $total  = $male + $female;
            $totalMale   += $male;
            $totalFemale += $female;

            $this->Cell(55, 5, $occupation, 1, 0, 'L');
            $this->Cell(36, 5, $male,   1, 0, 'C');
            $this->Cell(36, 5, $female, 1, 0, 'C');
            $this->Cell(38, 5, number_format($total), 1, 1, 'C');
        }

        $this->SetFont('Times', 'B', 12);
        $this->Cell(55, 5, 'Grand Total', 1, 0, 'L');
        $this->Cell(36, 5, number_format($totalMale), 1, 0, 'C');
        $this->Cell(36, 5, number_format($totalFemale), 1, 0, 'C');
        $this->Cell(38, 5, number_format($totalMale + $totalFemale), 1, 1, 'C');

        $this->Ln(5);

        $this->SetFont('Times', '', 12);
        $this->Cell(55, 5, 'Family Planning ACCEPTORS', 0, 0, 'L');

        $this->Ln(8);

        // Modern Methods Table
        $this->SetFont('Times', 'B', 12);
        $this->Cell(55, 6, '', 'TL', 0, 'L');
        $this->Cell(55, 6, 'METHOD', 1, 0, 'C');
        $this->Cell(55, 6, 'CURRENT USERS', 1, 1, 'C');

        $this->SetFont('Times', '', 12);
        $modernMethods = [
            'FS'         => $formData['fp_fs']         ?? 0,
            'MS'         => $formData['fp_ms']         ?? 0,
            'IUD'        => $formData['fp_iud']        ?? 0,
            'PILL'       => $formData['fp_pill']       ?? 0,
            'INJECTABLE' => $formData['fp_injectable'] ?? 0,
            'IMPLANT'    => $formData['fp_implant']    ?? 0,
            'CONDOM'     => $formData['fp_condom']     ?? 0
        ];

        $isFirst = true;
        foreach ($modernMethods as $method => $users) {
            $this->Cell(55, 6, ($isFirst) ? 'MODERN' : '', 'LR', 0, 'C');
            $this->Cell(55, 6, $method, 1, 0, 'L');
            $this->Cell(55, 6, $users, 1, 1, 'C');
            if ($isFirst) $isFirst = false;
        }
        $this->Cell(55, 0, '', 'T', 0, 'L');
        $this->Cell(110, 0, '', 0, 1, 'L');

        // Natural Methods
        $this->SetFont('Times', '', 12);
        $naturalMethods = [
            'CM'      => $formData['fp_cm']     ?? 0,
            'BBT'     => $formData['fp_bbt']    ?? 0,
            'ST'      => $formData['fp_st']     ?? 0,
            'SD'      => $formData['fp_sd']     ?? 0,
            'LAM'     => $formData['fp_lam']    ?? 0,
            'Two Day' => $formData['fp_twoday'] ?? 0
        ];

        $isFirst = true;
        foreach ($naturalMethods as $method => $users) {
            $this->Cell(55, 6, ($isFirst) ? 'NATURAL' : '', 'LR', 0, 'C');
            $this->Cell(55, 6, $method, 1, 0, 'L');
            $this->Cell(55, 6, $users, 1, 1, 'C');
            if ($isFirst) $isFirst = false;
        }
        $this->Cell(55, 0, '', 'T', 0, 'L');
        $this->Cell(110, 0, '', 0, 1, 'L');

        $this->Ln(8);

        // Summary Statistics Table
        $this->SetFont('Times', '', 12);
        $summaryStats = [
            'TOTAL CU' => $formData['fp_totalcu'] ?? 0,
            'MCRA'     => $formData['fp_mcra']    ?? 0,
            'CPR'      => $formData['fp_cpr']     ?? 0
        ];

        foreach ($summaryStats as $label => $value) {
            $this->Cell(110, 6, $label, 1, 0, 'C');
            $this->Cell(55, 6, $value, 1, 1, 'C');
        }

        $this->Ln(10);

        $punongName = '';
        if (is_array($punongBarangay) && isset($punongBarangay['full_name'])) {
            $punongName = strtoupper($punongBarangay['full_name']);
        } elseif (is_string($punongBarangay)) {
            $punongName = strtoupper($punongBarangay);
        }

        // Names side by side
        $this->SetFont('Times', 'UB', 12);
        $this->Cell(83, 6, strtoupper($generatedBy ?? ''), 0, 0, 'L');
        $this->Cell(0,  6, 'HON. ' . $punongName, 0, 1, 'R');

        // Designations side by side
        $this->SetFont('Times', '', 11);
        $this->Cell(83, 6, 'Barangay Service Point Officer', 0, 0, 'L');
        $this->Cell(0,  6, 'Punong Barangay', 0, 1, 'R');
    }
}

$directory = 'public/forms';
if (!file_exists($directory)) {
    mkdir($directory, 0777, true);
}

$pdf = new PDF();
$totalHouseholds ??= 0;
$totalFamilies ??= 0;
$totalRecentFamilies ??= 0;
$totalConstituentsWithSpecifiedClassification ??= [];
$formData ??= [];
$pdf->family_information_record($ageCategories, $totalHouseholds, $totalFamilies, $totalRecentFamilies, $totalConstituentsWithSpecifiedClassification, $totalConstituentsByEducationAttainment, $totalConstituentsByOccupation, $punongBarangay, $generatedBy, $formData);
$filePath = $directory . '/' . $filename;
$pdf->Output('F', $filePath);
ob_end_clean();
$pdf->Output('I', "Family Information Record.pdf");
exit;