<?php

class ConstituentsController extends BaseController
{
    private $constituentsModel;
    private $classificationsModel;
    private $constituentsValidator;
    private $constituentsClassificationsModel;
    private $householdModel;
    private $userModel;
    private $vehicleModel;
    public function __construct()
    {
        $this->constituentsModel = new Constituents();
        $this->constituentsValidator = new ConstituentsValidator();
        $this->classificationsModel = new Classifications();
        $this->constituentsClassificationsModel = new ConstituentsClassifications();
        $this->householdModel = new Households();
        $this->userModel = new User();
        $this->vehicleModel = new Vehicle();
    }

    public function verifySession()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
        }
    }

    private function sanitizeInput($input, $default = null, $toUpper = true)
    {
        if (!isset($input) || trim($input) === '') {
            return $default;
        }
        $input = trim($input);
        return $toUpper && is_string($input) ? strtoupper($input) : $input;
    }

    private function sanitizeData($postData)
    {
        $data = [
            'psn'                    => !empty($postData['psn']) ? $this->sanitizeInput($postData['psn']) : null,
            'last_name'              => $this->sanitizeInput($postData['last_name'] ?? null),
            'first_name'             => $this->sanitizeInput($postData['first_name'] ?? null),
            'middle_name'            => $this->sanitizeInput($postData['middle_name'] ?? null),
            'suffix'                 => $this->sanitizeInput($postData['suffix'] ?? null, null, false),
            'birthdate'              => $this->sanitizeInput($postData['birthdate'] ?? null),
            'birthplace'             => $this->sanitizeInput($postData['birthplace'] ?? null) ?? 'TACLOBAN CITY',
            'sex'                    => $this->sanitizeInput($postData['sex'] ?? null) ?? 'MALE',
            'civil_status'           => $this->sanitizeInput($postData['civil_status'] ?? null) ?? 'SINGLE',
            'religion'               => $this->sanitizeInput($postData['religion'] ?? null) ?? 'ROMAN CATHOLIC',
            'citizenship'            => $this->sanitizeInput($postData['citizenship'] ?? null) ?? 'FILIPINO',
            'citizenship_others'     => $this->sanitizeInput($postData['citizenship_others'] ?? null),
            'occupation'             => $this->sanitizeInput($postData['occupation'] ?? null, null, false),
            'contact'                => $this->sanitizeInput($postData['contact'] ?? null, null, false),
            'email'                  => !empty($postData['email']) ? strtolower(trim($postData['email'])) : null,
            'education_attainment'   => $this->sanitizeInput($postData['education_attainment'] ?? null) ?? '1',
            'is_graduate'            => $this->sanitizeInput($postData['is_graduate'] ?? null) ?? 'NO',
            'registered_voter'       => $this->sanitizeInput($postData['registered_voter'] ?? null) ?? 'NO',
            'classifications'        => $postData['classifications'] ?? [],
            'classification_org_ids' => [],
        ];

        if (isset($postData['classification_org_ids']) && is_array($postData['classification_org_ids'])) {
            foreach ($postData['classification_org_ids'] as $key => $value) {
                $data['classification_org_ids'][$key] = empty(trim($value)) ? null : $value;
            }
        }

        if ($data['citizenship'] === 'OTHERS' && !empty($postData['citizenship_others'])) {
            $data['citizenship'] = $this->sanitizeInput($postData['citizenship_others']);
        }

        return $data;
    }

    // ── Username/Password Generation ──────────────────────────────────────────

    private function buildBaseCredential(array $data): string
    {
        $first  = strtolower(preg_replace('/[^a-zA-Z]/', '', $data['first_name'] ?? ''));
        $last   = strtolower(preg_replace('/[^a-zA-Z]/', '', $data['last_name']  ?? ''));
        $suffix = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $data['suffix']  ?? ''));

        return $first . $last . $suffix;
    }

    private function generateUniqueUsername(array $data): string
    {
        $base     = $this->buildBaseCredential($data);
        $username = $base;
        $counter  = 2;

        while ($this->userModel->findByUsername($username)) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }

    private function createConstituentAccount(int $constituentId, array $data): string
    {
        $firstName = $data['first_name'] ?? '';
        $lastName  = $data['last_name']  ?? '';
        $middle    = $data['middle_name'] ?? '';
        $suffix    = $data['suffix']      ?? '';
        $fullName  = trim("$firstName $middle $lastName" . ($suffix ? " $suffix" : ''));

        $username = $this->generateUniqueUsername($data);
        $password = password_hash($username, PASSWORD_DEFAULT);

        $this->userModel->createWithConstituentId(
            $username,
            $password,
            'constituent',
            'approved',
            $fullName,
            null,
            $constituentId
        );

        return $username;
    }

    // ── Duplicate org-ID error parser ─────────────────────────────────────────

    private function parseDuplicateOrgIdError(PDOException $e, array $data): ?array
    {
        if (
            $e->getCode() == 23000 &&
            strpos($e->getMessage(), 'unique_org_per_classification') !== false
        ) {
            preg_match("/Duplicate entry '([^']+)'/", $e->getMessage(), $matches);
            $rawEntry = $matches[1] ?? '';

            $parts          = explode('-', $rawEntry);
            $classifId      = array_pop($parts);
            $duplicateOrgId = implode('-', $parts);

            $duplicateClassificationName = 'this classification';
            foreach (($data['classification_org_ids'] ?? []) as $classId => $orgId) {
                if ($orgId === $duplicateOrgId) {
                    $classification = $this->classificationsModel->get($classId);
                    $duplicateClassificationName = $classification['name'] ?? 'this classification';
                    break;
                }
            }

            return [
                'org_id'              => $duplicateOrgId,
                'classification_name' => $duplicateClassificationName,
            ];
        }

        return null;
    }

    // ── Controllers ───────────────────────────────────────────────────────────

    public function index()
    {
        $this->verifySession();

        // ── Collect filters from GET ──
        $filters = [
            'search'     => trim($_GET['search']     ?? ''),
            'age_min'    => trim($_GET['age_min']    ?? ''),
            'age_max'    => trim($_GET['age_max']    ?? ''),
            'occupation' => trim($_GET['occupation'] ?? ''),
            'education'  => trim($_GET['education']  ?? ''),
        ];

        // ── Server-side pagination ──
        $perPage     = 10;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $total       = $this->constituentsModel->countFilteredConstituents($filters);
        $totalPages  = max(1, (int)ceil($total / $perPage));
        $offset      = ($currentPage - 1) * $perPage;

        $constituents = $this->constituentsModel->getFilteredConstituents($filters, $perPage, $offset);

        $this->render('home/constituents/index', [
            'title'        => 'Constituents',
            'constituents' => $constituents,
            'filters'      => $filters,
            'currentPage'  => $currentPage,
            'totalPages'   => $totalPages,
            'totalRecords' => $total,
            'perPage'      => $perPage,
        ]);
    }

    public function create()
    {
        $this->verifySession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitizeData($_POST);

            $birthdate  = $data['birthdate'] ?? '';
            $occupation = $data['occupation'] ?? '';

            // ── AUTO-ADD SENIOR CITIZEN ──
            if (!empty($birthdate) && $this->isSeniorCitizen($birthdate)) {
                $scClassification = $this->classificationsModel->getByCode('SC');
                if ($scClassification) {
                    $scId = $scClassification['id'];
                    if (!in_array($scId, $data['classifications'])) {
                        $data['classifications'][] = $scId;
                        $data['classification_org_ids'][$scId] = $data['classification_org_ids'][$scId] ?? null;
                    }
                }
            }

            // ── AUTO-ADD LABOR/EMPLOYED ──
            $employedOccupations = [
                'Government Employee', 'Private Employee', 'OFW', 'Business',
                'Self-Employed', 'Carpenter', 'Laborer/Construction', 'Driver', 'Sari-Sari Store',
            ];
            if (in_array($occupation, $employedOccupations)) {
                foreach ($this->classificationsModel->getAllClassifications() as $cl) {
                    $label = strtolower(trim($cl['name']));
                    if ($label === 'labor/employed' ||
                        ($label !== 'unemployed' && strpos($label, 'labor') !== false && strpos($label, 'employed') !== false)) {
                        if (!in_array($cl['id'], $data['classifications'])) {
                            $data['classifications'][] = $cl['id'];
                            $data['classification_org_ids'][$cl['id']] = null;
                        }
                        break;
                    }
                }
            }

            // ── AUTO-ADD OFW ──
            if ($occupation === 'OFW') {
                $ofwCl = $this->classificationsModel->getByCode('OFW');
                if ($ofwCl && !in_array($ofwCl['id'], $data['classifications'])) {
                    $data['classifications'][] = $ofwCl['id'];
                    $data['classification_org_ids'][$ofwCl['id']] = null;
                }
            }

            // ── AUTO-ADD STUDENT ──
            if ($occupation === 'Student') {
                $stuCl = $this->classificationsModel->getByCode('STUDENT');
                if ($stuCl && !in_array($stuCl['id'], $data['classifications'])) {
                    $data['classifications'][] = $stuCl['id'];
                    $data['classification_org_ids'][$stuCl['id']] = null;
                }
            }

            $errors = $this->constituentsValidator->createValidate($data);

            if (empty($errors)) {
                try {
                    $constituent_id = $this->constituentsModel->create($data);

                    if ($constituent_id) {
                        if (!empty($data['classifications'])) {
                            $data['constituent_id'] = $constituent_id;
                            try {
                                $this->constituentsClassificationsModel->create($data);
                            } catch (PDOException $e) {
                                $dupInfo = $this->parseDuplicateOrgIdError($e, $data);
                                if ($dupInfo) {
                                    $this->constituentsModel->deleteById($constituent_id);
                                    $errors['duplicate_org_id']            = true;
                                    $data['duplicate_org_id']              = $dupInfo['org_id'];
                                    $data['duplicate_classification_name'] = $dupInfo['classification_name'];
                                } else {
                                    throw $e;
                                }
                            }
                        }

                        if (empty($errors)) {
                            // ── AUTO-CREATE CONSTITUENT ACCOUNT ──
                            $generatedUsername = $this->createConstituentAccount($constituent_id, $data);

                            $successMessage = 'Constituent added successfully!';

                            $addedCl = [];
                            if ($this->isSeniorCitizen($birthdate))          $addedCl[] = 'Senior Citizen';
                            if (in_array($occupation, $employedOccupations)) $addedCl[] = 'Labor/Employed';
                            if ($occupation === 'OFW')                        $addedCl[] = 'OFW';
                            if ($occupation === 'Student')                    $addedCl[] = 'Student';
                            if (!empty($addedCl)) {
                                $successMessage .= ' Auto-classified: ' . implode(', ', $addedCl) . '.';
                            }

                            $successMessage .= ' Login credentials — Username: '
                                . htmlspecialchars($generatedUsername)
                                . ' / Password: '
                                . htmlspecialchars($generatedUsername);

                            Session::setFlash('success', $successMessage);
                            header('Location: index.php?controller=constituents');
                            exit;
                        }
                    } else {
                        $errors['create_constituent'] = 'Database insert returned no ID. Check your DB logs.';
                    }
                } catch (PDOException $e) {
                    $dupInfo = $this->parseDuplicateOrgIdError($e, $data);
                    if ($dupInfo) {
                        $errors['duplicate_org_id']            = true;
                        $data['duplicate_org_id']              = $dupInfo['org_id'];
                        $data['duplicate_classification_name'] = $dupInfo['classification_name'];
                    } else {
                        $errors['create_constituent'] = 'A database error occurred. Please try again.';
                    }
                } catch (Exception $e) {
                    $errors['create_constituent'] = $e->getMessage();
                }
            }

            $classifications = $this->classificationsModel->getAllClassifications();
            $this->render('home/constituents/add_constituent', [
                'title'           => 'Constituents',
                'classifications' => $classifications,
                'data'            => $data,
                'errors'          => $errors,
            ]);

        } else {
            $this->render('home/constituents/add_constituent', [
                'title'           => 'Constituents',
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
                $classifications    = $this->constituentsClassificationsModel->getConstituentClassifications($id);
                $allClassifications = $this->classificationsModel->getAllClassifications();
                $constituentHousehold = $this->householdModel->getConstituentHouseholdInfo($id);

                $classificationMap = [];
                foreach ($allClassifications as $cl) {
                    $classificationMap[$cl['id']] = $cl['code'];
                }
                foreach ($classifications as &$cl) {
                    $cl['code'] = $classificationMap[$cl['classification_id']] ?? 'Unknown';
                }
                $vehicles = $this->vehicleModel->getByOwnerId((int)$id);
                $this->render('home/constituents/view_constituent', [
                    'title'                => 'Constituents',
                    'constituent'          => $constituent,
                    'classifications'      => $classifications,
                    'constituentHousehold' => $constituentHousehold,
                    'vehicles'             => $vehicles,
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

            $birthdate  = $data['birthdate'] ?? '';
            $occupation = $data['occupation'] ?? '';

            // ── AUTO-ADD SENIOR CITIZEN ──
            if (!empty($birthdate) && $this->isSeniorCitizen($birthdate)) {
                $scCl = $this->classificationsModel->getByCode('SC');
                if ($scCl && !in_array($scCl['id'], $data['classifications'])) {
                    $data['classifications'][] = $scCl['id'];
                    $data['classification_org_ids'][$scCl['id']] = null;
                }
            }

            // ── AUTO-ADD LABOR/EMPLOYED ──
            $employedOccupations = [
                'Government Employee', 'Private Employee', 'OFW', 'Business',
                'Self-Employed', 'Carpenter', 'Laborer/Construction', 'Driver', 'Sari-Sari Store',
            ];
            if (in_array($occupation, $employedOccupations)) {
                foreach ($this->classificationsModel->getAllClassifications() as $cl) {
                    $label = strtolower(trim($cl['name']));
                    if ($label === 'labor/employed' ||
                        ($label !== 'unemployed' && strpos($label, 'labor') !== false && strpos($label, 'employed') !== false)) {
                        if (!in_array($cl['id'], $data['classifications'])) {
                            $data['classifications'][] = $cl['id'];
                            $data['classification_org_ids'][$cl['id']] = null;
                        }
                        break;
                    }
                }
            }

            // ── AUTO-ADD OFW ──
            if ($occupation === 'OFW') {
                $ofwCl = $this->classificationsModel->getByCode('OFW');
                if ($ofwCl && !in_array($ofwCl['id'], $data['classifications'])) {
                    $data['classifications'][] = $ofwCl['id'];
                    $data['classification_org_ids'][$ofwCl['id']] = null;
                }
            }

            // ── AUTO-ADD STUDENT ──
            if ($occupation === 'Student') {
                $stuCl = $this->classificationsModel->getByCode('STUDENT');
                if ($stuCl && !in_array($stuCl['id'], $data['classifications'])) {
                    $data['classifications'][] = $stuCl['id'];
                    $data['classification_org_ids'][$stuCl['id']] = null;
                }
            }

            $id = $_GET['id'] ?? null;
            if (!$id) {
                Session::setFlash('error', 'Invalid constituent ID.');
                header('Location: index.php?controller=constituents');
                exit;
            }

            $data['id'] = $id;
            $errors     = $this->constituentsValidator->updateValidate($data);

            if (empty($errors)) {
                try {
                    $updateResult = $this->constituentsModel->update($data);

                    if ($updateResult) {
                        $this->constituentsClassificationsModel->deleteByConstituentId($id);

                        if (!empty($data['classifications'])) {
                            $data['constituent_id'] = $id;
                            try {
                                $this->constituentsClassificationsModel->create($data);
                            } catch (PDOException $e) {
                                $dupInfo = $this->parseDuplicateOrgIdError($e, $data);
                                if ($dupInfo) {
                                    $errors['duplicate_org_id']            = true;
                                    $data['duplicate_org_id']              = $dupInfo['org_id'];
                                    $data['duplicate_classification_name'] = $dupInfo['classification_name'];
                                    throw new Exception('__duplicate_org_id__');
                                } else {
                                    throw $e;
                                }
                            }
                        }

                        if (empty($errors)) {
                            $successMessage = 'Constituent updated successfully!';
                            $addedCl = [];
                            if ($this->isSeniorCitizen($birthdate))          $addedCl[] = 'Senior Citizen';
                            if (in_array($occupation, $employedOccupations)) $addedCl[] = 'Labor/Employed';
                            if ($occupation === 'OFW')                        $addedCl[] = 'OFW';
                            if ($occupation === 'Student')                    $addedCl[] = 'Student';
                            if (!empty($addedCl)) {
                                $successMessage .= ' (Auto-classifications updated: ' . implode(', ', $addedCl) . ')';
                            }
                            Session::setFlash('success', $successMessage);
                            header("Location: index.php?controller=constituents&action=view&id=$id");
                            exit;
                        }
                    } else {
                        throw new Exception('Failed to update constituent. Please check the database logs.');
                    }
                } catch (PDOException $e) {
                    $dupInfo = $this->parseDuplicateOrgIdError($e, $data);
                    if ($dupInfo) {
                        $errors['duplicate_org_id']            = true;
                        $data['duplicate_org_id']              = $dupInfo['org_id'];
                        $data['duplicate_classification_name'] = $dupInfo['classification_name'];
                    } else {
                        $errors['update_constituent'] = 'A database error occurred. Please try again.';
                    }
                } catch (Exception $e) {
                    if ($e->getMessage() !== '__duplicate_org_id__') {
                        $errors['update_constituent'] = $e->getMessage();
                    }
                }
            }

            $constituent     = $this->constituentsModel->get($id);
            $classifications = $this->classificationsModel->getAllClassifications();

            $classificationData = [];
            foreach (($data['classifications'] ?? []) as $classId) {
                $classificationData[$classId] = [
                    'checked' => true,
                    'org_id'  => $data['classification_org_ids'][$classId] ?? null,
                ];
            }

            $this->render('home/constituents/edit_constituent', [
                'title'              => 'Edit Constituent',
                'constituent'        => $constituent,
                'classifications'    => $classifications,
                'classificationData' => $classificationData,
                'errors'             => $errors,
                'data'               => $data,
            ]);

        } else {
            header('Location: index.php?controller=constituents');
            exit;
        }
    }

    public function removedConstituents()
    {
        $this->verifySession();

        $search       = trim($_GET['search'] ?? '');
        $constituents = $this->constituentsModel->getAllRemoved();

        if ($search !== '') {
            $constituents = array_filter($constituents, function ($c) use ($search) {
                $fullName = strtolower(trim(
                    ($c['first_name']  ?? '') . ' ' .
                    ($c['middle_name'] ?? '') . ' ' .
                    ($c['last_name']   ?? '') . ' ' .
                    ($c['suffix']      ?? '')
                ));
                return str_contains($fullName, strtolower($search));
            });
            $constituents = array_values($constituents);
        }

        $this->render('home/constituents/removed_constituents', [
            'title'               => 'Removed Constituents',
            'removedConstituents' => $constituents,
            'search'              => $search,
        ]);
    }

    public function addConstituent()
    {
        $this->verifySession();
        $this->render('home/constituents/add_constituent', [
            'title'           => 'Constituents',
            'classifications' => $this->classificationsModel->getAllClassifications(),
        ]);
    }

    public function editConstituent()
    {
        $this->verifySession();
        $id = $_GET['id'] ?? null;

        if ($id) {
            $constituent     = $this->constituentsModel->get($id);
            $classifications = $this->classificationsModel->getAllClassifications();

            $existingClassifications = $this->constituentsClassificationsModel->getConstituentClassifications($id);
            $classificationData = [];
            foreach ($existingClassifications as $cl) {
                $classificationData[$cl['classification_id']] = [
                    'checked' => true,
                    'org_id'  => $cl['org_id_no'],
                ];
            }

            $employedOccupations = [
                'Government Employee', 'Private Employee', 'OFW', 'Business',
                'Self-Employed', 'Carpenter', 'Laborer/Construction', 'Driver', 'Sari-Sari Store',
            ];

            if (in_array($constituent['occupation'], $employedOccupations)) {
                foreach ($classifications as $cl) {
                    $label = strtolower(trim($cl['name']));
                    if ($label === 'labor/employed' ||
                        ($label !== 'unemployed' && strpos($label, 'labor') !== false && strpos($label, 'employed') !== false)) {
                        if (!isset($classificationData[$cl['id']])) {
                            $classificationData[$cl['id']] = ['checked' => true, 'org_id' => null, 'auto_added' => true];
                        }
                        break;
                    }
                }
            }

            if ($constituent['occupation'] === 'OFW') {
                $ofwCl = $this->classificationsModel->getByCode('OFW');
                if ($ofwCl && !isset($classificationData[$ofwCl['id']])) {
                    $classificationData[$ofwCl['id']] = ['checked' => true, 'org_id' => null, 'auto_added' => true];
                }
            }

            if ($constituent['occupation'] === 'Student') {
                $stuCl = $this->classificationsModel->getByCode('STUDENT');
                if ($stuCl && !isset($classificationData[$stuCl['id']])) {
                    $classificationData[$stuCl['id']] = ['checked' => true, 'org_id' => null, 'auto_added' => true];
                }
            }

            if ($constituent) {
                $this->render('home/constituents/edit_constituent', [
                    'title'              => 'Constituents',
                    'constituent'        => $constituent,
                    'classifications'    => $classifications,
                    'classificationData' => $classificationData,
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
            $result = $this->constituentsModel->remove($id);
            if ($result['success']) {
                $msg = $result['was_official']
                    ? 'Constituent archived. They have also been removed from the barangay officials list.'
                    : 'Constituent archived successfully!';
                Session::setFlash('success', $msg);
            } else {
                Session::setFlash('error', 'Failed to archive constituent. ' . ($result['error'] ?? ''));
            }
            header('Location: index.php?controller=constituents');
        } else {
            Session::setFlash('error', 'Invalid request.');
            header('Location: index.php?controller=constituents');
        }
    }

    public function restoreConstituent()
    {
        $this->verifySession();
        $id = $_GET['id'] ?? null;

        if ($id) {
            $this->constituentsModel->restore($id);
            Session::setFlash('success', 'Constituent restored successfully!');
            header('Location: index.php?controller=constituents&action=removedConstituents');
        } else {
            Session::setFlash('error', 'Invalid request.');
        }
    }

    private function calculateAge($birthdate)
    {
        if (empty($birthdate)) return 0;
        return (new DateTime($birthdate))->diff(new DateTime('today'))->y;
    }

    private function isSeniorCitizen($birthdate)
    {
        return $this->calculateAge($birthdate) >= 60;
    }
}