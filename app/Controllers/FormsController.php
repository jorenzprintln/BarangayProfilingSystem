<?php

class FormsController extends BaseController
{
    public $constituentsModel;
    public $barangayOfficialsModel;
    public $householdModel;
    public $familyModel;
    public $constituentsClassificationsModel;
    public $transactionsModel;
    public $userModel;

    public function __construct()
    {
        // Set timezone to local timezone
        date_default_timezone_set('Asia/Manila');

        $this->constituentsModel = new Constituents();
        $this->barangayOfficialsModel = new BarangayOfficials();
        $this->householdModel = new Households();
        $this->familyModel = new Family();
        $this->constituentsClassificationsModel = new ConstituentsClassifications();
        $this->transactionsModel = new Transactions();
        $this->userModel = new User();
    }

    private function verifyLogin()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    /**
     * Generate and save form PDF
     * 
     * @param string $formType The type of form (e.g. rbi_form_b)
     * @param string $viewPath Path to the view file
     * @param array $data Data to pass to the view
     * @return string The saved PDF filename
     */
    private function generateAndSaveFormPDF($formType, $viewPath, $data)
    {
        // Create directory if it doesn't exist
        $directory = 'public/forms';
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Generate timestamp for filename
        $timestamp = date('m_d_Y_H_i_s');

        // Format form type (replace spaces with underscores)
        $formType = str_replace(' ', '_', strtolower($formType));

        // Build filename
        $filename = $formType . '_' . $timestamp . '.pdf';
        $filePath = $directory . '/' . $filename;

        // The actual PDF generation is done in the view using FPDF
        // We just need to pass the filename to the view and it will handle
        // creating and saving the PDF in that location

        return $filename;
    }

    public function index()
    {
        $this->verifyLogin();

        $this->render('forms/index', [
            'title' => 'Forms',
        ]);
    }

    public function rbi_form_B()
    {
        $this->verifyLogin();

        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            Session::setFlash('error', 'Invalid constituent ID');
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        try {
            $constituent = $this->constituentsModel->get($id);
            $barangaySecretary = $this->barangayOfficialsModel->getOfficialByRole(2);
            $household = $this->householdModel->getConstituentHouseholdInfo($id);

            $data = [
                'title' => 'Forms',
                'constituent' => $constituent,
                'household' => $household['household_number'] ?? '',
                'barangaySecretary' => $barangaySecretary['full_name'] ?? '',
            ];

            // Generate and save PDF
            $filename = $this->generateAndSaveFormPDF('rbi_form_b', 'forms/rbi_formb', $data);

            // Add filename to data for display
            $data['filename'] = $filename;

            // Record the transaction
            // Get current user's constituent information
            $currentUser = $this->userModel->getCurrentUser();
            $generatedBy = $this->constituentsModel->get($currentUser['constituent_id']);

            // Make sure to create the full name if it doesn't exist
            $generatedByName = '';
            if (!empty($generatedBy)) {
                $middleInitial = !empty($generatedBy['middle_name']) ? substr($generatedBy['middle_name'], 0, 1) . '.' : '';
                $generatedByName = $generatedBy['first_name'] . ' ' .
                    ($middleInitial ? $middleInitial . ' ' : '') .
                    $generatedBy['last_name'];
            }

            $transactionData = [
                'transaction' => 'RBI Form B',
                'requested_by' => $constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name'],
                'generated_by' => $generatedByName,
                'document_location' => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose' => 'RBI Form B Generation'
            ];

            $this->transactionsModel->create($transactionData);

            $this->render('forms/rbi_formb', $data);

        } catch (Exception $e) {
            Session::setFlash('error', 'An error occurred: ' . $e->getMessage());
            header('Location: index.php?controller=home&action=index');
            exit;
        }
    }

    public function coaEntry()
    {
        $this->verifyLogin();

        $this->render('forms/co_appearance/co_appearance_entry', [
            'title' => 'Forms',
        ]);
    }

