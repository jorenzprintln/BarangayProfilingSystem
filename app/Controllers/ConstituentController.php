<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Models/VehicleRequest.php';
class ConstituentController extends BaseController
{
    private $userModel;
    private $constituentsModel;
    private $classificationsModel;
    private $constituentsValidator;
    private $constituentsClassificationsModel;
    private $profileRequestModel;
    private $transactionsModel;

    public function __construct()
    {
        $this->userModel                          = new User();
        $this->constituentsModel                  = new Constituents();
        $this->classificationsModel               = new Classifications();
        $this->constituentsValidator              = new ConstituentsValidator();
        $this->constituentsClassificationsModel   = new ConstituentsClassifications();
        $this->profileRequestModel                = new ConstituentProfileRequest();
        $this->transactionsModel                  = new Transactions();
    }

    // ── Auth guards ───────────────────────────────────────────────────────────

    private function verifyConstituent(): void
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if (($_SESSION['role'] ?? '') !== 'constituent') {
            header('Location: index.php?controller=home');
            exit;
        }
    }

    /**
     * Returns true when the logged-in constituent is still using their
     * auto-generated default password (username === password).
     * Must only be called after verifyConstituent().
     */
    private function isUsingDefaultPassword(): bool
    {
        $user = $this->userModel->findById((int)$_SESSION['user_id']);
        if (!$user) {
            return false;
        }
        $username = (string)($user['username'] ?? '');
        return password_verify($username, (string)($user['password'] ?? ''));
    }

    /**
     * Call at the top of every constituent action that should be blocked
     * until the default password is changed.
     * Skips the redirect when already on a change-password action to avoid loops.
     */
    private function enforcePasswordChange(): void
    {
        if ($this->isUsingDefaultPassword()) {
            Session::set('force_password_change', true);
            header('Location: index.php?controller=constituent&action=changePassword');
            exit;
        }
        // Clear the flag once they've changed it
        Session::remove('force_password_change');
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    /**
     * Constituent dashboard
     */
    public function index(): void
    {
        $this->verifyConstituent();
        $this->enforcePasswordChange();

        $user = $this->userModel->findById($_SESSION['user_id']);

        $constituent = null;
        if (!empty($user['constituent_id'])) {
            $constituent = $this->constituentsModel->get((int)$user['constituent_id']);
        }

        $this->render('constituent/dashboard', [
            'title'         => 'My Dashboard',
            'user'          => $user,
            'constituent'   => $constituent,
            'toast_success' => Session::hasFlash('toast_success') ? Session::getFlash('toast_success') : null,
        ]);
    }

    /**
     * Constituent My Requests page - shows all submission requests
     */
    public function myRequests(): void
    {
        $this->verifyConstituent();
        $this->enforcePasswordChange();
        date_default_timezone_set('Asia/Manila'); 
        $userId   = $_SESSION['user_id'];
        $username = $_SESSION['username'] ?? '';
        $profileRequests  = [];
        $documentRequests = [];
        $vehicleRequests  = [];

        $tab = strtolower((string)($_GET['tab'] ?? 'profile'));

        if ($tab === 'profile') {
            try {
                $now = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s');
                $db  = (new Database())->connect();
                $db->prepare(
                    "UPDATE constituent_profile_requests
                    SET seen_at = :now
                    WHERE user_id = :user_id
                    AND status IN ('approved', 'rejected')
                    AND seen_at IS NULL
                    ORDER BY id DESC
                    LIMIT 1"
                )->execute([':user_id' => (int)$userId, ':now' => $now]);
            } catch (Exception $e) { /* non-fatal */ }

        } elseif ($tab === 'document') {
            try {
                $now = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s');
                $db  = (new Database())->connect();
                $db->prepare(
                    "UPDATE transactions
                    SET seen_at = :now
                    WHERE requested_by = :username
                    AND UPPER(generated_by) NOT IN ('PENDING')
                    AND generated_by IS NOT NULL
                    AND generated_by != ''
                    AND seen_at IS NULL"
                )->execute([':username' => $username, ':now' => $now]);
            } catch (Exception $e) { /* non-fatal */ }

        } elseif ($tab === 'vehicle') {
            try {
                $now = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s');
                $db  = (new Database())->connect();
                $db->prepare(
                    "UPDATE vehicle_requests
                    SET seen_at = :now
                    WHERE user_id = :user_id
                    AND status IN ('approved', 'rejected')
                    AND seen_at IS NULL"
                )->execute([':user_id' => (int)$userId, ':now' => $now]);
            } catch (Exception $e) { /* non-fatal */ }
        }

        try {
            $profileRequests = $this->profileRequestModel->getHistoryByUser($userId, 50);

            $classifications   = $this->classificationsModel->getAllClassifications();
            foreach ($classifications as $classification) {
                $classificationMap[(int)$classification['id']] = $classification['code'];
            }

            foreach ($profileRequests as &$request) {
                $payload = json_decode((string)($request['payload_json'] ?? ''), true);
                if (is_array($payload)) {
                    $classIds = [];
                    if (!empty($payload['classifications']) && is_array($payload['classifications'])) {
                        $classIds = array_map('intval', $payload['classifications']);
                    }
                    $classificationNames = [];
                    foreach ($classIds as $classId) {
                        if (isset($classificationMap[$classId])) {
                            $classificationNames[] = $classificationMap[$classId];
                        }
                    }
                    $request['payload_data'] = ['classifications' => $classificationNames];
                } else {
                    $request['payload_data'] = ['classifications' => []];
                }
            }
            unset($request);
        } catch (Exception $e) {
            $profileRequests = [];
        }

        try {
            $documentTypes    = array_values($this->getDocumentTypeOptions());
            $documentRequests = $this->transactionsModel->getRequestsByRequester($username, 50, $documentTypes);
        } catch (Exception $e) {
            $documentRequests = [];
        }

        try {
            $vehicleRequestModel = new VehicleRequest();
            $vehicleRequests     = $vehicleRequestModel->getByUserId((int)$userId);
        } catch (Exception $e) {
            $vehicleRequests = [];
        }

        $this->render('constituent/my_requests', [
            'title'            => 'My Requests',
            'profileRequests'  => $profileRequests,
            'documentRequests' => $documentRequests,
            'vehicleRequests'  => $vehicleRequests,
        ]);
    }

    private function getDocumentTypeOptions(): array
    {
        return [
            'bc_general'      => 'Barangay Certificate',
            'co_indigency'    => 'Barangay Indigency',
            'bc_good_moral'   => 'Certificate of Good Moral',
            'bc_business'     => 'Barangay Certificate for Business',
            'bc_ofw'          => 'Barangay Certificate for OFW',
            'bc_unemployment' => 'Certificate of Unemployment',
            'co_solo_parent'  => 'Certificate of Solo Parent',
        ];
    }


    public function requestDocument(): void
    {
        $this->verifyConstituent();
        $this->enforcePasswordChange();

        $this->render('constituent/request_document', [
            'title'         => 'Request Document',
            'documentTypes' => $this->getDocumentTypeOptions(),
        ]);
    }

    public function requestDocumentPurpose(): void
    {
        $this->verifyConstituent();
        $this->enforcePasswordChange();
        date_default_timezone_set('Asia/Manila');
        $documentTypes    = $this->getDocumentTypeOptions();
        $documentTypeKey  = trim((string)($_GET['type'] ?? ''));

        if (!isset($documentTypes[$documentTypeKey])) {
            Session::setFlash('error', 'Please select a valid document type first.');
            header('Location: index.php?controller=constituent&action=requestDocument');
            exit;
        }

        $this->render('constituent/request_document_purpose', [
            'title'             => 'Request Document',
            'documentTypeKey'   => $documentTypeKey,
            'documentTypeLabel' => $documentTypes[$documentTypeKey],
            'oldPurpose'        => '',
            'errors'            => [],
        ]);
    }

    public function submitDocumentRequest(): void
    {
        $this->verifyConstituent();
        $this->enforcePasswordChange();
        date_default_timezone_set('Asia/Manila');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=constituent&action=requestDocument');
            exit;
        }
        $this->verifyCsrf();

        $documentTypes   = $this->getDocumentTypeOptions();
        $documentTypeKey = trim((string)($_POST['document_type'] ?? ''));
        $purpose         = trim((string)($_POST['purpose'] ?? ''));

        $errors = [];
        if (!isset($documentTypes[$documentTypeKey])) {
            $errors['document_type'] = 'Please select a valid document type.';
        }
        if ($purpose === '') {
            $errors['purpose'] = 'Purpose is required.';
        } elseif (strlen($purpose) < 5) {
            $errors['purpose'] = 'Purpose must be at least 5 characters.';
        }

        if (!empty($errors)) {
            $this->render('constituent/request_document_purpose', [
                'title'             => 'Request Document',
                'documentTypeKey'   => $documentTypeKey,
                'documentTypeLabel' => $documentTypes[$documentTypeKey] ?? 'Unknown Document',
                'oldPurpose'        => $purpose,
                'errors'            => $errors,
            ]);
            return;
        }

        $transactionLabel = $documentTypes[$documentTypeKey];
        $requestedBy      = (string)($_SESSION['username'] ?? '');

        $saved = $this->transactionsModel->createConstituentRequest([
            'transaction'  => $transactionLabel,
            'requested_by' => $requestedBy,
            'purpose'      => $purpose,
        ]);

        if ($saved) {
            Session::setFlash('success', 'Document request submitted successfully. Please wait for admin processing.');
            header('Location: index.php?controller=constituent&action=myRequests&tab=document');
            exit;
        }

        Session::setFlash('error', 'Failed to submit document request. Please try again.');
        header('Location: index.php?controller=constituent&action=requestDocument');
        exit;
    }

    public function profile(): void
    {
        $this->verifyConstituent();
        $this->enforcePasswordChange();
        date_default_timezone_set('Asia/Manila');
        $user = $this->userModel->findById((int)$_SESSION['user_id']);
        if (!$user) {
            Session::setFlash('error', 'Unable to load profile.');
            header('Location: index.php?controller=auth&action=logout');
            exit;
        }

        $data = [];
        if (!empty($user['constituent_id'])) {
            $data = $this->buildDataFromConstituent((int)$user['constituent_id']);
        }

        $profileRequest        = null;
        $profileRequestHistory = [];
        try {
            $profileRequest        = $this->profileRequestModel->getLatestByUser((int)$user['id']);
            $profileRequestHistory = $this->profileRequestModel->getHistoryByUser((int)$user['id'], 10);
            if ($profileRequest && in_array($profileRequest['status'], ['pending', 'rejected'])) {
                $payload = json_decode((string)$profileRequest['payload_json'], true);
                if (is_array($payload)) {
                    $data = array_merge($data, $payload);
                }
            }
        } catch (Exception $e) {
            $profileRequestHistory = [];
        }

        $this->render('constituent/profile', [
            'title'                  => 'My Profile',
            'classifications'        => $this->classificationsModel->getAllClassifications(),
            'data'                   => $data,
            'errors'                 => [],
            'profileRequest'         => $profileRequest,
            'profileRequestHistory'  => $profileRequestHistory,
            'isLinkedToConstituent'  => !empty($user['constituent_id']),
        ]);
    }

    public function accountSettings(): void
    {
        $this->verifyConstituent();
        $this->enforcePasswordChange();

        $user = $this->userModel->findById((int)$_SESSION['user_id']);
        if (!$user) {
            Session::setFlash('error', 'Unable to load account settings.');
            header('Location: index.php?controller=auth&action=logout');
            exit;
        }

        $this->render('constituent/account_settings', [
            'title'  => 'Account Settings',
            'data'   => [
                'id'       => (int)$user['id'],
                'username' => $user['username'] ?? '',
                'fullname' => $user['fullname'] ?? '',
                'email'    => $user['email'] ?? '',
            ],
            'errors' => [],
        ]);
    }

    public function saveAccountSettings(): void
    {
        $this->verifyConstituent();
        $this->enforcePasswordChange();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=constituent&action=accountSettings');
            exit;
        }
        $this->verifyCsrf();

        $userId = (int)$_SESSION['user_id'];
        $user   = $this->userModel->findById($userId);
        if (!$user) {
            Session::setFlash('error', 'User not found.');
            header('Location: index.php?controller=auth&action=logout');
            exit;
        }

        $username        = trim((string)($_POST['username']         ?? ''));
        $fullname        = trim((string)($_POST['fullname']         ?? ''));
        $email           = trim((string)($_POST['email']            ?? ''));
        $password        = (string)($_POST['password']              ?? '');
        $confirmPassword = (string)($_POST['confirm_password']      ?? '');

        $errors = [];

        if ($username === '') {
            $errors['username'] = 'Username is required';
        } elseif (strlen($username) < 3) {
            $errors['username'] = 'Username must be at least 3 characters';
        } else {
            $existing = $this->userModel->findByUsername($username);
            if ($existing && (int)$existing['id'] !== $userId) {
                $errors['username'] = 'Username is already taken';
            }
        }

        if ($fullname === '') {
            $errors['fullname'] = 'Full name is required';
        } elseif (strlen($fullname) < 2) {
            $errors['fullname'] = 'Full name must be at least 2 characters';
        }

        if ($email !== '') {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Please enter a valid email address';
            } else {
                $existingEmail = $this->userModel->findByEmail($email);
                if ($existingEmail && (int)$existingEmail['id'] !== $userId) {
                    $errors['email'] = 'This email address is already in use';
                }
            }
        }

        if ($password !== '') {
            if (strlen($password) < 8) {
                $errors['password'] = 'Password must be at least 8 characters';
            }
            if ($password !== $confirmPassword) {
                $errors['confirm_password'] = 'Passwords do not match';
            }
        }

        if (!empty($errors)) {
            $this->render('constituent/account_settings', [
                'title'  => 'Account Settings',
                'data'   => [
                    'id'       => $userId,
                    'username' => $username,
                    'fullname' => $fullname,
                    'email'    => $email,
                ],
                'errors' => $errors,
            ]);
            return;
        }

        $updateData = [
            'username' => $username,
            'fullname' => $fullname,
            'email'    => $email !== '' ? $email : null,
        ];

        if ($password !== '') {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->userModel->update($userId, $updateData)) {
            $_SESSION['username'] = $username;
            $_SESSION['fullname'] = $fullname;
            Session::setFlash('success', 'Account settings updated successfully.');
        } else {
            Session::setFlash('error', 'Failed to update account settings.');
        }

        header('Location: index.php?controller=constituent&action=accountSettings');
        exit;
    }

    // ── Forced password change ────────────────────────────────────────────────

    /**
     * Show the forced first-login password change form.
     * NOTE: enforcePasswordChange() is intentionally NOT called here — that
     * would create an infinite redirect loop.
     */
    public function changePassword(): void
    {
        $this->verifyConstituent();
    
        // If they've already changed their password, redirect away
        if (!$this->isUsingDefaultPassword()) {
            header('Location: index.php?controller=constituent');
            exit;
        }
    
        $user   = $this->userModel->findById((int)$_SESSION['user_id']);
        $errors = [];
    
        // Render as a standalone page — bypasses the sidebar layout entirely
        require_once 'app/Views/constituent/change_password.php';
    }
    
    public function saveChangePassword(): void
    {
        $this->verifyConstituent();
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=constituent&action=changePassword');
            exit;
        }
        $this->verifyCsrf();
    
        $userId = (int)$_SESSION['user_id'];
        $user   = $this->userModel->findById($userId);
    
        if (!$user) {
            Session::setFlash('error', 'User not found.');
            header('Location: index.php?controller=auth&action=logout');
            exit;
        }
    
        $newPassword     = (string)($_POST['new_password']     ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');
        $username        = (string)($user['username']          ?? '');
    
        $errors = [];
    
        if ($newPassword === '') {
            $errors['new_password'] = 'New password is required.';
        } elseif (strlen($newPassword) < 8) {
            $errors['new_password'] = 'Password must be at least 8 characters.';
        } elseif ($newPassword === $username) {
            $errors['new_password'] = 'Your new password cannot be the same as your username.';
        }
    
        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }
    
        if (!empty($errors)) {
            // Re-render standalone page with errors
            require_once 'app/Views/constituent/change_password.php';
            return;
        }
    
        if ($this->userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ])) {
            Session::remove('force_password_change');
            Session::setFlash('toast_success', 'Password set successfully! Welcome to your dashboard.');
            header('Location: index.php?controller=constituent');
            exit;
        }
    
        $errors['new_password'] = 'Failed to update password. Please try again.';
        require_once 'app/Views/constituent/change_password.php';
    }
 

    // ── Profile helpers ───────────────────────────────────────────────────────

    private function sanitizeInput($input, $default = null, $toUpper = true)
    {
        if (!isset($input) || trim($input) === '') {
            return $default;
        }
        $input = trim($input);
        return $toUpper && is_string($input) ? strtoupper($input) : $input;
    }

    private function sanitizeData($postData): array
    {
        $data = [
            'psn'                    => !empty($postData['psn']) ? $this->sanitizeInput($postData['psn']) : null,
            'last_name'              => $this->sanitizeInput($postData['last_name']            ?? null),
            'first_name'             => $this->sanitizeInput($postData['first_name']           ?? null),
            'middle_name'            => $this->sanitizeInput($postData['middle_name']          ?? null),
            'suffix'                 => $this->sanitizeInput($postData['suffix']               ?? null, null, false),
            'birthdate'              => $this->sanitizeInput($postData['birthdate']            ?? null),
            'birthplace'             => $this->sanitizeInput($postData['birthplace']           ?? null) ?? 'TACLOBAN CITY',
            'sex'                    => $this->sanitizeInput($postData['sex']                  ?? null) ?? 'MALE',
            'civil_status'           => $this->sanitizeInput($postData['civil_status']         ?? null) ?? 'SINGLE',
            'religion'               => $this->sanitizeInput($postData['religion']             ?? null) ?? 'ROMAN CATHOLIC',
            'citizenship'            => $this->sanitizeInput($postData['citizenship']          ?? null) ?? 'FILIPINO',
            'citizenship_others'     => $this->sanitizeInput($postData['citizenship_others']   ?? null),
            'occupation'             => $this->sanitizeInput($postData['occupation']           ?? null, null, false),
            'contact'                => $this->sanitizeInput($postData['contact']              ?? null, null, false),
            'email'                  => !empty($postData['email']) ? strtolower(trim($postData['email'])) : null,
            'education_attainment'   => $this->sanitizeInput($postData['education_attainment'] ?? null) ?? '1',
            'is_graduate'            => $this->sanitizeInput($postData['is_graduate']          ?? null) ?? 'NO',
            'registered_voter'       => $this->sanitizeInput($postData['registered_voter']     ?? null) ?? 'NO',
            'classifications'        => array_map('strval', $postData['classifications'] ?? []),
            'classification_org_ids' => [],
        ];

        if (isset($postData['classification_org_ids']) && is_array($postData['classification_org_ids'])) {
            foreach ($postData['classification_org_ids'] as $key => $value) {
                $data['classification_org_ids'][$key] = empty(trim($value)) ? null : $value;
            }
        }
        foreach (array_keys($data['classification_org_ids']) as $orgKey) {
            if (!in_array((string)$orgKey, $data['classifications'])) {
                $data['classifications'][] = (string)$orgKey;
            }
        }

        if ($data['citizenship'] === 'OTHERS' && !empty($postData['citizenship_others'])) {
            $data['citizenship'] = $this->sanitizeInput($postData['citizenship_others']);
        }

        return $data;
    }

    private function applyAutoClassifications(array &$data): void
    {
        $birthdate = $data['birthdate'] ?? '';
        if (!empty($birthdate) && $this->isSeniorCitizen($birthdate)) {
            $scClassification = $this->classificationsModel->getByCode('SC');
            if ($scClassification) {
                $scId = $scClassification['id'];
                if (!in_array($scId, $data['classifications'])) {
                    $data['classifications'][] = $scId;
                    if (!isset($data['classification_org_ids'][$scId])) {
                        $data['classification_org_ids'][$scId] = null;
                    }
                }
            }
        }

        $employedOccupations = [
            'Government Employee', 'Private Employee', 'OFW', 'Business',
            'Self-Employed', 'Carpenter', 'Laborer/Construction', 'Driver', 'Sari-Sari Store',
        ];

        $occupation = $data['occupation'] ?? '';
        if (in_array($occupation, $employedOccupations)) {
            foreach ($this->classificationsModel->getAllClassifications() as $classification) {
                $label = strtolower(trim($classification['name']));
                if ($label === 'labor/employed' ||
                    ($label !== 'unemployed' && strpos($label, 'labor') !== false && strpos($label, 'employed') !== false)) {
                    $laborId = $classification['id'];
                    if (!in_array($laborId, $data['classifications'])) {
                        $data['classifications'][] = $laborId;
                        if (!isset($data['classification_org_ids'][$laborId])) {
                            $data['classification_org_ids'][$laborId] = null;
                        }
                    }
                    break;
                }
            }
        }

        if ($occupation === 'OFW') {
            $ofwClassification = $this->classificationsModel->getByCode('OFW');
            if ($ofwClassification) {
                $ofwId = $ofwClassification['id'];
                if (!in_array($ofwId, $data['classifications'])) {
                    $data['classifications'][] = $ofwId;
                    if (!isset($data['classification_org_ids'][$ofwId])) {
                        $data['classification_org_ids'][$ofwId] = null;
                    }
                }
            }
        }
    }

    private function calculateAge($birthdate): int
    {
        if (empty($birthdate)) return 0;
        return (new DateTime($birthdate))->diff(new DateTime('today'))->y;
    }

    private function isSeniorCitizen($birthdate): bool
    {
        return $this->calculateAge($birthdate) >= 60;
    }

    private function buildDataFromConstituent(int $constituentId): array
    {
        $constituent = $this->constituentsModel->get($constituentId);
        if (!$constituent) return [];

        $data = [
            'psn'                  => $constituent['psn']                  ?? '',
            'last_name'            => $constituent['last_name']            ?? '',
            'first_name'           => $constituent['first_name']           ?? '',
            'middle_name'          => $constituent['middle_name']          ?? '',
            'suffix'               => $constituent['suffix']               ?? '',
            'sex'                  => strtoupper($constituent['sex']       ?? ''),
            'birthdate'            => $constituent['birthdate']            ?? '',
            'birthplace'           => $constituent['birthplace']           ?? '',
            'civil_status'         => strtoupper($constituent['civil_status']  ?? ''),
            'religion'             => strtoupper($constituent['religion']      ?? ''),
            'citizenship'          => strtoupper($constituent['citizenship']   ?? ''),
            'occupation'           => $constituent['occupation']           ?? '',
            'contact'              => $constituent['contact']              ?? '',
            'email'                => $constituent['email']                ?? '',
            'education_attainment' => (string)($constituent['education_attainment'] ?? ''),
            'is_graduate'          => strtoupper($constituent['is_graduate']    ?? ''),
            'registered_voter'     => strtoupper($constituent['registered_voter'] ?? ''),
            'classifications'        => [],
            'classification_org_ids' => [],
        ];

        foreach ($this->constituentsClassificationsModel->getConstituentClassifications($constituentId) as $cl) {
            $classId = (string)$cl['classification_id'];
            $data['classifications'][]                = $classId;
            $data['classification_org_ids'][$classId] = $cl['org_id_no'];
        }

        return $data;
    }

    public function saveProfile(): void
    {
        $this->verifyConstituent();
        $this->enforcePasswordChange();
        date_default_timezone_set('Asia/Manila');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=constituent&action=profile');
            exit;
        }
        $this->verifyCsrf();

        $user = $this->userModel->findById((int)$_SESSION['user_id']);
        if (!$user) {
            Session::setFlash('error', 'User not found.');
            header('Location: index.php?controller=auth&action=logout');
            exit;
        }

        $data = $this->sanitizeData($_POST);
        if (!empty($user['constituent_id'])) {
            $data['id'] = (int)$user['constituent_id'];
        }
        $this->applyAutoClassifications($data);

        $errors = $this->constituentsValidator->updateValidate($data);

        if (!empty($errors)) {
            $latestRequest = null;
            try {
                $latestRequest = $this->profileRequestModel->getLatestByUser((int)$user['id']);
            } catch (Exception $e) {}

            $this->render('constituent/profile', [
                'title'                 => 'My Profile',
                'classifications'       => $this->classificationsModel->getAllClassifications(),
                'data'                  => $data,
                'errors'                => $errors,
                'profileRequest'        => $latestRequest,
                'profileRequestHistory' => !empty($latestRequest) ? [$latestRequest] : [],
                'isLinkedToConstituent' => !empty($user['constituent_id']),
            ]);
            return;
        }

        try {
            $pending       = $this->profileRequestModel->findPendingByUser((int)$user['id']);
            $payloadJson   = json_encode($data);
            $constituentId = !empty($user['constituent_id']) ? (int)$user['constituent_id'] : null;

            if ($pending) {
                $saved = $this->profileRequestModel->updatePending((int)$pending['id'], $constituentId, $payloadJson);
            } else {
                $saved = $this->profileRequestModel->create([
                    'user_id'        => (int)$user['id'],
                    'constituent_id' => $constituentId,
                    'payload_json'   => $payloadJson,
                ]);
            }

            if ($saved) {
                Session::setFlash('success', 'Profile submission saved and sent for admin review.');
            } else {
                Session::setFlash('error', 'Failed to submit profile for review.');
            }
        } catch (Exception $e) {
            Session::setFlash('error', 'Profile review table not found yet. Please run the updated SQL first.');
        }

        header('Location: index.php?controller=constituent&action=profile');
        exit;
    }
    public function requestVehicle(): void
    {
        $title = 'Request Vehicle Registration';
        $old   = [];
        require_once 'app/Views/constituent/request_vehicle.php';
    }
    
    public function submitVehicleRequest(): void
    {
        $userId = (int)($_SESSION['user_id'] ?? 0);
    
        // Resolve constituent id from user id
        $db     = (new Database())->connect();
        // Simpler approach — look up constituent linked to this user account
        $userRow = $db->prepare("SELECT constituent_id FROM users WHERE id = :uid LIMIT 1");
        $userRow->execute([':uid' => $userId]);
        $uRow = $userRow->fetch(PDO::FETCH_ASSOC);
        $constituentId = !empty($uRow['constituent_id']) ? (int)$uRow['constituent_id'] : null;
    
        $data = [
            'user_id'        => $userId,
            'constituent_id' => $constituentId,
            'plate_number'   => strtoupper(trim($_POST['plate_number'] ?? '')),
            'or_number'      => trim($_POST['or_number']      ?? ''),
            'cr_number'      => trim($_POST['cr_number']      ?? ''),
            'vehicle_type'   => trim($_POST['vehicle_type']   ?? ''),
            'vehicle_use'    => trim($_POST['vehicle_use']    ?? 'Private'),
            'make'           => trim($_POST['make']           ?? ''),
            'model'          => trim($_POST['model']          ?? ''),
            'year'           => (int)($_POST['year']          ?? 0),
            'color'          => trim($_POST['color']          ?? ''),
            'fuel_type'      => trim($_POST['fuel_type']      ?? ''),
            'transmission'   => trim($_POST['transmission']   ?? ''),
            'engine_number'  => trim($_POST['engine_number']  ?? ''),
            'chassis_number' => trim($_POST['chassis_number'] ?? ''),
            'notes'          => trim($_POST['notes']          ?? ''),
        ];
    
        $errors = [];
        if ($data['vehicle_type'] === '') $errors[] = 'Vehicle type is required.';
        if ($data['make'] === '')         $errors[] = 'Make / brand is required.';
        if ($data['year'] < 1900 || $data['year'] > (int)date('Y') + 1) $errors[] = 'Please enter a valid year.';
        if ($data['color'] === '')        $errors[] = 'Color is required.';
    
        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            $old   = $data;
            $title = 'Request Vehicle Registration';
            require_once 'app/Views/constituent/request_vehicle.php';
            return;
        }
    
        $requestModel = new VehicleRequest();
        $requestModel->create($data);
        Session::setFlash('success', 'Your vehicle registration request has been submitted and is pending review.');
        header('Location: index.php?controller=constituent&action=myRequests&tab=vehicle');
        exit;
    }
     public function myVehicles(): void
    {
        $this->verifyConstituent();
        $this->enforcePasswordChange();
 
        // Resolve constituent_id from session user
        $db      = (new Database())->connect();
        $stmt    = $db->prepare("SELECT constituent_id FROM users WHERE id = :uid LIMIT 1");
        $stmt->execute([':uid' => (int)$_SESSION['user_id']]);
        $row     = $stmt->fetch(PDO::FETCH_ASSOC);
        $constituentId = !empty($row['constituent_id']) ? (int)$row['constituent_id'] : 0;
 
        $vehicles = [];
        if ($constituentId > 0) {
            $vehicleModel = new Vehicle();
            $vehicles     = $vehicleModel->getByOwnerId($constituentId);
        }
 
        $title = 'My Registered Vehicles';
        $this->render('constituent/my_vehicles', [
            'title'    => $title,
            'vehicles' => $vehicles,
        ]);
    }
 
    /**
     * Show the detail page for a single approved vehicle the constituent owns.
     */
    public function myVehicleView(): void
    {
        $this->verifyConstituent();
        $this->enforcePasswordChange();
 
        $id = (int)($_GET['id'] ?? 0);
 
        // Resolve constituent_id
        $db   = (new Database())->connect();
        $stmt = $db->prepare("SELECT constituent_id FROM users WHERE id = :uid LIMIT 1");
        $stmt->execute([':uid' => (int)$_SESSION['user_id']]);
        $row           = $stmt->fetch(PDO::FETCH_ASSOC);
        $constituentId = !empty($row['constituent_id']) ? (int)$row['constituent_id'] : 0;
 
        $vehicleModel = new Vehicle();
        $vehicle      = $vehicleModel->findById($id);
 
        // Ownership check — constituent may only view their own vehicles
        if (
            !$vehicle ||
            (int)($vehicle['owner_constituent_id'] ?? 0) !== $constituentId
        ) {
            Session::setFlash('error', 'Vehicle not found or access denied.');
            header('Location: index.php?controller=constituent&action=myVehicles');
            exit;
        }
 
        $title = 'Vehicle Details';
        $this->render('constituent/my_vehicle_view', [
            'title'   => $title,
            'vehicle' => $vehicle,
        ]);
    }
    public function submitVehicleEditRequest(): void
    {
        $this->verifyConstituent();
        $this->enforcePasswordChange();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=constituent&action=myVehicles');
            exit;
        }
        $this->verifyCsrf();

        $vehicleId = (int)($_POST['vehicle_id'] ?? 0);

        // Ownership check
        $db   = (new Database())->connect();
        $stmt = $db->prepare("SELECT constituent_id FROM users WHERE id = :uid LIMIT 1");
        $stmt->execute([':uid' => (int)$_SESSION['user_id']]);
        $row           = $stmt->fetch(PDO::FETCH_ASSOC);
        $constituentId = !empty($row['constituent_id']) ? (int)$row['constituent_id'] : 0;

        $vehicleModel = new Vehicle();
        $vehicle      = $vehicleModel->findById($vehicleId);

        if (!$vehicle || (int)($vehicle['owner_constituent_id'] ?? 0) !== $constituentId) {
            Session::setFlash('error', 'Vehicle not found or access denied.');
            header('Location: index.php?controller=constituent&action=myVehicles');
            exit;
        }

        // Check no pending edit request already exists for this vehicle
        $checkStmt = $db->prepare("
            SELECT COUNT(*) FROM vehicle_requests
            WHERE vehicle_id = :vid AND request_type = 'edit' AND status = 'pending'
        ");
        $checkStmt->execute([':vid' => $vehicleId]);
        if ((int)$checkStmt->fetchColumn() > 0) {
            Session::setFlash('error', 'You already have a pending edit request for this vehicle.');
            header('Location: index.php?controller=constituent&action=myVehicleView&id=' . $vehicleId);
            exit;
        }

        $data = [
            'user_id'        => (int)$_SESSION['user_id'],
            'constituent_id' => $constituentId,
            'vehicle_id'     => $vehicleId,
            'plate_number'   => strtoupper(trim($_POST['plate_number'] ?? '')),
            'or_number'      => trim($_POST['or_number']      ?? ''),
            'cr_number'      => trim($_POST['cr_number']      ?? ''),
            'vehicle_type'   => trim($_POST['vehicle_type']   ?? ''),
            'vehicle_use'    => trim($_POST['vehicle_use']    ?? 'Private'),
            'make'           => trim($_POST['make']           ?? ''),
            'model'          => trim($_POST['model']          ?? ''),
            'year'           => (int)($_POST['year']          ?? 0),
            'color'          => trim($_POST['color']          ?? ''),
            'fuel_type'      => trim($_POST['fuel_type']      ?? ''),
            'transmission'   => trim($_POST['transmission']   ?? ''),
            'engine_number'  => trim($_POST['engine_number']  ?? ''),
            'chassis_number' => trim($_POST['chassis_number'] ?? ''),
            'notes'          => trim($_POST['notes']          ?? ''),
        ];

        $errors = [];
        if ($data['vehicle_type'] === '') $errors[] = 'Vehicle type is required.';
        if ($data['make'] === '')         $errors[] = 'Make / brand is required.';
        if ($data['year'] < 1900 || $data['year'] > (int)date('Y') + 1) $errors[] = 'Please enter a valid year.';
        if ($data['color'] === '')        $errors[] = 'Color is required.';

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            header('Location: index.php?controller=constituent&action=myVehicleView&id=' . $vehicleId);
            exit;
        }

        $requestModel = new VehicleRequest();
        $requestModel->createEditRequest($data);

        Session::setFlash('success', 'Your edit request has been submitted and is pending secretary review.');
        header('Location: index.php?controller=constituent&action=myVehicleView&id=' . $vehicleId);
        exit;
    }
}