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
    public $classificationsModel;

    public function __construct()
    {
        date_default_timezone_set('Asia/Manila');

        $this->constituentsModel = new Constituents();
        $this->barangayOfficialsModel = new BarangayOfficials();
        $this->householdModel = new Households();
        $this->familyModel = new Family();
        $this->constituentsClassificationsModel = new ConstituentsClassifications();
        $this->transactionsModel = new Transactions();
        $this->userModel = new User();
        $this->classificationsModel = new Classifications();
    }

    private function verifyLogin()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    private function getLoggedInUsername()
    {
        return $_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Unknown User';
    }

    private function generateAndSaveFormPDF($formType, $viewPath, $data)
    {
        $directory = 'public/forms';
        if (!file_exists($directory)) {
            if (!mkdir($directory, 0777, true)) {
                error_log("Failed to create directory: $directory");
                throw new Exception("Failed to create forms directory");
            }
        }

        if (!is_writable($directory)) {
            error_log("Directory not writable: $directory");
            chmod($directory, 0777);
        }

        $timestamp = date('Y-m-d_H-i-s');
        $formType  = str_replace(' ', '_', strtolower($formType));
        $filename  = $formType . '_' . $timestamp . '.pdf';

        error_log("Generated PDF filename: $filename");
        error_log("Full path: $directory/$filename");

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
            $constituent       = $this->constituentsModel->get($id);
            $barangaySecretary = $this->barangayOfficialsModel->getOfficialByRole('SECRETARY');
            $household         = $this->householdModel->getConstituentHouseholdInfo($id);

            $filename = $this->generateAndSaveFormPDF('rbi_form_b', 'forms/rbi_formb', []);

            $generatedByName = $this->getLoggedInUsername();

            // Null-safe name fields for transaction log
            $firstName  = $constituent['first_name']  ?? '';
            $middleName = $constituent['middle_name']  ?? '';
            $lastName   = $constituent['last_name']    ?? '';

            $transactionData = [
                'transaction'         => 'RBI Form B',
                'requested_by'        => trim("$firstName $middleName $lastName"),
                'generated_by'        => $generatedByName,
                'document_location'   => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose'             => 'RBI Form B Generation',
            ];
            $this->transactionsModel->create($transactionData);

            // Clear ALL output buffers before sending PDF
            while (ob_get_level()) {
                ob_end_clean();
            }

            // Suppress deprecation/notices so nothing leaks before headers
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);

            $data = [
                'constituent'       => $constituent,
                'barangaySecretary' => $barangaySecretary['full_name'] ?? '',
                'household'         => $household['household_number']  ?? '',
                'filename'          => $filename,
            ];

            require_once 'app/Views/forms/rbi_formb.php';
            exit;

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
            $this->verifyCsrf();
            $name     = $_POST['name']     ?? 'N/A';
            $location = $_POST['location'] ?? 'N/A';
            $reason   = $_POST['reason']   ?? 'N/A';
            $date     = date('jS \d\a\y \o\f F, Y');

            $punongBarangay = $barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');

            $data = [
                'name'           => $name,
                'location'       => $location,
                'reason'         => $reason,
                'date'           => $date,
                'punongBarangay' => $punongBarangay,
            ];

            $filename       = $this->generateAndSaveFormPDF('certificate_of_appearance', 'forms/co_appearance/co_appearance_output', $data);
            $data['filename'] = $filename;

            $generatedByName = $_SESSION['username'] ?? 'Unknown User';

            $transactionData = [
                'transaction'         => 'Certificate of Appearance',
                'requested_by'        => $name,
                'generated_by'        => $generatedByName,
                'document_location'   => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose'             => $reason,
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

        $constituents = $this->constituentsModel->getAllNotRemoved();

        $this->render('forms/bc_business/bc_business_entry', [
            'title'        => 'Forms',
            'constituents' => $constituents,
        ]);
    }

    public function processBcbEntry()
    {
        $this->verifyLogin();
        $barangayOfficialsModel = new BarangayOfficials();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $bname          = $_POST['bname']          ?? 'N/A';
            $tob            = $_POST['tob']            ?? 'N/A';
            $constituent_id = $_POST['constituent_id'] ?? '';
            $location       = $_POST['location']       ?? 'N/A';

            // Resolve owner name from constituent
            if (!empty($constituent_id)) {
                $constituent = $this->constituentsModel->get($constituent_id);
                $firstName   = $constituent['first_name']  ?? '';
                $middleName  = $constituent['middle_name'] ?? '';
                $lastName    = $constituent['last_name']   ?? '';
                $suffix      = $constituent['suffix']      ?? '';
                $bo          = strtoupper(trim("$firstName $middleName $lastName $suffix"));
            } else {
                $bo = 'N/A';
            }
            $date     = date('jS \d\a\y \o\f F, Y');

            $punongBarangay = $barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');

            $data = [
                'bname'          => $bname,
                'tob'            => $tob,
                'bo'             => $bo,
                'location'       => $location,
                'date'           => $date,
                'punongBarangay' => $punongBarangay,
            ];

            $filename       = $this->generateAndSaveFormPDF('bc_business', 'forms/bc_business/bc_business_output', $data);
            $data['filename'] = $filename;

            $generatedByName = $_SESSION['username'] ?? 'Unknown User';

            $transactionData = [
                'transaction' => 'Barangay Certificate for Business',
                'requested_by'        => $bo,
                'generated_by'        => $generatedByName,
                'document_location'   => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose'             => "Business Certificate for $bname",
            ];
            $this->transactionsModel->create($transactionData);

            // Load PDF output directly without layout wrapper
            extract($data);
            $filePath = 'public/forms/' . $filename;
            require 'app/Views/forms/bc_business/bc_business_output.php';
            exit;
        }

        header('Location: index.php?controller=forms&action=bcbEntry');
        exit;
    }

    public function bcOfwEntry()
    {
        $this->verifyLogin();

        $search = trim($_GET['search'] ?? '');

        // Get OFWs by occupation
        $ofwByOccupation = $this->constituentsModel->getConstituentsByOccupation("OFW");

        // Get OFWs by classification (catches constituents with OFW classification
        // even if their occupation field doesn't say "OFW")
        $ofwClassification = $this->classificationsModel->getByCode('OFW');
        $ofwByClassification = [];
        if ($ofwClassification) {
            $ofwByClassification = $this->constituentsClassificationsModel
                ->getConstituentsByClassificationId($ofwClassification['id']);
        }

        // Merge and deduplicate by constituent ID
        $merged = [];
        foreach (array_merge($ofwByOccupation, $ofwByClassification) as $ofw) {
            $merged[$ofw['id']] = $ofw;
        }
        $ofwConstituents = array_values($merged);

        if (!empty($search)) {
            $ofwConstituents = array_values(array_filter($ofwConstituents, function ($ofw) use ($search) {
                return stripos($ofw['full_name'] ?? '', $search) !== false;
            }));
        }

        $this->render('forms/bc_ofw/bc_ofw_entry', [
            'title'           => 'Forms',
            'ofwConstituents' => $ofwConstituents,
            'search'          => $search,
        ]);
    }

    public function processBcOfwEntry()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $fullName = $_POST['full_name'] ?? 'N/A';
            $date     = date('jS \d\a\y \o\f F, Y');

            $barangayOfficialsModel = new BarangayOfficials();
            $punongBarangay         = $barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');

            $data = [
                'fullName'       => $fullName,
                'date'           => $date,
                'punongBarangay' => $punongBarangay['full_name'] ?? '',
            ];

            $filename       = $this->generateAndSaveFormPDF('bc_ofw', 'forms/bc_ofw/bc_ofw_output', $data);
            $data['filename'] = $filename;

            $generatedByName = $_SESSION['username'] ?? 'Unknown User';

            $transactionData = [
                'transaction' => 'Barangay Certificate for OFW',
                'requested_by'        => $fullName,
                'generated_by'        => $generatedByName,
                'document_location'   => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose'             => 'OFW Certificate',
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

        $formData = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $formData['pregnant_10_14']  = $_POST['pregnant_10_14']  ?? 0;
            $formData['pregnant_15_19']  = $_POST['pregnant_15_19']  ?? 0;
            $formData['pregnant_20_above'] = $_POST['pregnant_20_above'] ?? 0;

            $formData['fp_fs']         = $_POST['fp_fs']         ?? 0;
            $formData['fp_ms']         = $_POST['fp_ms']         ?? 0;
            $formData['fp_iud']        = $_POST['fp_iud']        ?? 0;
            $formData['fp_pill']       = $_POST['fp_pill']       ?? 0;
            $formData['fp_injectable'] = $_POST['fp_injectable'] ?? 0;
            $formData['fp_implant']    = $_POST['fp_implant']    ?? 0;
            $formData['fp_condom']     = $_POST['fp_condom']     ?? 0;

            $formData['fp_cm']     = $_POST['fp_cm']     ?? 0;
            $formData['fp_bbt']    = $_POST['fp_bbt']    ?? 0;
            $formData['fp_st']     = $_POST['fp_st']     ?? 0;
            $formData['fp_sd']     = $_POST['fp_sd']     ?? 0;
            $formData['fp_lam']    = $_POST['fp_lam']    ?? 0;
            $formData['fp_twoday'] = $_POST['fp_twoday'] ?? 0;

            $formData['fp_totalcu'] = $_POST['fp_totalcu'] ?? 0;
            $formData['fp_mcra']    = $_POST['fp_mcra']    ?? 0;
            $formData['fp_cpr']     = $_POST['fp_cpr']     ?? 0;
        }

        $generatedByName = $_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Unknown User';

        $ageCategories = $this->constituentsModel->getConsituentsCountByAgeCategory();
        $totalHouseholds = $this->householdModel->getTotalHouseholdsCount() ?? 0;
        $totalFamilies = $this->familyModel->countTotalFamilies() ?? 0;
        $totalRecentFamilies = $this->familyModel->getTotalRecentFamiliesCount() ?? 0;
        $totalConstituentsWithSpecifiedClassification = $this->constituentsClassificationsModel->getTotalNumberofConstituentsWithSpecifiedClassifications();
        $totalConstituentsByEducationAttainment = $this->constituentsModel->getConsituentsCountByEducationAttainment();
        $totalConstituentsByOccupation = $this->constituentsModel->getConsituentsCountByOccupation();
        $punongBarangay = $this->barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');
        
        $scData = $this->constituentsModel->getTotalSeniorCitizensByAge();
        $totalConstituentsWithSpecifiedClassification['SC'] = [
            'MALE'   => $scData['TOTAL']['MALE']   ?? 0,
            'FEMALE' => $scData['TOTAL']['FEMALE'] ?? 0,
            'total'  => $scData['TOTAL']['total']  ?? 0,
        ];
        $data = [
            'title'          => 'Forms',
            'ageCategories'  => $ageCategories,
            'totalHouseholds'=> $totalHouseholds,
            'totalFamilies'  => $totalFamilies,
            'totalRecentFamilies' => $totalRecentFamilies,
            'totalConstituentsWithSpecifiedClassification' => $totalConstituentsWithSpecifiedClassification,
            'totalConstituentsByEducationAttainment'       => $totalConstituentsByEducationAttainment,
            'totalConstituentsByOccupation'                => $totalConstituentsByOccupation,
            'punongBarangay' => $punongBarangay,
            'generatedBy'    => $generatedByName,
            'formData'       => $formData,
        ];

        $filename       = $this->generateAndSaveFormPDF('fir', 'forms/fir/firs', $data);
        $data['filename'] = $filename;

        $transactionData = [
            'transaction'         => 'Family Information Record',
            'requested_by'        => 'Barangay Office',
            'generated_by'        => $generatedByName,
            'document_location'   => "public/forms/$filename",
            'date_of_transaction' => date('Y-m-d H:i:s'),
            'purpose'             => 'Family Information Record Generation',
        ];
        $this->transactionsModel->create($transactionData);

        $this->render('forms/fir/firs', $data);
    }

    public function bcGeneralEntry()
    {
        $this->verifyLogin();

        $constituents = $this->constituentsModel->getAllNotRemoved();

        $this->render('forms/bc_general/bc_general_entry', [
            'title'        => 'Forms',
            'constituents' => $constituents,
        ]);
    }

    public function bcCustomEntry()
    {
        $this->verifyLogin();

        $this->render('forms/bc_custom/bc_custom_entry', [
            'title' => 'Forms',
        ]);
    }

    public function processBcCustomEntry()
    {
        $this->verifyLogin();

        $filePath = 'app/views/forms/bc_custom/Barangay-Certificate.docx';

        if (file_exists($filePath)) {
            $requesting_party = $_POST['requesting_party'] ?? '';
            $purpose          = $_POST['purpose']          ?? 'Custom Certificate Template';
            $generatedByName  = $_SESSION['username']      ?? 'Unknown User';
            $requestedBy      = !empty($requesting_party)  ? strtoupper($requesting_party) : strtoupper($generatedByName);

            $transactionData = [
                'transaction'         => 'Custom Barangay Certificate',
                'requested_by'        => $requestedBy,
                'generated_by'        => $generatedByName,
                'document_location'   => $filePath,
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose'             => $purpose,
            ];
            $this->transactionsModel->create($transactionData);

            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="Barangay-Certificate.docx"');
            header('Content-Length: ' . filesize($filePath));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Expires: 0');

            ob_clean();
            flush();
            readfile($filePath);
            exit;
        }

        Session::setFlash('error', 'Certificate template file not found');
        header('Location: index.php?controller=forms&action=bcCustomEntry');
        exit;
    }

    public function processBcGeneralEntry()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $purpose = $_POST['purpose'] ?? '';

        if (empty($purpose)) {
            Session::setFlash('error', 'All fields are required');
            header('Location: index.php?controller=forms&action=bcGeneralEntry');
            exit;
        }

        $constituent = $this->getValidConstituent($_POST['constituent_id'] ?? '', 'bcGeneralEntry');
            $barangayOfficialsModel = new BarangayOfficials();
            $punongBarangay = $barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');
            $barangaySecretary = $barangayOfficialsModel->getOfficialByRole('SECRETARY');
            $date           = date('jS \d\a\y \o\f F, Y');

            $data = [
                'constituent'      => $constituent,
                'purpose'          => $purpose,
                'date'             => $date,
                'punongBarangay'   => $punongBarangay,
                'barangaySecretary'=> $barangaySecretary,
            ];

            $filename       = $this->generateAndSaveFormPDF('bc_general', 'forms/bc_general/bc_general_output', $data);
            $data['filename'] = $filename;

            $generatedByName = $_SESSION['username'] ?? 'Unknown User';
            $firstName  = $constituent['first_name']  ?? '';
            $middleName = $constituent['middle_name']  ?? '';
            $lastName   = $constituent['last_name']    ?? '';
            $suffix     = $constituent['suffix']      ?? '';

            $transactionData = [
                'transaction' => 'Barangay Certificate',
                'requested_by'        => trim("$firstName $middleName $lastName $suffix"),
                'generated_by'        => $generatedByName,
                'document_location'   => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose'             => $purpose,
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
            'title'        => 'Forms',
            'constituents' => $constituents,
        ]);
    }

    public function processBcGoodMoralEntry()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $purpose = $_POST['purpose'] ?? '';

        if (empty($purpose)) {
            Session::setFlash('error', 'All fields are required');
            header('Location: index.php?controller=forms&action=bcGoodMoralEntry');
            exit;
        }

        $constituent = $this->getValidConstituent($_POST['constituent_id'] ?? '', 'bcGoodMoralEntry');
            $barangayOfficialsModel = new BarangayOfficials();
            $punongBarangay = $barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');
            $date           = date('jS \d\a\y \o\f F, Y');

            $data = [
                'constituent'    => $constituent,
                'purpose'        => $purpose,
                'date'           => $date,
                'punongBarangay' => $punongBarangay,
            ];

            $filename       = $this->generateAndSaveFormPDF('bc_good_moral', 'forms/bc_good_moral/bc_gm_output', $data);
            $data['filename'] = $filename;

            $generatedByName = $_SESSION['username'] ?? 'Unknown User';
            $firstName  = $constituent['first_name']  ?? '';
            $middleName = $constituent['middle_name']  ?? '';
            $lastName   = $constituent['last_name']    ?? '';
            $suffix     = $constituent['suffix']      ?? '';

            $transactionData = [
                'transaction' => 'Certificate of Good Moral Character',
                'requested_by'        => trim("$firstName $middleName $lastName $suffix"),
                'generated_by'        => $generatedByName,
                'document_location'   => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose'             => $purpose,
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
            'title'        => 'Forms',
            'constituents' => $constituents,
        ]);
    }

    public function processBcUnemploymentEntry()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $purpose = $_POST['purpose'] ?? '';

        if (empty($purpose)) {
            Session::setFlash('error', 'All fields are required');
            header('Location: index.php?controller=forms&action=bcUnemploymentEntry');
            exit;
        }

        $constituent = $this->getValidConstituent($_POST['constituent_id'] ?? '', 'bcUnemploymentEntry');
            $barangayOfficialsModel = new BarangayOfficials();
            $punongBarangay = $barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');
            $date           = date('jS \d\a\y \o\f F, Y');

            $data = [
                'constituent'    => $constituent,
                'purpose'        => $purpose,
                'date'           => $date,
                'punongBarangay' => $punongBarangay,
            ];

            $filename       = $this->generateAndSaveFormPDF('bc_unemployment', 'forms/bc_unemployment/bc_unemployment_output', $data);
            $data['filename'] = $filename;

            $generatedByName = $_SESSION['username'] ?? 'Unknown User';
            $firstName  = $constituent['first_name']  ?? '';
            $middleName = $constituent['middle_name']  ?? '';
            $lastName   = $constituent['last_name']    ?? '';
            $suffix     = $constituent['suffix']      ?? '';

            $transactionData = [
                'transaction' => 'Barangay Certificate for Unemployment',
                'requested_by'        => trim("$firstName $middleName $lastName $suffix"),
                'generated_by'        => $generatedByName,
                'document_location'   => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose'             => $purpose,
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
            'title'        => 'Forms',
            'constituents' => $constituents,
        ]);
    }

    public function processCoIndigencyEntry()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $purpose = $_POST['purpose'] ?? '';

        if (empty($purpose)) {
            Session::setFlash('error', 'All fields are required');
            header('Location: index.php?controller=forms&action=coIndigencyEntry');
            exit;
        }

            $barangayOfficialsModel = new BarangayOfficials();
            $punongBarangay         = $barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');
            $constituent            = $this->getValidConstituent($_POST['constituent_id'] ?? '', 'coIndigencyEntry');
            $age                    = $this->constituentsModel->getAge($constituent['birthdate'] ?? '');
            $date                   = date('jS \d\a\y \o\f F, Y');

            $data = [
                'constituent'    => $constituent,
                'age'            => $age,
                'purpose'        => $purpose,
                'date'           => $date,
                'punongBarangay' => $punongBarangay,
            ];

            $filename       = $this->generateAndSaveFormPDF('co_indigency', 'forms/co_indigency/co_indigency_output', $data);
            $data['filename'] = $filename;

            $generatedByName = $_SESSION['username'] ?? 'Unknown User';
            $firstName  = $constituent['first_name']  ?? '';
            $middleName = $constituent['middle_name']  ?? '';
            $lastName   = $constituent['last_name']    ?? '';
            $suffix     = $constituent['suffix']      ?? '';

            $transactionData = [
                'transaction'         => 'Certificate of Indigency',
                'requested_by'        => trim("$firstName $middleName $lastName $suffix"),
                'generated_by'        => $generatedByName,
                'document_location'   => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose'             => $purpose,
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
            'title'        => 'Forms',
            'constituents' => $constituents,
        ]);
    }

    public function processCoSoloParentEntry()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();

            $dependents = $_POST['dependents'] ?? '';
            $purpose    = $_POST['purpose']    ?? '';

            if (empty($dependents) || empty($purpose)) {
                Session::setFlash('error', 'All fields are required');
                header('Location: index.php?controller=forms&action=coSoloParentEntry');
                exit;
            }

            $barangayOfficialsModel = new BarangayOfficials();
            $punongBarangay         = $barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');
            $constituent            = $this->getValidConstituent($_POST['constituent_id'] ?? '', 'coSoloParentEntry');
            $date                   = date('jS \d\a\y \o\f F, Y');

            $data = [
                'constituent'    => $constituent,
                'dependents'     => $dependents,
                'purpose'        => $purpose,
                'date'           => $date,
                'punongBarangay' => $punongBarangay,
            ];

            $filename       = $this->generateAndSaveFormPDF('co_solo_parent', 'forms/co_solo_parent/co_solo_parent_output', $data);
            $data['filename'] = $filename;

            $generatedByName = $_SESSION['username'] ?? 'Unknown User';
            $firstName  = $constituent['first_name']  ?? '';
            $middleName = $constituent['middle_name']  ?? '';
            $lastName   = $constituent['last_name']    ?? '';
            $suffix     = $constituent['suffix']      ?? '';

            $transactionData = [
                'transaction'         => 'Certificate of Solo Parent',
                'requested_by'        => trim("$firstName $middleName $lastName $suffix"),
                'generated_by'        => $generatedByName,
                'document_location'   => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d H:i:s'),
                'purpose'             => $purpose,
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

        $transactions = $this->transactionsModel->getAllTransactions();

        $linkedFiles = array_filter($transactions, function ($t) {
            return !empty($t['document_location']) && file_exists($t['document_location']);
        });

        if (empty($linkedFiles)) {
            Session::setFlash('error', 'No documents available to download.');
            header('Location: index.php?controller=dashboard&action=index');
            exit;
        }

        $zipFileName = 'public/forms/all_forms_' . date('m_d_Y_H_i_s') . '.zip';

        $zip = new ZipArchive();
        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            Session::setFlash('error', 'Could not create zip file.');
            header('Location: index.php?controller=dashboard&action=index');
            exit;
        }

        $allowedDir = realpath('public/forms');
        foreach ($linkedFiles as $transaction) {
            $filePath = $transaction['document_location'];
            $realPath = realpath($filePath);
            if ($realPath && $allowedDir && strpos($realPath, $allowedDir) === 0) {
                $zip->addFile($filePath, basename($filePath));
            }
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="all_forms.zip"');
        header('Content-Length: ' . filesize($zipFileName));
        readfile($zipFileName);

        unlink($zipFileName);
        exit;
    }
    private function getValidConstituent($constituentId, $redirectAction)
    {
        $id = (int)$constituentId;
        if ($id <= 0) {
            Session::setFlash('error', 'Invalid constituent selected.');
            header('Location: index.php?controller=forms&action=' . $redirectAction);
            exit;
        }
        $constituent = $this->constituentsModel->get($id);
        if (!$constituent) {
            Session::setFlash('error', 'Constituent not found.');
            header('Location: index.php?controller=forms&action=' . $redirectAction);
            exit;
        }
        return $constituent;
    }
}