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

        //render create household family view
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
}
