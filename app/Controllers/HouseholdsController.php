<?php
class HouseholdsController extends BaseController
{
    private $householdModel;

    public function __construct()
    {
        date_default_timezone_set('Asia/Manila');
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

        $search = trim($_GET['search'] ?? '');

        // ── Server-side pagination ──
        $perPage     = 10;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $total       = $this->householdModel->countFilteredHouseholds($search);
        $totalPages  = max(1, (int)ceil($total / $perPage));
        $offset      = ($currentPage - 1) * $perPage;

        $households = $this->householdModel->getFilteredHouseholds($search, $perPage, $offset);

        $formSubmitted = Session::get('formSubmitted');
        $error         = Session::get('formError');
        $formData      = Session::get('formData');

        Session::remove('formSubmitted');
        Session::remove('formError');
        Session::remove('formData');

        $this->render('home/households/index', [
            'title'         => 'Households',
            'households'    => $households,
            'search'        => $search,
            'currentPage'   => $currentPage,
            'totalPages'    => $totalPages,
            'totalRecords'  => $total,
            'perPage'       => $perPage,
            'formSubmitted' => $formSubmitted,
            'error'         => $error,
            'formData'      => $formData,
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
            'user'  => Session::get('username'),
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
            'household_number'      => $_POST['household_number'] !== '' ? $_POST['household_number'] : null,
            'region'                => $_POST['region'],
            'province'              => $_POST['province'],
            'city_municipality'     => $_POST['city_municipality'],
            'barangay_code'         => $_POST['barangay_code'],
            'barangay_name'         => $_POST['barangay_name'],
            'street_name'           => $_POST['street_name'] ?? '',
            'zip_code'              => $_POST['zip_code'],
            'purok'                 => $_POST['zone'] ?? null,
            'block_number'          => $_POST['block_no'] ?? null,
            'lot_number'            => $_POST['lot_no'] ?? null,
            'house_building_number' => $_POST['house_bldg_no'] ?? null,
            'unit_number'           => $_POST['unit_no'] ?? null,
        ];

        if ($data['household_number'] !== null) {
            if ($householdModel->checkHouseholdNumberExist($data['household_number'])) {
                Session::set('formSubmitted', true);
                Session::set('formError', 'The household number already exists. Please use a unique household number.');
                Session::set('formData', $data);
                header('Location: index.php?controller=households&action=index');
                exit;
            }
        }

        $householdModel->create($data);
        $householdId = $householdModel->getLastInsertedId();

        Session::setFlash('success', 'Household created successfully.');
        header('Location: index.php?controller=households&action=index');
        exit;
    }

    public function addConstituents()
    {
        $this->verifySession();

        $householdId = $_GET['household_id'] ?? null;

        if (!$householdId) {
            header('Location: index.php?controller=households&action=index');
            exit;
        }

        $householdModel = new Households();

        $household    = $householdModel->getHousehold($householdId);
        $constituents = $householdModel->getConstituentsNotInHousehold();
        $hasHead      = $householdModel->checkHouseholdHeadExists($householdId);

        $this->render('home/households/add_consti_household', [
            'title'            => 'Add Constituents to Household',
            'user'             => Session::get('username'),
            'household_id'     => $householdId,
            'household_number' => $household['household_number'] ?? null,
            'constituents'     => $constituents,
            'hasHead'          => $hasHead,
        ]);
    }

    public function storeConstituents()
    {
        $this->verifySession();

        $householdId  = $_POST['household_id'] ?? null;
        $constituents = $_POST['constituents'] ?? [];
        $isHead       = $_POST['is_head'] ?? null;

        if (!$householdId || empty($constituents)) {
            header('Location: index.php?controller=households&action=index');
            exit;
        }

        $householdModel = new Households();

        try {
            foreach ($constituents as $constiId => $data) {
                if (!isset($data['selected'])) continue;

                $role        = $data['role'] ?? '';
                $isHeadValue = ($isHead == $constiId) ? 'YES' : 'NO';

                $householdModel->addConstituentsToHousehold($householdId, $constiId, $role, $isHeadValue);
            }

            Session::setFlash('success', 'Constituents added successfully.');
            header('Location: index.php?controller=households&action=view&household_id=' . $householdId);
        } catch (Exception $e) {
            throw $e;
        }
        exit;
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
        $familyModel    = new Family();

        $household = $householdModel->getHousehold($householdId);

        if (!$household) {
            echo "Error: Household not found.";
            exit;
        }

        $members  = $householdModel->getHouseholdMembersWithDetails($householdId);
        $families = $familyModel->getFamiliesWithMembersByHouseholdId($householdId);

        $this->render('home/households/view_household', [
            'title'              => 'View Household',
            'user'               => Session::get('username'),
            'household'          => $household,
            'members'            => $members,
            'families'           => $families,
            'generateRBIFormUrl' => "index.php?controller=households&action=generate_rbi_A&household_id={$householdId}",
        ]);
    }

    public function generate_rbi_A()
    {
        $this->verifySession();

        $householdId = $_GET['household_id'] ?? null;

        if (!$householdId || !is_numeric($householdId)) {
            Session::setFlash('error', 'Invalid household ID');
            header('Location: index.php?controller=households&action=index');
            exit;
        }

        try {
            $householdModel        = new Households();
            $barangayOfficialsModel = new BarangayOfficials();
            $transactionsModel     = new Transactions();

            $household = $householdModel->getHousehold($householdId);
            $members   = $householdModel->getHouseholdMembersInformation($householdId);

            if (!$household) {
                Session::setFlash('error', 'Household not found');
                header('Location: index.php?controller=households&action=index');
                exit;
            }

            $barangaySecretary = $barangayOfficialsModel->getOfficialByRole('SECRETARY');
            $punongBarangay    = $barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');
            $headOfHousehold   = $householdModel->getHouseholdHead($householdId);

            if (!$members) {
                Session::setFlash('error', 'No members found for this household');
                header('Location: index.php?controller=households&action=index');
                exit;
            }

            ob_clean();

            $directory = 'public/forms';
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $timestamp = date('Y-m-d_H-i-s');
            $filename  = "rbi_form_a_$timestamp.pdf";

            $generatedByName  = $_SESSION['username'] ?? 'Unknown User';
            $requestedByName  = $headOfHousehold
                ? ($headOfHousehold['full_name'] ?? 'Household #' . $household['household_number'])
                : 'Household #' . $household['household_number'];

            $transactionsModel->create([
                'transaction'          => 'RBI Form A',
                'requested_by'         => $requestedByName,
                'generated_by'         => $generatedByName,
                'document_location'    => "public/forms/$filename",
                'date_of_transaction'  => date('Y-m-d h:i:s A'),
                'purpose'              => 'RBI Form A Generation for Household #' . $household['household_number'],
            ]);

            $this->render('forms/rbi_forma', [
                'household'          => $household,
                'members'            => $members,
                'barangaySecretary'  => $barangaySecretary ? ($barangaySecretary['full_name'] ?? '') : '',
                'punongBarangay'     => $punongBarangay ? ($punongBarangay['full_name'] ?? '') : '',
                'headOfHousehold'    => $headOfHousehold ? ($headOfHousehold['full_name'] ?? '') : '',
                'household_number'   => $household['household_number'] ?? '',
                'filename'           => $filename,
            ]);
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
            $householdModel        = new Households();
            $constituentsModel     = new Constituents();
            $barangayOfficialsModel = new BarangayOfficials();
            $transactionsModel     = new Transactions();

            $constituent = $constituentsModel->get($constituentId);
            if (!$constituent) {
                Session::setFlash('error', 'Constituent not found');
                header('Location: index.php?controller=households&action=index');
                exit;
            }

            $household         = $householdModel->getConstituentHousehold($constituentId);
            $barangaySecretary = $barangayOfficialsModel->getOfficialByRole('SECRETARY');

            ob_clean();

            $directory = 'public/forms';
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $timestamp = date('Y-m-d_H-i-s');
            $filename  = 'rbi_form_b_' . $timestamp . '.pdf';

            $generatedByName = $_SESSION['username'] ?? 'Unknown User';

            $transactionsModel->create([
                'transaction'         => 'RBI Form B',
                'requested_by'        => $constituent['first_name'] . ' ' . $constituent['middle_name'] . ' ' . $constituent['last_name'],
                'generated_by'        => $generatedByName,
                'document_location'   => "public/forms/$filename",
                'date_of_transaction' => date('Y-m-d h:i:s A'),
                'purpose'             => 'RBI Form B Generation',
            ]);

            $this->render('forms/rbi_formb', [
                'constituent'       => $constituent,
                'barangaySecretary' => $barangaySecretary ? ($barangaySecretary['full_name'] ?? '') : '',
                'household'         => $household ? $household['household_number'] ?? '' : '',
                'filename'          => $filename,
            ]);
        } catch (Exception $e) {
            Session::setFlash('error', 'An error occurred: ' . $e->getMessage());
            header('Location: index.php?controller=households&action=index');
            exit;
        }
    }

    public function addMembersToHousehold()
    {
        $this->verifySession();
        $householdId = $_GET['household_id'] ?? null;
    }

    public function delete()
    {
        $this->verifySession();

        $householdId = $_GET['household_id'] ?? null;

        error_log("Delete household request received for ID: $householdId");

        if (!$householdId) {
            Session::setFlash('error', 'Invalid household ID');
            error_log("Error: Invalid household ID");
            header('Location: index.php?controller=households&action=index');
            exit;
        }

        $householdModel = new Households();

        $members = $householdModel->getHouseholdMembersWithDetails($householdId);
        error_log("Household members check: " . count($members) . " members found");

        if (!empty($members)) {
            Session::setFlash('error', 'Cannot delete household with existing members');
            error_log("Error: Cannot delete household with existing members");
            header('Location: index.php?controller=households&action=index');
            exit;
        }

        error_log("Attempting to delete household ID: $householdId");

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

    public function removeMember()
    {
        $this->verifySession();
        $constituentId = $_GET['constituent_id'] ?? null;
        $householdId   = $_GET['household_id']   ?? null;

        try {
            $this->householdModel->removeMemberFromHousehold($constituentId, $householdId);
            Session::setFlash('success', 'Member removed from household successfully');
        } catch (Exception $e) {
            Session::setFlash('error', 'Error removing member: ' . $e->getMessage());
        }

        header('Location: index.php?controller=households&action=view&household_id=' . $householdId);
        exit;
    }

    public function setHouseholdHead()
    {
        $this->verifySession();

        $constituentId = $_GET['constituent_id'] ?? null;
        $householdId   = $_GET['household_id']   ?? null;

        if (!$constituentId || !$householdId) {
            Session::setFlash('error', 'Missing required parameters');
            header('Location: index.php?controller=households&action=index');
            exit;
        }

        try {
            $this->householdModel->demoteHouseholdHead($householdId);
            $this->householdModel->promoteHouseholdHead($householdId, $constituentId);
            Session::setFlash('success', 'Household head updated successfully.');
        } catch (Exception $e) {
            Session::setFlash('error', 'Error updating household head: ' . $e->getMessage());
        }

        header('Location: index.php?controller=households&action=view&household_id=' . $householdId);
        exit;
    }
}