    public function processCoaEntry()
    {
        $this->verifyLogin();

        $barangayOfficialsModel = new BarangayOfficials();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? 'N/A';
            $location = $_POST['location'] ?? 'N/A';
            $reason = $_POST['reason'] ?? 'N/A';
            $date = date('jS \d\a\y \o\f F, Y'); // Format the date

            $punongBarangay = $barangayOfficialsModel->getOfficialByRole(1);

            $data = [
                'name' => $name,
                'location' => $location,
                'reason' => $reason,
                'date' => $date,
                'punongBarangay' => $punongBarangay,
            ];

            // Generate and save PDF
            $filename = $this->generateAndSaveFormPDF('certificate_of_appearance', 'forms/co_appearance/co_appearance_output', $data);

            // Add filename to data for display
            $data['filename'] = $filename;

            // Record the transaction
            $currentUser = $this->userModel->getCurrentUser();
            $generatedBy = $this->constituentsModel->get($currentUser['constituent_id']);

            // Make sure to create the full name if it doesn't exist
            $generatedByName = '';
            if (!empty($generatedBy)) {
                $middleInitial = !empty($generatedBy['middle_name']) ? substr($generatedBy['middle_name'], 0, 1) . '.' : '';
                $generatedByName = $generatedBy['first_name'] . ' ' .
                    ($middleInitial ? $middleInitial . ' ' : '') .
                    $generatedBy['last_name'];
            }

            $transactionData = [
                'transaction' => 'Certificate of Appearance',
                'requested_by' => $name,
                'generated_by' => $generatedByName,
                'document_location' => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose' => $reason
            ];

            $this->transactionsModel->create($transactionData);

            $this->render('forms/co_appearance/co_appearance_output', $data);
            exit;
        }

