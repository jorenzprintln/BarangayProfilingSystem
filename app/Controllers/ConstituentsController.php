<?php

class ConstituentsController extends BaseController
{
    private $constituentsModel;
    private $classificationsModel;
    private $constituentsValidator;
    private $constituentsClassificationsModel;
    private $householdModel;


    public function __construct()
    {
        $this->constituentsModel = new Constituents();
        $this->constituentsValidator = new ConstituentsValidator();
        $this->classificationsModel = new Classifications();
        $this->constituentsClassificationsModel = new ConstituentsClassifications();
        $this->householdModel = new Households();
    }

    public function verifySession()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
        }
    }

    private function sanitizeInput($input, $default = null, $toUpper = true)
    {
        $input = isset($input) ? trim($input) : $default;
        return $toUpper && is_string($input) ? strtoupper($input) : $input;
    }

    private function sanitizeData($postData)
    {
        $data = [
            'psn' => $this->sanitizeInput($postData['psn']),
            'last_name' => $this->sanitizeInput($postData['last_name'] ?? null),
            'first_name' => $this->sanitizeInput($postData['first_name'] ?? null),
            'middle_name' => $this->sanitizeInput($postData['middle_name'] ?? null),
            'suffix' => $this->sanitizeInput($postData['suffix'] ?? null),
            'birthdate' => $this->sanitizeInput($postData['birthdate'] ?? null),
            'birthplace' => $this->sanitizeInput($postData['birthplace'] ?? 'TACLOBAN CITY'),
            'sex' => $this->sanitizeInput($postData['sex'] ?? 'MALE'),
            'civil_status' => $this->sanitizeInput($postData['civil_status'] ?? 'SINGLE'),
            'religion' => $this->sanitizeInput($postData['religion'] ?? 'ROMAN CATHOLIC'),
            'citizenship' => $this->sanitizeInput($postData['citizenship'] ?? 'FILIPINO'),
            'occupation' => $this->sanitizeInput($postData['occupation'] ?? null),
            'contact' => $this->sanitizeInput($postData['contact'] ?? null),
            'email' => $this->sanitizeInput($postData['email'] ?? null),
            'education_attainment' => $this->sanitizeInput($postData['education_attainment'] ?? null),
            'is_graduate' => $this->sanitizeInput($postData['is_graduate'] ?? null),
            'registered_voter' => $this->sanitizeInput($postData['registered_voter'] ?? null),
            'classifications' => $postData['classifications'] ?? [],
            'classification_org_ids' => [],
        ];

        // Handle classification_org_ids
        if (isset($postData['classification_org_ids']) && is_array($postData['classification_org_ids'])) {
            foreach ($postData['classification_org_ids'] as $key => $value) {
                // Convert empty strings to null
                $data['classification_org_ids'][$key] = empty(trim($value)) ? null : $value;
            }
        }

        return $data;
    }

    //Done (for further testing)
    public function index()
    {
        $this->verifySession();
        $constituents = $this->constituentsModel->getAllNotRemoved();

        $this->render('home/constituents/index', [
            'title' => 'Constituents',
            'constituents' => $constituents,
        ]);
    }

    //Done (for further testing)
    public function create()
    {
        $this->verifySession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitizeData($_POST);
            $errors = $this->constituentsValidator->createValidate($data);

            if (empty($errors)) {
                try {
                    $constituent_id = $this->constituentsModel->create($data);
                    if ($constituent_id) {
                        // If we have classifications, save them
                        if (!empty($data['classifications'])) {
                            $data['constituent_id'] = $constituent_id;

                            try {
                                $this->constituentsClassificationsModel->create($data);
                            } catch (Exception $e) {
                                throw $e;
                            }
                        }
                        Session::setFlash('success', 'Constituent created successfully!');
                        header('Location: index.php?controller=constituents');
                        exit;
                    } else {
                        Session::setFlash('error', 'Failed to create constituent.');
                    }
                } catch (Exception $e) {
                    $errors['create_constituent'] = $e->getMessage();
                }
            }

            $classifications = $this->classificationsModel->getAllClassifications();
            $this->render('home/constituents/add_constituent', [
                'title' => 'Constituents',
                'classifications' => $classifications,
                'data' => $data,
                'errors' => $errors,
            ]);
        } else {
            $this->render('home/constituents/add_constituent', [
                'title' => 'Constituents',
                'classifications' => $this->classificationsModel->getAllClassifications(),
            ]);
        }
    }

    public function view()
    {
        $this->verifySession();

        $id = $_GET['id'] ?? null;

        if ($id) {
            $constituent = $this->constituentsModel->get($id);

            if ($constituent) {
                // Get the constituent's classifications with their names
                $classifications = $this->constituentsClassificationsModel->getConstituentClassifications($id);
                
                // Get all classifications to map the names
                $allClassifications = $this->classificationsModel->getAllClassifications();

                $constituentHousehold = $this->householdModel->getConstituentHouseholdInfo($id);
                
                // Create a map of classification IDs to names
                $classificationMap = [];
                foreach ($allClassifications as $classification) {
                    $classificationMap[$classification['id']] = $classification['code'];
                }
                
                // Add classification names to the constituent's classifications
                foreach ($classifications as &$classification) {
                    $classification['code'] = $classificationMap[$classification['classification_id']] ?? 'Unknown';
                }

                $this->render('home/constituents/view_constituent', [
                    'title' => 'Constituents',
                    'constituent' => $constituent,
                    'classifications' => $classifications,
                    'constituentHousehold' => $constituentHousehold
                ]);
            } else {
                Session::setFlash('error', 'Constituent not found.');
                header('Location: index.php?controller=constituents');
            }
        } else {
            Session::setFlash('error', 'Invalid request.');
            header('Location: index.php?controller=constituents');
        }
    }

    public function update()
    {
        $this->verifySession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitizeData($_POST);
            $errors = $this->constituentsValidator->updateValidate($data);

            if (empty($errors)) {
                try {
                    // Get the constituent ID from the URL
                    $id = $_GET['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Invalid constituent ID');
                    }

                    // Add the ID to the data array for the update
                    $data['id'] = $id;

                    // Debug: Log the data being sent for update
                    error_log("Update data: " . print_r($data, true));

                    // Update the constituent's basic information
                    $updateResult = $this->constituentsModel->update($data);
                    $this->constituentsClassificationsModel->deleteByConstituentId($id);

                    if ($updateResult) {
                        // Handle classifications update
                        if (!empty($data['classifications'])) {
                            // First, delete existing classifications for this constituent
                            
                            // Then add the new classifications
                            $data['constituent_id'] = $id;
                            $this->constituentsClassificationsModel->create($data);
                        }

                        Session::setFlash('success', 'Constituent updated successfully!');
                        header("Location: index.php?controller=constituents&action=view&id=$id");
                        exit;
                    } else {
                        throw new Exception('Failed to update constituent. Please check the database logs for more details.');
                    }
                } catch (PDOException $e) {
                    error_log("PDO Error during update: " . $e->getMessage());
                    $errors['update_constituent'] = 'Database error: ' . $e->getMessage();
                } catch (Exception $e) {
                    error_log("General Error during update: " . $e->getMessage());
                    $errors['update_constituent'] = $e->getMessage();
                }
            }

            // If there are errors, redirect back to the edit page with the errors
            $constituent = $this->constituentsModel->get($_GET['id']);
            $classifications = $this->classificationsModel->getAllClassifications();
            
            // Get existing classifications for this constituent
            $existingClassifications = $this->constituentsClassificationsModel->getConstituentClassifications($_GET['id']);
            
            // Create an array of classification IDs and their org IDs
            $classificationData = [];
            foreach ($existingClassifications as $classification) {
                $classificationData[$classification['classification_id']] = [
                    'checked' => true,
                    'org_id' => $classification['org_id_no']
                ];
            }

            $this->render('home/constituents/edit_constituent', [
                'title' => 'Edit Constituent',
                'constituent' => $constituent,
                'classifications' => $classifications,
                'classificationData' => $classificationData,
                'errors' => $errors,
                'data' => $data
            ]);
        } else {
            // If not a POST request, redirect to the constituents list
            header('Location: index.php?controller=constituents');
            exit;
        }
    }

    public function removedConstituents()
    {
        $this->verifySession();
        $constituents = $this->constituentsModel->getAllRemoved();
        $this->render('home/constituents/removed_constituents', [
            'title' => 'Removed Constituents',
            'removedConstituents' => $constituents,
        ]);
    }
    

    public function addConstituent()
    {
        $this->verifySession();

        $classifications = $this->classificationsModel->getAllClassifications();

        $this->render('home/constituents/add_constituent', [
            'title' => 'Constituents',
            'classifications' => $classifications,
        ]);
    }

    public function editConstituent()
    {
        $this->verifySession();
        $id = $_GET['id'] ?? null;

        if ($id) {
            $constituent = $this->constituentsModel->get($id);
            $classifications = $this->classificationsModel->getAllClassifications();
            
            // Get existing classifications for this constituent
            $existingClassifications = $this->constituentsClassificationsModel->getConstituentClassifications($id);
            
            // Create an array of classification IDs and their org IDs
            $classificationData = [];
            foreach ($existingClassifications as $classification) {
                $classificationData[$classification['classification_id']] = [
                    'checked' => true,
                    'org_id' => $classification['org_id_no']
                ];
            }

            if ($constituent) {
                $this->render('home/constituents/edit_constituent', [
                    'title' => 'Constituents',
                    'constituent' => $constituent,
                    'classifications' => $classifications,
                    'classificationData' => $classificationData
                ]);
            } else {
                Session::setFlash('error', 'Constituent not found.');
                header('Location: index.php?controller=constituents');
            }
        } else {
            Session::setFlash('error', 'Invalid request.');
            header('Location: index.php?controller=constituents');
        }
    }

    public function removeConstituent()
    {
        $this->verifySession();

        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->constituentsModel->remove($id);
            Session::setFlash('success', 'Constituent removed successfully!');
            header('Location: index.php?controller=constituents');
        } else {
            Session::setFlash('error', 'Invalid request.');
        }
    }

    public function restoreConstituent()
    {
        $this->verifySession();

        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->constituentsModel->restore($id);
            Session::setFlash('success', 'Constituent restored successfully!');
            header('Location: index.php?controller=constituents');
        } else {
            Session::setFlash('error', 'Invalid request.');
        }
    }



    // public function generateRBIFormB()
    // {
    //     if (!Session::isLoggedIn()) {
    //         header('Location: index.php?controller=auth&action=login');
    //         exit;
    //     }

    //     $id = $_GET['id'] ?? null;

    //     if (!$id || !is_numeric($id)) {
    //         Session::setFlash('error', 'Invalid constituent ID');
    //         header('Location: index.php?controller=home&action=index');
    //         exit;
    //     }

    //     try {
    //         $constituent = $this->constituentsModel->getConstituentById($id);
    //         $barangaySecretary = $this->constituentsModel->getBarangayOfficialByRole(2);

    //         if (!$constituent) {
    //             Session::setFlash('error', 'Constituent not found');
    //             header('Location: index.php?controller=home&action=index');
    //             exit;
    //         }

    //         $this->render('forms/rbi_formb', [
    //             'title' => 'RBI Form B',
    //             'constituent' => $constituent,
    //             'barangaySecretary' => $barangaySecretary['full_name'] ?? 'N/A',
    //             'household_number' => $constituent['household_number'] ?? 'Not Assigned'
    //         ]);
    //     } catch (Exception $e) {
    //         Session::setFlash('error', 'An error occurred: ' . $e->getMessage());
    //         header('Location: index.php?controller=home&action=index');
    //         exit;
    //     }
    // }
}
