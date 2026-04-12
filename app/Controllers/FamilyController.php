<?php

class FamilyController extends BaseController
{
    private $familyModel;

    public function __construct()
    {
        $this->familyModel = new Family();
    }

    public function verifySession()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    public function createHouseholdFamily()
    {
        $this->verifySession();

        $householdId = $_GET['household_id'] ?? null;

        if (!$householdId) {
            Session::setFlash('error', 'Household ID is required');
            header('Location: index.php?controller=households&action=index');
            exit;
        }

        $this->render('home/households/create_household_family', [
            'householdId' => $householdId,
            'constituents' => $this->familyModel->getConstituentsInHouseholdNotInFamily($householdId)
        ]);
    }

    public function store()
    {
        $this->verifySession();

        if (!isset($_POST['members']) || empty($_POST['members'])) {
            Session::setFlash('error', 'Please select at least one family member');
            header('Location: index.php?controller=family&action=createHouseholdFamily&household_id=' . $_GET['household_id']);
            exit;
        }

        if (!isset($_POST['head_constituent_id']) || !in_array($_POST['head_constituent_id'], $_POST['members'])) {
            Session::setFlash('error', 'The head of family must be selected from the family members');
            header('Location: index.php?controller=family&action=createHouseholdFamily&household_id=' . $_GET['household_id']);
            exit;
        }

        $data = [
            'household_id' => $_GET['household_id'],
            'family_name' => $_POST['family_name'],
            'head_constituent_id' => $_POST['head_constituent_id'],
            'date_resided' => $_POST['date_resided']
        ];

        try {
            $familyId = $this->familyModel->create($data);

            if ($familyId) {
                foreach ($_POST['members'] as $constituentId) {
                    $this->familyModel->updateConstituentsFamilyIdInHousehold($constituentId, $familyId);
                }
                Session::setFlash('success', 'Family created successfully');
            } else {
                Session::setFlash('error', 'Failed to create family');
            }
        } catch (Exception $e) {
            Session::setFlash('error', 'An error occurred while creating the family: ' . $e->getMessage());
        }

        header('Location: index.php?controller=households&action=view&household_id=' . $data['household_id']);
        exit;
    }
    public function addMember()
    {
        $this->verifySession();

        $familyId   = $_GET['family_id']   ?? null;
        $householdId = $_GET['household_id'] ?? null;

        if (!$familyId || !$householdId) {
            Session::setFlash('error', 'Missing required parameters');
            header('Location: index.php?controller=households&action=index');
            exit;
        }

        $family      = $this->familyModel->getFamilyById($familyId);
        $constituents = $this->familyModel->getConstituentsInHouseholdNotInFamily($householdId);

        if (!$family) {
            Session::setFlash('error', 'Family not found');
            header('Location: index.php?controller=households&action=view&household_id=' . $householdId);
            exit;
        }

        $this->render('home/households/add_member_to_family', [
            'title'        => 'Add Member to Family',
            'family'       => $family,
            'householdId'  => $householdId,
            'constituents' => $constituents
        ]);
    }

    public function storeMember()
    {
        $this->verifySession();

        $familyId    = $_POST['family_id']    ?? null;
        $householdId = $_POST['household_id'] ?? null;
        $members     = $_POST['members']      ?? [];

        if (!$familyId || !$householdId) {
            Session::setFlash('error', 'Missing required parameters');
            header('Location: index.php?controller=households&action=index');
            exit;
        }

        if (empty($members)) {
            Session::setFlash('error', 'Please select at least one member to add');
            header('Location: index.php?controller=family&action=addMember&family_id=' . $familyId . '&household_id=' . $householdId);
            exit;
        }

        try {
            foreach ($members as $constituentId) {
                $this->familyModel->addMemberToFamily($constituentId, $familyId, $householdId);
            }
            Session::setFlash('success', 'Member(s) added to family successfully');
        } catch (Exception $e) {
            Session::setFlash('error', 'Error adding members: ' . $e->getMessage());
        }

        header('Location: index.php?controller=households&action=view&household_id=' . $householdId);
        exit;
    }
    public function delete()
    {
        $this->verifySession();
        $familyId    = $_GET['family_id']    ?? null;
        $householdId = $_GET['household_id'] ?? null;

        try {
            $this->familyModel->deleteFamily($familyId);
            Session::setFlash('success', 'Family deleted successfully');
        } catch (Exception $e) {
            Session::setFlash('error', 'Error deleting family: ' . $e->getMessage());
        }

        header('Location: index.php?controller=households&action=view&household_id=' . $householdId);
        exit;
    }
    public function removeMemberFromFamily()
    {
        $this->verifySession();

        $constituentId = $_GET['constituent_id'] ?? null;
        $familyId      = $_GET['family_id']      ?? null;
        $householdId   = $_GET['household_id']   ?? null;

        $redirectUrl = 'index.php?controller=households&action=view&household_id=' . $householdId;

        if (!$constituentId || !$familyId || !$householdId) {
            Session::setFlash('error', 'Missing required parameters');
            header('Location: ' . $redirectUrl);
            exit;
        }

        try {
            $this->familyModel->removeMemberFromFamily($constituentId, $familyId);
            Session::setFlash('success', 'Member removed from family successfully');
        } catch (Exception $e) {
            Session::setFlash('error', 'Error removing member from family: ' . $e->getMessage());
        }

        header('Location: ' . $redirectUrl);
        exit;
    }
    public function getMembersJson()
    {
        $this->verifySession();
        
        // Clean any previous output and set JSON header
        ob_clean();
        header('Content-Type: application/json');

        $familyId = $_GET['family_id'] ?? null;

        if (!$familyId) {
            echo json_encode(['success' => false, 'message' => 'Missing family ID']);
            exit;
        }

        try {
            $members = $this->familyModel->getFamilyMembersById($familyId);
            echo json_encode(['success' => true, 'members' => $members]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    public function setFamilyHead()
    {
        $this->verifySession();

        $constituentId = $_GET['constituent_id'] ?? null;
        $familyId      = $_GET['family_id']      ?? null;
        $householdId   = $_GET['household_id']   ?? null;

        try {
            $familyModel = new Family();

            $familyModel->demoteCurrentHead($familyId);

            $familyModel->setFamilyHead($familyId, $constituentId);

            Session::setFlash('success', 'Head of family updated successfully.');
        } catch (Exception $e) {
            Session::setFlash('error', 'Error updating head: ' . $e->getMessage());
        }

        header('Location: index.php?controller=households&action=view&household_id=' . $householdId);
        exit;
    }
}