        header('Location: index.php?controller=forms&action=coaEntry');
        exit;
    }

    public function bcbEntry()
    {
        $this->verifyLogin();

        $this->render('forms/bc_business/bc_business_entry', [
            'title' => 'Forms',
        ]);
    }

    public function processBcbEntry()
    {
        $this->verifyLogin();
        $barangayOfficialsModel = new BarangayOfficials();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bname = $_POST['bname'] ?? 'N/A';
            $tob = $_POST['tob'] ?? 'N/A';
            $bo = $_POST['bo'] ?? 'N/A';
            $location = $_POST['location'] ?? 'N/A';
            $date = date('jS \d\a\y \o\f F, Y'); // Format the date

            $punongBarangay = $barangayOfficialsModel->getOfficialByRole(1);

            $data = [
                'bname' => $bname,
                'tob' => $tob,
                'bo' => $bo,
                'location' => $location,
                'date' => $date,
                'punongBarangay' => $punongBarangay,
            ];

            // Generate and save PDF
            $filename = $this->generateAndSaveFormPDF('bc_business', 'forms/bc_business/bc_business_output', $data);

            // Add filename to data for display
            $data['filename'] = $filename;

            // Record the transaction
            $currentUser = $this->userModel->getCurrentUser();
            $generatedBy = $this->constituentsModel->get($currentUser['constituent_id']);

            // Make sure to create the full name if it doesn't exist
            $generatedByName = '';
            if (!empty($generatedBy)) {
                $middleInitial = !empty($generatedBy['middle_name']) ? substr($generatedBy['middle_name'], 0, 1) . '.' : '';
                $generatedByName = $generatedBy['first_name'] . ' ' .
                    ($middleInitial ? $middleInitial . ' ' : '') .
                    $generatedBy['last_name'];
            }

            $transactionData = [
                'transaction' => 'Barangay Clearance for Business',
                'requested_by' => $bo,
                'generated_by' => $generatedByName,
                'document_location' => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose' => "Business Clearance for $bname"
            ];

            $this->transactionsModel->create($transactionData);

            $this->render('forms/bc_business/bc_business_output', $data);
            exit;
        }

        header('Location: index.php?controller=forms&action=coaEntry');
        exit;
    }

    public function bcOfwEntry()
    {
        $this->verifyLogin();

        $classificationsModel = new Classifications();
        $ofwConstituents = $classificationsModel->getConstituentsByClassification("OFW");

        $this->render('forms/bc_ofw/bc_ofw_entry', [
            'title' => 'Forms',
            'ofwConstituents' => $ofwConstituents
        ]);
    }

    public function processBcOfwEntry()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = $_POST['full_name'] ?? 'N/A';
            $date = date('jS \d\a\y \o\f F, Y'); // Format the date

            $barangayOfficialsModel = new BarangayOfficials();
            $punongBarangay = $barangayOfficialsModel->getOfficialByRole(1);

            $data = [
                'fullName' => $fullName,
                'date' => $date,
                'punongBarangay' => $punongBarangay['full_name'] ?? '',
            ];

            // Generate and save PDF
            $filename = $this->generateAndSaveFormPDF('bc_ofw', 'forms/bc_ofw/bc_ofw_output', $data);

            // Add filename to data for display
            $data['filename'] = $filename;

            // Record the transaction
            $currentUser = $this->userModel->getCurrentUser();
            $generatedBy = $this->constituentsModel->get($currentUser['constituent_id']);

            // Make sure to create the full name if it doesn't exist
            $generatedByName = '';
            if (!empty($generatedBy)) {
                $middleInitial = !empty($generatedBy['middle_name']) ? substr($generatedBy['middle_name'], 0, 1) . '.' : '';
                $generatedByName = $generatedBy['first_name'] . ' ' .
                    ($middleInitial ? $middleInitial . ' ' : '') .
                    $generatedBy['last_name'];
            }

            $transactionData = [
                'transaction' => 'Barangay Clearance for OFW',
                'requested_by' => $fullName,
                'generated_by' => $generatedByName,
                'document_location' => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose' => 'OFW Clearance'
            ];

            $this->transactionsModel->create($transactionData);

            $this->render('forms/bc_ofw/bc_ofw_output', $data);
            exit;
        }

        header('Location: index.php?controller=forms&action=bcOfwEntry');
        exit;
    }

    public function firEntry()
    {
        $this->verifyLogin();

        $this->render('forms/fir/fir_entry', [
            'title' => 'Forms',
        ]);
    }

    public function processFirEntry()
    {
        $this->verifyLogin();

        // Collect form data
        $formData = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Pregnant Women data
            $formData['pregnant_10_14'] = $_POST['pregnant_10_14'] ?? 0;
            $formData['pregnant_15_19'] = $_POST['pregnant_15_19'] ?? 0;
            $formData['pregnant_20_above'] = $_POST['pregnant_20_above'] ?? 0;

            // Family Planning - Modern methods
            $formData['fp_fs'] = $_POST['fp_fs'] ?? 0;
            $formData['fp_ms'] = $_POST['fp_ms'] ?? 0;
            $formData['fp_iud'] = $_POST['fp_iud'] ?? 0;
            $formData['fp_pill'] = $_POST['fp_pill'] ?? 0;
            $formData['fp_injectable'] = $_POST['fp_injectable'] ?? 0;
            $formData['fp_implant'] = $_POST['fp_implant'] ?? 0;
            $formData['fp_condom'] = $_POST['fp_condom'] ?? 0;

            // Family Planning - Natural methods
            $formData['fp_cm'] = $_POST['fp_cm'] ?? 0;
            $formData['fp_bbt'] = $_POST['fp_bbt'] ?? 0;
            $formData['fp_st'] = $_POST['fp_st'] ?? 0;
            $formData['fp_sd'] = $_POST['fp_sd'] ?? 0;
            $formData['fp_lam'] = $_POST['fp_lam'] ?? 0;
            $formData['fp_twoday'] = $_POST['fp_twoday'] ?? 0;

            // Totals
            $formData['fp_totalcu'] = $_POST['fp_totalcu'] ?? 0;
            $formData['fp_mcra'] = $_POST['fp_mcra'] ?? 0;
            $formData['fp_cpr'] = $_POST['fp_cpr'] ?? 0;
        }

        // Record the transaction
        $currentUser = $this->userModel->getCurrentUser();
        $generatedBy = $this->constituentsModel->get($currentUser['constituent_id']);

        // Make sure to create the full name if it doesn't exist
        $generatedByName = '';
        if (!empty($generatedBy)) {
            $middleInitial = !empty($generatedBy['middle_name']) ? substr($generatedBy['middle_name'], 0, 1) . '.' : '';
            $generatedByName = $generatedBy['first_name'] . ' ' .
                ($middleInitial ? $middleInitial . ' ' : '') .
                $generatedBy['last_name'];
        }

        // Get data from database
        $ageCategories = $this->constituentsModel->getConsituentsCountByAgeCategory();
        $totalHouseholds = $this->householdModel->getTotalHouseholdsCount() ?? 0;
        $totalFamilies = $this->familyModel->countTotalFamilies() ?? 0;
        $totalRecentFamilies = $this->familyModel->getTotalRecentFamiliesCount() ?? 0;
        $totalConstituentsWithSpecifiedClassification = $this->constituentsClassificationsModel->getTotalNumberofConstituentsWithSpecifiedClassifications();
        $totalConstituentsByEducationAttainment = $this->constituentsModel->getConsituentsCountByEducationAttainment();
        $totalConstituentsByOccupation = $this->constituentsModel->getConsituentsCountByOccupation();
        $punongBarangay = $this->barangayOfficialsModel->getOfficialByRole(1);

        $data = [
            'title' => 'Forms',
            'ageCategories' => $ageCategories,
            'totalHouseholds' => $totalHouseholds,
            'totalFamilies' => $totalFamilies,
            'totalRecentFamilies' => $totalRecentFamilies,
            'totalConstituentsWithSpecifiedClassification' => $totalConstituentsWithSpecifiedClassification,
            'totalConstituentsByEducationAttainment' => $totalConstituentsByEducationAttainment,
            'totalConstituentsByOccupation' => $totalConstituentsByOccupation,
            'punongBarangay' => $punongBarangay,
            'generatedBy' => $generatedByName,
            'formData' => $formData
        ];

        // Generate and save PDF
        $filename = $this->generateAndSaveFormPDF('fir', 'forms/fir/firs', $data);

        // Add filename to data for display
        $data['filename'] = $filename;

        $transactionData = [
            'transaction' => 'Family Information Record',
            'requested_by' => 'Barangay Office',
            'generated_by' => $generatedByName,
            'document_location' => "public/forms/$filename",
            'date_of_transaction' => date('Y-m-d H:i:s'),
            'purpose' => 'Family Information Record Generation'
        ];

        $this->transactionsModel->create($transactionData);

        $this->render('forms/fir/firs', $data);
    }

    public function bcGeneralEntry()
    {
        $this->verifyLogin();

        $constituents = $this->constituentsModel->getAllNotRemoved();

        $this->render('forms/bc_general/bc_general_entry', [
            'title' => 'Forms',
            'constituents' => $constituents
        ]);
    }

    public function bcCustomEntry()
    {
        $this->verifyLogin();

        $this->render('forms/bc_custom/bc_custom_entry', [
            'title' => 'Forms'
        ]);
    }

    public function processBcCustomEntry()
    {
        $this->verifyLogin();

        // Path to the DOCX file
        $filePath = 'app/views/forms/bc_custom/Barangay-Certificate.docx';

        // Check if file exists
        if (file_exists($filePath)) {
            // Get form data if submitted via POST
            $requesting_party = $_POST['requesting_party'] ?? '';
            $purpose = $_POST['purpose'] ?? 'Custom Certificate Template';

            // Record the transaction
            $currentUser = $this->userModel->getCurrentUser();
            $generatedBy = $this->constituentsModel->get($currentUser['constituent_id']);

            // Make sure to create the full name if it doesn't exist
            $generatedByName = '';
            if (!empty($generatedBy)) {
                $middleInitial = !empty($generatedBy['middle_name']) ? substr($generatedBy['middle_name'], 0, 1) . '.' : '';
                $generatedByName = $generatedBy['first_name'] . ' ' .
                    ($middleInitial ? $middleInitial . ' ' : '') .
                    $generatedBy['last_name'];
            }

            // Use requesting party as the requested_by value
            $requestedBy = !empty($requesting_party) ? strtoupper($requesting_party) : strtoupper($generatedByName);



            $transactionData = [
                'transaction' => 'Custom Barangay Certificate',
                'requested_by' => $requestedBy,
                'generated_by' => $generatedByName,
                'document_location' => $filePath,
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose' => $purpose
            ];

            $this->transactionsModel->create($transactionData);

            // Set appropriate headers for file download
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="Barangay-Certificate.docx"');
            header('Content-Length: ' . filesize($filePath));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Expires: 0');

            // Clear output buffer
            ob_clean();
            flush();

            // Read and output file content
            readfile($filePath);
            exit;
        } else {
            // If file doesn't exist, set error message and redirect
            Session::setFlash('error', 'Certificate template file not found');
            header('Location: index.php?controller=forms&action=bcCustomEntry');
            exit;
        }
    }

    public function processBcGeneralEntry()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $constituent_id = $_POST['constituent_id'] ?? '';
            $purpose = $_POST['purpose'] ?? '';

            if (empty($constituent_id) || empty($purpose)) {
                Session::setFlash('error', 'All fields are required');
                header('Location: index.php?controller=forms&action=bcGeneralEntry');
                exit;
            }

            // Get constituent data
            $constituent = $this->constituentsModel->get($constituent_id);

            // Get barangay official
            $barangayOfficialsModel = new BarangayOfficials();
            $punongBarangay = $barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');
            $barangaySecretary = $barangayOfficialsModel->getOfficialByRole('SECRETARY');
            // Format the date
            $date = date('jS \d\a\y \o\f F, Y');

            $data = [
                'constituent' => $constituent,
                'purpose' => $purpose,
                'date' => $date,
                'punongBarangay' => $punongBarangay,
                'barangaySecretary' => $barangaySecretary,
            ];

            // Generate and save PDF
            $filename = $this->generateAndSaveFormPDF('bc_general', 'forms/bc_general/bc_general_output', $data);

            // Add filename to data for display
            $data['filename'] = $filename;

            // Record the transaction
            $currentUser = $this->userModel->getCurrentUser();
            $generatedBy = $this->constituentsModel->get($currentUser['constituent_id']);

            // Make sure to create the full name if it doesn't exist
            $generatedByName = '';
            if (!empty($generatedBy)) {
                $middleInitial = !empty($generatedBy['middle_name']) ? substr($generatedBy['middle_name'], 0, 1) . '.' : '';
                $generatedByName = $generatedBy['first_name'] . ' ' .
                    ($middleInitial ? $middleInitial . ' ' : '') .
                    $generatedBy['last_name'];
            }

            $transactionData = [
                'transaction' => 'Barangay Clearance General',
                'requested_by' => $constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name'],
                'generated_by' => $generatedByName,
                'document_location' => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose' => $purpose
            ];

            $this->transactionsModel->create($transactionData);

            $this->render('forms/bc_general/bc_general_output', $data);
            exit;
        }

        header('Location: index.php?controller=forms&action=bcGeneralEntry');
        exit;
    }

    public function bcGoodMoralEntry()
    {
        $this->verifyLogin();

        $constituents = $this->constituentsModel->getAllNotRemoved();

        $this->render('forms/bc_good_moral/bc_gm_entry', [
            'title' => 'Forms',
            'constituents' => $constituents
        ]);
    }

    public function processBcGoodMoralEntry()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $constituent_id = $_POST['constituent_id'] ?? '';
            $purpose = $_POST['purpose'] ?? '';

            if (empty($constituent_id) || empty($purpose)) {
                Session::setFlash('error', 'All fields are required');
                header('Location: index.php?controller=forms&action=bcGoodMoralEntry');
                exit;
            }

            // Get constituent data
            $constituent = $this->constituentsModel->get($constituent_id);

            // Get barangay official
            $barangayOfficialsModel = new BarangayOfficials();
            $punongBarangay = $barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');

            // Format the date
            $date = date('jS \d\a\y \o\f F, Y');

            $data = [
                'constituent' => $constituent,
                'purpose' => $purpose,
                'date' => $date,
                'punongBarangay' => $punongBarangay,
            ];

            // Generate and save PDF
            $filename = $this->generateAndSaveFormPDF('bc_good_moral', 'forms/bc_good_moral/bc_gm_output', $data);

            // Add filename to data for display
            $data['filename'] = $filename;

            // Record the transaction
            $currentUser = $this->userModel->getCurrentUser();
            $generatedBy = $this->constituentsModel->get($currentUser['constituent_id']);

            // Make sure to create the full name if it doesn't exist
            $generatedByName = '';
            if (!empty($generatedBy)) {
                $middleInitial = !empty($generatedBy['middle_name']) ? substr($generatedBy['middle_name'], 0, 1) . '.' : '';
                $generatedByName = $generatedBy['first_name'] . ' ' .
                    ($middleInitial ? $middleInitial . ' ' : '') .
                    $generatedBy['last_name'];
            }

            $transactionData = [
                'transaction' => 'Barangay Clearance Good Moral',
                'requested_by' => $constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name'],
                'generated_by' => $generatedByName,
                'document_location' => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose' => $purpose
            ];

            $this->transactionsModel->create($transactionData);

            $this->render('forms/bc_good_moral/bc_gm_output', $data);
            exit;
        }

        header('Location: index.php?controller=forms&action=bcGoodMoralEntry');
        exit;
    }

    public function bcUnemploymentEntry()
    {
        $this->verifyLogin();

        $constituents = $this->constituentsModel->getAllNotRemoved();

        $this->render('forms/bc_unemployment/bc_unemployment_entry', [
            'title' => 'Forms',
            'constituents' => $constituents
        ]);
    }

    public function processBcUnemploymentEntry()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $constituent_id = $_POST['constituent_id'] ?? '';
            $purpose = $_POST['purpose'] ?? '';

            if (empty($constituent_id) || empty($purpose)) {
                Session::setFlash('error', 'All fields are required');
                header('Location: index.php?controller=forms&action=bcUnemploymentEntry');
                exit;
            }

            $constituent = $this->constituentsModel->get($constituent_id);

            $barangayOfficialsModel = new BarangayOfficials();
            $punongBarangay = $barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');

            $date = date('jS \d\a\y \o\f F, Y');

            $data = [
                'constituent' => $constituent,
                'purpose' => $purpose,
                'date' => $date,
                'punongBarangay' => $punongBarangay,
            ];

            // Generate and save PDF
            $filename = $this->generateAndSaveFormPDF('bc_unemployment', 'forms/bc_unemployment/bc_unemployment_output', $data);

            // Add filename to data for display
            $data['filename'] = $filename;

            // Record the transaction
            $currentUser = $this->userModel->getCurrentUser();
            $generatedBy = $this->constituentsModel->get($currentUser['constituent_id']);

            // Make sure to create the full name if it doesn't exist
            $generatedByName = '';
            if (!empty($generatedBy)) {
                $middleInitial = !empty($generatedBy['middle_name']) ? substr($generatedBy['middle_name'], 0, 1) . '.' : '';
                $generatedByName = $generatedBy['first_name'] . ' ' .
                    ($middleInitial ? $middleInitial . ' ' : '') .
                    $generatedBy['last_name'];
            }

            $transactionData = [
                'transaction' => 'Barangay Clearance Unemployment',
                'requested_by' => $constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name'],
                'generated_by' => $generatedByName,
                'document_location' => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose' => $purpose
            ];

            $this->transactionsModel->create($transactionData);

            $this->render('forms/bc_unemployment/bc_unemployment_output', $data);
            exit;
        }

        header('Location: index.php?controller=forms&action=bcUnemploymentEntry');
    }

    public function coIndigencyEntry()
    {
        $this->verifyLogin();

        $constituents = $this->constituentsModel->getAllNotRemoved();

        $this->render('forms/co_indigency/co_indigency_entry', [
            'title' => 'Forms',
            'constituents' => $constituents
        ]);
    }

    public function processCoIndigencyEntry()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $constituent_id = $_POST['constituent_id'] ?? '';
            $purpose = $_POST['purpose'] ?? '';

            if (empty($constituent_id) || empty($purpose)) {
                Session::setFlash('error', 'All fields are required');
                header('Location: index.php?controller=forms&action=coIndigencyEntry');
                exit;
            }

            $barangayOfficialsModel = new BarangayOfficials();
            $punongBarangay = $barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');

            $constituent = $this->constituentsModel->get($constituent_id);
            $age = $this->constituentsModel->getAge($constituent['birthdate']);

            $date = date('jS \d\a\y \o\f F, Y');

            $data = [
                'constituent' => $constituent,
                'age' => $age,
                'purpose' => $purpose,
                'date' => $date,
                'punongBarangay' => $punongBarangay,
            ];

            // Generate and save PDF
            $filename = $this->generateAndSaveFormPDF('co_indigency', 'forms/co_indigency/co_indigency_output', $data);

            // Add filename to data for display
            $data['filename'] = $filename;

            // Record the transaction
            $currentUser = $this->userModel->getCurrentUser();
            $generatedBy = $this->constituentsModel->get($currentUser['constituent_id']);

            // Make sure to create the full name if it doesn't exist
            $generatedByName = '';
            if (!empty($generatedBy)) {
                $middleInitial = !empty($generatedBy['middle_name']) ? substr($generatedBy['middle_name'], 0, 1) . '.' : '';
                $generatedByName = $generatedBy['first_name'] . ' ' .
                    ($middleInitial ? $middleInitial . ' ' : '') .
                    $generatedBy['last_name'];
            }

            $transactionData = [
                'transaction' => 'Certificate of Indigency',
                'requested_by' => $constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name'],
                'generated_by' => $generatedByName,
                'document_location' => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose' => $purpose
            ];

            $this->transactionsModel->create($transactionData);

            $this->render('forms/co_indigency/co_indigency_output', $data);
            exit;
        }

        header('Location: index.php?controller=forms&action=coIndigencyEntry');
    }

    public function coSoloParentEntry()
    {
        $this->verifyLogin();

        $constituents = $this->constituentsModel->getAllNotRemoved();
        $this->render('forms/co_solo_parent/co_solo_parent_entry', [
            'title' => 'Forms',
            'constituents' => $constituents,
        ]);
    }

    public function processCoSoloParentEntry()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $constituent_id = $_POST['constituent_id'] ?? '';
            $dependents = $_POST['dependents'] ?? '';
            $purpose = $_POST['purpose'] ?? '';

            if (empty($constituent_id) || empty($dependents) || empty($purpose)) {
                Session::setFlash('error', 'All fields are required');
                header('Location: index.php?controller=forms&action=coSoloParentEntry');
                exit;
            }

            $barangayOfficialsModel = new BarangayOfficials();
            $punongBarangay = $barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');

            $constituent = $this->constituentsModel->get($constituent_id);
            $date = date('jS \d\a\y \o\f F, Y');

            $data = [
                'constituent' => $constituent,
                'dependents' => $dependents,
                'purpose' => $purpose,
                'date' => $date,
                'punongBarangay' => $punongBarangay,
            ];

            // Generate and save PDF
            $filename = $this->generateAndSaveFormPDF('co_solo_parent', 'forms/co_solo_parent/co_solo_parent_output', $data);

            // Add filename to data for display
            $data['filename'] = $filename;

            // Record the transaction
            $currentUser = $this->userModel->getCurrentUser();
            $generatedBy = $this->constituentsModel->get($currentUser['constituent_id']);

            // Make sure to create the full name if it doesn't exist
            $generatedByName = '';
            if (!empty($generatedBy)) {
                $middleInitial = !empty($generatedBy['middle_name']) ? substr($generatedBy['middle_name'], 0, 1) . '.' : '';
                $generatedByName = $generatedBy['first_name'] . ' ' .
                    ($middleInitial ? $middleInitial . ' ' : '') .
                    $generatedBy['last_name'];
            }

            $transactionData = [
                'transaction' => 'Certificate of Solo Parent',
                'requested_by' => $constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name'],
                'generated_by' => $generatedByName,
                'document_location' => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose' => $purpose
            ];

            $this->transactionsModel->create($transactionData);

            $this->render('forms/co_solo_parent/co_solo_parent_output', $data);
            exit;
        }

        header('Location: index.php?controller=forms&action=coSoloParentEntry');
        exit;
    }

    public function downloadAllFormsToZip()
    {
        $this->verifyLogin();

        $formsDirectory = 'public/forms';
        $zipFileName = 'all_forms.zip';

        $zip = new ZipArchive();
        $zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($formsDirectory),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($formsDirectory) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
        readfile($zipFileName);

        unlink($zipFileName);
        exit;
    }
}