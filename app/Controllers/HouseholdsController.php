<?php
class HouseholdsController extends BaseController
{
    private $householdModel;

    public function __construct()
    {
        $this->householdModel = new Households();
    }

    public function verifySession()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public function index()
    {
        $this->verifySession();

        $householdModel = new Households();
        $households = $householdModel->getAllHouseholds();
        
        $formSubmitted = Session::get('formSubmitted');
        $error = Session::get('formError');
        $formData = Session::get('formData');
        
        // Clear session data after retrieving it
        Session::remove('formSubmitted');
        Session::remove('formError');
        Session::remove('formData');
        
        $this->render('home/households/index', [
            'title' => 'Households',
            'households' => $households,
            'formSubmitted' => $formSubmitted,
            'error' => $error,
            'formData' => $formData
        ]);
    }

    public function createView()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $this->render('home/households/create_household', [
            'title' => 'Create Household',
            'user' => Session::get('username')
        ]);
    }

    public function store()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $householdModel = new Households();

        $data = [
            'household_number' => $_POST['household_number'] !== '' ? $_POST['household_number'] : null,
            'region' => $_POST['region'],
            'province' => $_POST['province'],
            'city_municipality' => $_POST['city_municipality'],
            'barangay_code' => $_POST['barangay_code'],
            'barangay_name' => $_POST['barangay_name'],
            'street_name' => $_POST['street_name'] ?? '',
            'zip_code' => $_POST['zip_code'],
            'purok' => $_POST['zone'] ?? null,
            'block_number' => $_POST['block_no'] ?? null,
            'lot_number' => $_POST['lot_no'] ?? null,
            'house_building_number' => $_POST['house_bldg_no'] ?? null,
            'unit_number' => $_POST['unit_no'] ?? null
        ];

        if ($data['household_number'] !== null) {
            if ($householdModel->checkHouseholdNumberExist($data['household_number'])) {
                // Set form submission status for modal display
                Session::set('formSubmitted', true);
                Session::set('formError', 'The household number already exists. Please use a unique household number.');
                Session::set('formData', $data);
                
                header('Location: index.php?controller=households&action=index');
                exit;
            }
        }

        $householdModel->create($data);
        $householdId = $householdModel->getLastInsertedId();

        // Redirect directly to the index page without asking about constituents
        Session::setFlash('success', 'Household created successfully.');
        header('Location: index.php?controller=households&action=index');
        exit;
    }

    // Add a new method to handle the add constituents view
    public function addConstituents()
    {
        $this->verifySession();

        $householdId = $_GET['household_id'] ?? null;

        if (!$householdId) {
            header('Location: index.php?controller=households&action=index');
            exit;
        }

        $householdModel = new Households();

        $constituents = $householdModel->getConstituentsNotInHousehold();
        $hasHead = $householdModel->checkHouseholdHeadExists($householdId);

        $this->render('home/households/add_consti_household', [
            'title' => 'Add Constituents to Household',
            'user' => Session::get('username'),
            'household_id' => $householdId,
            'constituents' => $constituents,
            'hasHead' => $hasHead // Pass the flag to the view
        ]);
    }

    public function storeConstituents()
    {
        $this->verifySession();

        $householdId = $_POST['household_id'] ?? null;
        $constituents = $_POST['constituents'] ?? [];
        $isHead = $_POST['is_head'] ?? null;

        if (!$householdId || empty($constituents)) {
            header('Location: index.php?controller=households&action=index');
            exit;
        }

        $householdModel = new Households();

        try {
            foreach ($constituents as $constiId => $data) {
                if (!isset($data['selected'])) {
                    continue;
                }

                $role = $data['role'] ?? '';
                $isHeadValue = ($isHead == $constiId) ? 'YES' : 'NO';

                // Ensure all required data is passed to the model
                $householdModel->addConstituentsToHousehold($householdId, $constiId, $role, $isHeadValue);
            }

            header('Location: index.php?controller=households&action=index');
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function view()
    {
        $this->verifySession();

        $householdId = $_GET['household_id'] ?? null;

        if (!$householdId) {
            echo "Error: Household ID is missing or invalid.";
            exit;
        }

        $householdModel = new Households();
        $familyModel = new Family();

        $household = $householdModel->getHousehold($householdId);

        if (!$household) {
            echo "Error: Household not found.";
            exit;
        }

        $members = $householdModel->getHouseholdMembersWithDetails($householdId);
        $families = $familyModel->getFamiliesWithMembersByHouseholdId($householdId);

        $this->render('home/households/view_household', [
            'title' => 'View Household',
            'user' => Session::get('username'),
            'household' => $household,
            'members' => $members,
            'families' => $families,
            'generateRBIFormUrl' => "index.php?controller=households&action=generateRBIForm&household_id={$householdId}"
        ]);
    }

    public function generateRBIForm()
    {
        $this->verifySession();

        $householdId = $_GET['household_id'] ?? null;

        if (!$householdId || !is_numeric($householdId)) {
            Session::setFlash('error', 'Invalid household ID');
            header('Location: index.php?controller=households&action=index');
            exit;
        }

        try {
            $householdModel = new Households();
            $barangayOfficialsModel = new BarangayOfficials();

            $household = $householdModel->getHousehold($householdId);
            $members = $householdModel->getHouseholdMembersInformation($householdId);

            if (!$household) {
                Session::setFlash('error', 'Household not found');
                header('Location: index.php?controller=households&action=index');
                exit;
            }

            // Get officials and handle cases where they might not exist
            $barangaySecretary = $barangayOfficialsModel->getOfficialByRole(2);
            $punongBarangay = $barangayOfficialsModel->getOfficialByRole(1);
            $headOfHousehold = $householdModel->getHouseholdHead($householdId);

            // Ensure we have valid data before proceeding
            if (!$members) {
                Session::setFlash('error', 'No members found for this household');
                header('Location: index.php?controller=households&action=index');
                exit;
            }

            // Clear any previous output
            ob_clean();

            // Create data array for the view
            $data = [
                'household' => $household,
                'members' => $members,
                'barangaySecretary' => $barangaySecretary ? ($barangaySecretary['full_name'] ?? '') : '',
                'punongBarangay' => $punongBarangay ? ($punongBarangay['full_name'] ?? '') : '',
                'headOfHousehold' => $headOfHousehold ? ($headOfHousehold['full_name'] ?? '') : '',
                'household_number' => $household['household_number'] ?? ''
            ];

            // Create directory if it doesn't exist
            $directory = 'public/forms';
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            // Generate timestamp for filename
            $timestamp = date('m_d_Y_H_i_s');

            // Build filename
            $filename = "rbi_form_a_$timestamp.pdf";

            // Add filename to data for the view
            $data['filename'] = $filename;

            $this->render('forms/rbi_forma', $data);
        } catch (Exception $e) {
            Session::setFlash('error', 'An error occurred: ' . $e->getMessage());
            header('Location: index.php?controller=households&action=index');
            exit;
        }
    }

    public function generateRBIFormB()
    {
        $this->verifySession();

        $constituentId = $_GET['constituent_id'] ?? null;

        if (!$constituentId || !is_numeric($constituentId)) {
            Session::setFlash('error', 'Invalid constituent ID');
            header('Location: index.php?controller=households&action=index');
            exit;
        }

        try {
            $householdModel = new Households();
            $constituentsModel = new Constituents();
            $barangayOfficialsModel = new BarangayOfficials();

            $constituent = $constituentsModel->get($constituentId);
            if (!$constituent) {
                Session::setFlash('error', 'Constituent not found');
                header('Location: index.php?controller=households&action=index');
                exit;
            }

            $household = $householdModel->getConstituentHousehold($constituentId);

            // Get officials
            $barangaySecretary = $barangayOfficialsModel->getOfficialByRole(2);

            // Clear any previous output
            ob_clean();

            // Create data array for the view
            $data = [
                'constituent' => $constituent,
                'barangaySecretary' => $barangaySecretary ? ($barangaySecretary['full_name'] ?? '') : '',
                'household' => $household ? $household['household_number'] ?? '' : ''
            ];

            // Create directory if it doesn't exist
            $directory = 'public/forms';
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            // Generate timestamp for filename
            $timestamp = date('m_d_Y_H_i_s');

            // Build filename
            $filename = 'rbi_form_b_' . $timestamp . '.pdf';

            // Add filename to data for the view
            $data['filename'] = $filename;

            $this->render('forms/rbi_formb', $data);
        } catch (Exception $e) {
            Session::setFlash('error', 'An error occurred: ' . $e->getMessage());
            header('Location: index.php?controller=households&action=index');
            exit;
        }
    }

    // New updates here

    public function addMembersToHousehold()
    {
        $this->verifySession();

        $householdId = $_GET['household_id'] ?? null;

    }
    
    public function delete()
    {
        $this->verifySession();
        
        $householdId = $_GET['household_id'] ?? null;
        
        // Debug log - received request
        error_log("Delete household request received for ID: $householdId");
        
        if (!$householdId) {
            Session::setFlash('error', 'Invalid household ID');
            error_log("Error: Invalid household ID");
            header('Location: index.php?controller=households&action=index');
            exit;
        }
        
        $householdModel = new Households();
        
        // Check if the household has any members
        $members = $householdModel->getHouseholdMembersWithDetails($householdId);
        error_log("Household members check: " . count($members) . " members found");
        
        if (!empty($members)) {
            Session::setFlash('error', 'Cannot delete household with existing members');
            error_log("Error: Cannot delete household with existing members");
            header('Location: index.php?controller=households&action=index');
            exit;
        }
        
        // Debug log - attempting deletion
        error_log("Attempting to delete household ID: $householdId");
        
        // Delete the household
        try {
            $result = $householdModel->delete($householdId);
            error_log("Delete result: " . ($result ? "success" : "failed"));
            
            if ($result) {
                Session::setFlash('success', 'Household deleted successfully');
            } else {
                Session::setFlash('error', 'Failed to delete household');
            }
        } catch (Exception $e) {
            error_log("Exception during delete: " . $e->getMessage());
            Session::setFlash('error', 'Error deleting household: ' . $e->getMessage());
        }
        
        header('Location: index.php?controller=households&action=index');
        exit;
    }
}