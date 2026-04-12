<?php

require_once __DIR__ . '/BaseController.php';

class UsersController extends BaseController
{
    private $userModel;
    private $profileRequestModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->profileRequestModel = new ConstituentProfileRequest();
    }

    private function verifyLogin()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        // Only admins can access user management
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    private function isSingleAdminMode(): bool
    {
        return defined('SINGLE_ADMIN_MODE') && SINGLE_ADMIN_MODE === true;
    }

    private function blockAdminAccountManagement(): void
    {
        if ($this->isSingleAdminMode()) {
            Session::setFlash('error', 'Single-admin mode is enabled. Admin account management is disabled.');
            header('Location: index.php?controller=users&tab=constituent');
            exit;
        }
    }

    /**
     * List all admin/official accounts
     */
    public function index()
    {
        $this->verifyLogin();

        $search = trim($_GET['search'] ?? '');
        $users  = $this->userModel->getAdminUsers();

        // [PENDING APPROVALS DISABLED] - Registration is disabled; accounts are now
        // auto-created by the secretary when adding a constituent. Pending approval
        // flow is no longer needed. Uncomment if self-registration is re-enabled.
        // $pendingUsers = $this->userModel->getPendingUsers();
        // $constituentsModel = new Constituents();
        // $unlinkedConstituents = $constituentsModel->getUnlinkedConstituents();
        $pendingUsers         = []; // always empty — no self-registration
        $unlinkedConstituents = [];

        // Filter admin accounts by search (client-side since small dataset)
        if (!empty($search)) {
            $users = array_values(array_filter($users, function ($user) use ($search) {
                $searchLower = strtolower($search);
                return str_contains(strtolower($user['username']), $searchLower)
                    || str_contains(strtolower($user['fullname'] ?? ''), $searchLower);
            }));
        }

        // Server-side search + pagination for constituent accounts
        $conSearch = trim($_GET['con_search'] ?? '');
        $conPage   = max(1, (int)($_GET['con_page'] ?? 1));
        $perPage   = 10;
        $conTotal  = $this->userModel->countConstituentAccounts($conSearch);
        $conTotalPages = max(1, (int)ceil($conTotal / $perPage));
        $conOffset = ($conPage - 1) * $perPage;
        $constituentAccounts = $this->userModel->getConstituentAccounts($conSearch, $perPage, $conOffset);

        $pendingProfileRequests = [];
        try {
            $pendingProfileRequests = $this->profileRequestModel->getPendingRequests();

            $classificationsModel = new Classifications();
            $classifications = $classificationsModel->getAllClassifications();
            $classificationMap = [];
            foreach ($classifications as $classification) {
                $classificationMap[(int)$classification['id']] = $classification['name'];
            }

            foreach ($pendingProfileRequests as &$pendingProfileRequest) {
                $payload = json_decode((string)($pendingProfileRequest['payload_json'] ?? ''), true);
                $pendingProfileRequest['payload_data'] = is_array($payload)
                    ? $this->normalizeProfilePayload($payload, $classificationMap)
                    : [];
            }
            unset($pendingProfileRequest);
        } catch (Exception $e) {
            $pendingProfileRequests = [];
        }

        $activeTab = $_GET['tab'] ?? 'admin';
        if ($this->isSingleAdminMode()) {
            $activeTab = 'constituent';
        }
        if ($this->isSingleAdminMode()) {
            $title = 'Manage Accounts';
        } else {
            $title = $activeTab === 'constituent' ? 'Manage Constituent Accounts' : 'Manage Admin Accounts';
        }

        $this->render('home/users/index', [
            'title'                  => $title,
            'users'                  => $users,
            'pendingUsers'           => $pendingUsers,           // always [] now
            'constituentAccounts'    => $constituentAccounts,
            'unlinkedConstituents'   => $unlinkedConstituents,   // always [] now
            'search'                 => $search,
            'conSearch'              => $conSearch,
            'conPage'                => $conPage,
            'conTotal'               => $conTotal,
            'conTotalPages'          => $conTotalPages,
            'pendingProfileRequests' => $pendingProfileRequests,
        ]);
    }

    private function buildFullnameFromPayload(array $payload): string
    {
        $parts = [
            trim((string)($payload['first_name'] ?? '')),
            trim((string)($payload['middle_name'] ?? '')),
            trim((string)($payload['last_name'] ?? '')),
            trim((string)($payload['suffix'] ?? '')),
        ];
        $name = trim(implode(' ', array_filter($parts)));
        return $name;
    }

    private function getProfileRequestsRedirect(): string
    {
        $default = 'index.php?controller=constituentRequests&action=profileRequests';
        $redirectTo = trim((string)($_POST['redirect_to'] ?? ''));
        if ($redirectTo === '') {
            return $default;
        }

        $allowed = [
            'index.php?controller=constituentRequests&action=profileRequests',
            'index.php?controller=users&tab=constituent',
        ];

        return in_array($redirectTo, $allowed, true) ? $redirectTo : $default;
    }

    private function normalizeProfilePayload(array $payload, array $classificationMap): array
    {
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

        return [
            'psn'                    => $payload['psn'] ?? '',
            'last_name'              => $payload['last_name'] ?? '',
            'first_name'             => $payload['first_name'] ?? '',
            'middle_name'            => $payload['middle_name'] ?? '',
            'suffix'                 => $payload['suffix'] ?? '',
            'sex'                    => $payload['sex'] ?? '',
            'birthdate'              => $payload['birthdate'] ?? '',
            'birthplace'             => $payload['birthplace'] ?? '',
            'civil_status'           => $payload['civil_status'] ?? '',
            'religion'               => $payload['religion'] ?? '',
            'citizenship'            => $payload['citizenship'] ?? '',
            'occupation'             => $payload['occupation'] ?? '',
            'contact'                => $payload['contact'] ?? '',
            'email'                  => $payload['email'] ?? '',
            'education_attainment'   => $payload['education_attainment'] ?? '',
            'is_graduate'            => $payload['is_graduate'] ?? '',
            'registered_voter'       => $payload['registered_voter'] ?? '',
            'classification_names'   => $classificationNames,
            'classification_org_ids' => is_array($payload['classification_org_ids'] ?? null)
                ? $payload['classification_org_ids']
                : [],
        ];
    }

    public function approveProfileRequest()
    {
        $this->verifyLogin();
        $redirectUrl = $this->getProfileRequestsRedirect();
        date_default_timezone_set('Asia/Manila');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $redirectUrl);
            exit;
        }
        $this->verifyCsrf();

        $requestId = (int)($_POST['request_id'] ?? 0);
        if ($requestId <= 0) {
            Session::setFlash('error', 'Invalid profile request.');
            header('Location: ' . $redirectUrl);
            exit;
        }

        $request = $this->profileRequestModel->findById($requestId);
        if (!$request || ($request['status'] ?? '') !== 'pending') {
            Session::setFlash('error', 'Profile request not found or already reviewed.');
            header('Location: ' . $redirectUrl);
            exit;
        }

        $payload = json_decode((string)$request['payload_json'], true);
        if (!is_array($payload)) {
            Session::setFlash('error', 'Invalid profile payload data.');
            header('Location: ' . $redirectUrl);
            exit;
        }
        $classificationsModel = new Classifications();
        $allClassifications   = $classificationsModel->getAllClassifications();

        $birthdate  = $payload['birthdate'] ?? '';
        $occupation = $payload['occupation'] ?? '';

        // Senior Citizen (age >= 60)
        if (!empty($birthdate)) {
            $age = (new DateTime($birthdate))->diff(new DateTime('today'))->y;
            if ($age >= 60) {
                foreach ($allClassifications as $cl) {
                    if ($cl['code'] === 'SC') {
                        $scId = (string)$cl['id'];
                        if (!in_array($scId, $payload['classifications'])) {
                            $payload['classifications'][] = $scId;
                        }
                        break;
                    }
                }
            }
        }

        // OFW occupation → OFW classification
        if ($occupation === 'OFW') {
            foreach ($allClassifications as $cl) {
                if ($cl['code'] === 'OFW') {
                    $ofwId = (string)$cl['id'];
                    if (!in_array($ofwId, $payload['classifications'])) {
                        $payload['classifications'][] = $ofwId;
                    }
                    break;
                }
            }
        }

        // Labor/Employed occupations → LABOR classification
        $employedOccupations = [
            'Government Employee','Private Employee','OFW','Business',
            'Self-Employed','Carpenter','Laborer/Construction','Driver','Sari-Sari Store',
        ];
        if (in_array($occupation, $employedOccupations)) {
            foreach ($allClassifications as $cl) {
                $label = strtolower(trim($cl['name']));
                if (strpos($label, 'labor') !== false && strpos($label, 'employed') !== false) {
                    $laborId = (string)$cl['id'];
                    if (!in_array($laborId, $payload['classifications'])) {
                        $payload['classifications'][] = $laborId;
                    }
                    break;
                }
            }
        }

        // Student occupation → STUDENT classification
        if ($occupation === 'Student') {
            foreach ($allClassifications as $cl) {
                if (strtoupper($cl['code']) === 'STUDENT') {
                    $studentId = (string)$cl['id'];
                    if (!in_array($studentId, $payload['classifications'])) {
                        $payload['classifications'][] = $studentId;
                    }
                    break;
                }
            }
        }
        $constituentsModel = new Constituents();
        $constituentsClassificationsModel = new ConstituentsClassifications();

        try {
            $constituentId = !empty($request['user_constituent_id']) ? (int)$request['user_constituent_id'] : null;

            if ($constituentId) {
                $payload['id'] = $constituentId;
                if (!$constituentsModel->update($payload)) {
                    throw new Exception('Failed to update linked constituent record.');
                }
            } else {
                $constituentId = (int)$constituentsModel->create($payload);
                if ($constituentId <= 0) {
                    throw new Exception('Failed to create constituent record.');
                }

                $this->userModel->update((int)$request['user_id'], [
                    'constituent_id' => $constituentId,
                ]);
            }

            $constituentsClassificationsModel->deleteByConstituentId($constituentId);
            if (!empty($payload['classifications']) && is_array($payload['classifications'])) {
                $constituentsClassificationsModel->create([
                    'constituent_id'         => $constituentId,
                    'classifications'        => $payload['classifications'],
                    'classification_org_ids' => $payload['classification_org_ids'] ?? [],
                ]);
            }

            $fullname = $this->buildFullnameFromPayload($payload);
            if ($fullname !== '') {
                $this->userModel->update((int)$request['user_id'], [
                    'fullname' => $fullname,
                ]);
            }

            $this->profileRequestModel->markApproved($requestId, (int)$_SESSION['user_id']);

            Session::setFlash('success', 'Profile request approved and constituent data has been updated.');
        } catch (Exception $e) {
            Session::setFlash('error', 'Failed to approve profile request: ' . $e->getMessage());
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    public function rejectProfileRequest()
    {
        $this->verifyLogin();
        $redirectUrl = $this->getProfileRequestsRedirect();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $redirectUrl);
            exit;
        }
        $this->verifyCsrf();

        $requestId = (int)($_POST['request_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');

        if ($requestId <= 0) {
            Session::setFlash('error', 'Invalid profile request.');
            header('Location: ' . $redirectUrl);
            exit;
        }

        $request = $this->profileRequestModel->findById($requestId);
        if (!$request || ($request['status'] ?? '') !== 'pending') {
            Session::setFlash('error', 'Profile request not found or already reviewed.');
            header('Location: ' . $redirectUrl);
            exit;
        }

        if ($reason === '') {
            Session::setFlash('error', 'Please provide a reject reason before submitting.');
            header('Location: ' . $redirectUrl);
            exit;
        }

        if ($this->profileRequestModel->markRejected($requestId, (int)$_SESSION['user_id'], $reason)) {
            Session::setFlash('success', 'Profile request rejected.');
        } else {
            Session::setFlash('error', 'Failed to reject profile request.');
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * Archived/rejected constituent accounts
     */
    public function archivedAccounts()
    {
        $this->verifyLogin();

        $search  = trim($_GET['search'] ?? '');
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $total   = $this->userModel->countRejectedAccounts($search);
        $totalPages = max(1, (int)ceil($total / $perPage));
        $offset  = ($page - 1) * $perPage;
        $rejectedAccounts = $this->userModel->getRejectedAccounts($search, $perPage, $offset);

        $constituentsModel = new Constituents();
        $unlinkedConstituents = $constituentsModel->getUnlinkedConstituents();

        $this->render('home/users/archived_accounts', [
            'title'                => 'Archived Accounts',
            'rejectedAccounts'     => $rejectedAccounts,
            'unlinkedConstituents' => $unlinkedConstituents,
            'search'               => $search,
            'currentPage'          => $page,
            'totalRecords'         => $total,
            'totalPages'           => $totalPages,
        ]);
    }

    /**
     * Re-approve a rejected account
     */
    public function reApprove()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=users&action=archivedAccounts');
            exit;
        }
        $this->verifyCsrf();

        $id            = $_POST['id'] ?? null;
        $constituentId = $_POST['constituent_id'] ?? null;

        if (!$id) {
            Session::setFlash('error', 'Invalid user');
            header('Location: index.php?controller=users&action=archivedAccounts');
            exit;
        }

        $user = $this->userModel->findById($id);
        if (!$user || $user['status'] !== 'rejected') {
            Session::setFlash('error', 'User not found or not rejected');
            header('Location: index.php?controller=users&action=archivedAccounts');
            exit;
        }

        $updateData = ['status' => 'approved'];
        if (!empty($constituentId)) {
            $updateData['constituent_id'] = (int)$constituentId;
        }

        if ($this->userModel->update($id, $updateData)) {
            $name = htmlspecialchars($user['fullname'] ?: $user['username']);
            $msg  = 'Account for "' . $name . '" has been approved';
            if (!empty($constituentId)) {
                $msg .= ' and linked to constituent record';
            }
            Session::setFlash('success', $msg);
        } else {
            Session::setFlash('error', 'Failed to approve account');
        }

        header('Location: index.php?controller=users&action=archivedAccounts');
        exit;
    }

    /**
     * Permanently remove a rejected account
     */
    public function removeRejected()
    {
        $this->verifyLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=users&action=archivedAccounts');
            exit;
        }
        $this->verifyCsrf();
        $id = $_POST['id'] ?? null;
        if (!$id) {
            Session::setFlash('error', 'Invalid user');
            header('Location: index.php?controller=users&action=archivedAccounts');
            exit;
        }
        $user = $this->userModel->findById($id);
        if (!$user || $user['status'] !== 'rejected') {
            Session::setFlash('error', 'User not found or not rejected');
            header('Location: index.php?controller=users&action=archivedAccounts');
            exit;
        }
        if ($this->userModel->hardDelete($id)) {
            Session::setFlash('success', 'Account for "' . htmlspecialchars($user['fullname'] ?: $user['username']) . '" has been permanently removed');
        } else {
            Session::setFlash('error', 'Failed to remove account');
        }
        header('Location: index.php?controller=users&action=archivedAccounts');
        exit;
    }

    /**
     * Show create user form
     */
    public function createView()
    {
        $this->verifyLogin();
        $this->blockAdminAccountManagement();

        $this->render('home/users/create', [
            'title' => 'Manage Admin Accounts',
        ]);
    }

    /**
     * Store a new admin/official user
     */
    public function store()
    {
        $this->verifyLogin();
        $this->blockAdminAccountManagement();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=users');
            exit;
        }
        $this->verifyCsrf();

        $username        = trim($_POST['username'] ?? '');
        $fullname        = trim($_POST['fullname'] ?? '');
        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        if (empty($username)) {
            $errors['username'] = 'Username is required';
        } elseif (strlen($username) < 3) {
            $errors['username'] = 'Username must be at least 3 characters';
        } elseif ($this->userModel->findByUsername($username)) {
            $errors['username'] = 'Username is already taken';
        }

        if (empty($fullname)) {
            $errors['fullname'] = 'Full name is required';
        } elseif (strlen($fullname) < 2) {
            $errors['fullname'] = 'Full name must be at least 2 characters';
        }

        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        }

        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }

        if (!empty($errors)) {
            $this->render('home/users/create', [
                'title'    => 'Manage Admin Accounts',
                'errors'   => $errors,
                'username' => $username,
                'fullname' => $fullname,
            ]);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($this->userModel->create($username, $hashedPassword, 'admin', 'approved', $fullname)) {
            Session::setFlash('success', 'Official account created successfully');
            header('Location: index.php?controller=users');
            exit;
        } else {
            $errors['general'] = 'Failed to create account. Please try again.';
            $this->render('home/users/create', [
                'title'    => 'Manage Admin Accounts',
                'errors'   => $errors,
                'username' => $username,
                'fullname' => $fullname,
            ]);
        }
    }

    /**
     * Show edit user form
     */
    public function edit()
    {
        $this->verifyLogin();

        $id = $_GET['id'] ?? null;

        if ($id != $_SESSION['user_id']) {
            Session::setFlash('error', 'You can only edit your own account');
            header('Location: index.php?controller=users');
            exit;
        }

        $user = $this->userModel->findById($id);

        if (!$user) {
            Session::setFlash('error', 'User not found');
            header('Location: index.php?controller=users');
            exit;
        }

        $this->render('home/users/edit', [
            'title'    => $this->isSingleAdminMode() ? 'Manage Accounts' : 'Manage Admin Accounts',
            'editUser' => $user,
        ]);
    }

    /**
     * Update an existing user
     */
    public function update()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=users');
            exit;
        }
        $this->verifyCsrf();

        $id = $_POST['id'] ?? null;

        if ($id != $_SESSION['user_id']) {
            Session::setFlash('error', 'You can only edit your own account');
            header('Location: index.php?controller=users');
            exit;
        }

        $username        = trim($_POST['username'] ?? '');
        $fullname        = trim($_POST['fullname'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $user = $id ? $this->userModel->findById($id) : null;

        if (!$user) {
            Session::setFlash('error', 'User not found');
            header('Location: index.php?controller=users');
            exit;
        }

        $errors = [];

        if (empty($username)) {
            $errors['username'] = 'Username is required';
        } elseif (strlen($username) < 3) {
            $errors['username'] = 'Username must be at least 3 characters';
        } else {
            $existing = $this->userModel->findByUsername($username);
            if ($existing && $existing['id'] != $id) {
                $errors['username'] = 'Username is already taken';
            }
        }

        if (empty($fullname)) {
            $errors['fullname'] = 'Full name is required';
        } elseif (strlen($fullname) < 2) {
            $errors['fullname'] = 'Full name must be at least 2 characters';
        }

        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Please enter a valid email address';
            } else {
                $existingEmail = $this->userModel->findByEmail($email);
                if ($existingEmail && $existingEmail['id'] != $id) {
                    $errors['email'] = 'This email address is already in use';
                }
            }
        }

        if (!empty($password)) {
            if (strlen($password) < 8) {
                $errors['password'] = 'Password must be at least 8 characters';
            }
            if ($password !== $confirmPassword) {
                $errors['confirm_password'] = 'Passwords do not match';
            }
        }

        if (!empty($errors)) {
            $this->render('home/users/edit', [
                'title'    => $this->isSingleAdminMode() ? 'Manage Accounts' : 'Manage Admin Accounts',
                'editUser' => array_merge($user, ['username' => $username, 'fullname' => $fullname, 'email' => $email]),
                'errors'   => $errors,
            ]);
            return;
        }

        $data = [
            'username' => $username,
            'fullname' => $fullname,
            'email'    => !empty($email) ? $email : null,
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->userModel->update($id, $data)) {
            if (Session::get('force_change_password') && !empty($data['password'])) {
                Session::remove('force_change_password');
                Session::setFlash('success', 'Password changed successfully! You can now use the system.');
                header('Location: index.php?controller=home&_=' . time());
                exit;
            }
            Session::setFlash('success', 'Account updated successfully');
        } else {
            Session::setFlash('error', 'Failed to update account');
        }

        header('Location: index.php?controller=users');
        exit;
    }

    /**
     * Soft-delete a user account
     */
    public function delete()
    {
        $this->verifyLogin();
        $this->blockAdminAccountManagement();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=users');
            exit;
        }
        $this->verifyCsrf();
        $id = $_POST['id'] ?? null;
        if (!$id) {
            Session::setFlash('error', 'Invalid user');
            header('Location: index.php?controller=users');
            exit;
        }
        if ($id == $_SESSION['user_id']) {
            Session::setFlash('error', 'You cannot delete your own account');
            header('Location: index.php?controller=users');
            exit;
        }
        if ($this->userModel->softDelete($id)) {
            Session::setFlash('success', 'Account has been removed');
        } else {
            Session::setFlash('error', 'Failed to remove account');
        }
        header('Location: index.php?controller=users');
        exit;
    }

    /**
     * Toggle constituent account status (activate/deactivate)
     */
    public function toggleStatus()
    {
        $this->verifyLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=users');
            exit;
        }
        $this->verifyCsrf();
        $id = $_POST['id'] ?? null;
        if (!$id) {
            Session::setFlash('error', 'Invalid user');
            header('Location: index.php?controller=users&tab=constituent');
            exit;
        }
        $user = $this->userModel->findById($id);
        if (!$user || $user['role'] !== 'constituent') {
            Session::setFlash('error', 'Invalid constituent account');
            header('Location: index.php?controller=users&tab=constituent');
            exit;
        }
        if ($this->userModel->toggleStatus($id)) {
            $newStatus = $user['status'] === 'approved' ? 'deactivated' : 'activated';
            Session::setFlash('success', 'Account has been ' . $newStatus);
        } else {
            Session::setFlash('error', 'Failed to update account status');
        }
        header('Location: index.php?controller=users&tab=constituent');
        exit;
    }

    /**
     * Reset password for a constituent account (admin action)
     */
    public function resetPassword()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=users');
            exit;
        }
        $this->verifyCsrf();

        $id              = (int)($_POST['id'] ?? 0);
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!$id) {
            Session::setFlash('error', 'Invalid user.');
            header('Location: index.php?controller=users');
            exit;
        }

        $user = $this->userModel->findById($id);
        if (!$user || $user['role'] !== 'constituent') {
            Session::setFlash('error', 'Constituent account not found.');
            header('Location: index.php?controller=users');
            exit;
        }

        if (strlen($newPassword) < 8) {
            Session::setFlash('error', 'Password must be at least 8 characters.');
            header('Location: index.php?controller=users');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            Session::setFlash('error', 'Passwords do not match.');
            header('Location: index.php?controller=users');
            exit;
        }

        if ($this->userModel->update($id, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ])) {
            $name = htmlspecialchars($user['fullname'] ?: $user['username']);
            Session::setFlash('success', 'Password for "' . $name . '" has been reset successfully.');
        } else {
            Session::setFlash('error', 'Failed to reset password. Please try again.');
        }

        header('Location: index.php?controller=users');
        exit;
    }

    // [PENDING APPROVALS DISABLED] - Self-registration is disabled. Accounts are now
    // auto-created by the secretary when adding a constituent. These methods are kept
    // for reference but will never be triggered. Uncomment if registration is re-enabled.

    /*
    public function approve()
    {
        $this->verifyLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=users');
            exit;
        }
        $this->verifyCsrf();
        $id = $_POST['id'] ?? null;
        $constituentId = $_POST['constituent_id'] ?? null;

        if (!$id) {
            Session::setFlash('error', 'Invalid user');
            header('Location: index.php?controller=users');
            exit;
        }

        $user = $this->userModel->findById($id);
        if (!$user || $user['status'] !== 'pending') {
            Session::setFlash('error', 'User not found or not pending');
            header('Location: index.php?controller=users');
            exit;
        }

        $updateData = ['status' => 'approved'];
        if (!empty($constituentId)) {
            $updateData['constituent_id'] = (int)$constituentId;
        }

        if ($this->userModel->update($id, $updateData)) {
            $name = htmlspecialchars($user['fullname'] ?: $user['username']);
            $msg = 'Account for "' . $name . '" has been approved';
            if (!empty($constituentId)) {
                $msg .= ' and linked to constituent record';
            }
            Session::setFlash('success', $msg);
        } else {
            Session::setFlash('error', 'Failed to approve account');
        }

        header('Location: index.php?controller=users');
        exit;
    }

    public function reject()
    {
        $this->verifyLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=users');
            exit;
        }
        $this->verifyCsrf();
        $id = $_POST['id'] ?? null;
        if (!$id) {
            Session::setFlash('error', 'Invalid user');
            header('Location: index.php?controller=users');
            exit;
        }
        $user = $this->userModel->findById($id);
        if (!$user || $user['status'] !== 'pending') {
            Session::setFlash('error', 'User not found or not pending');
            header('Location: index.php?controller=users');
            exit;
        }
        if ($this->userModel->update($id, ['status' => 'rejected'])) {
            Session::setFlash('success', 'Account for "' . htmlspecialchars($user['fullname'] ?: $user['username']) . '" has been rejected');
        } else {
            Session::setFlash('error', 'Failed to reject account');
        }
        header('Location: index.php?controller=users');
        exit;
    }
    */
}