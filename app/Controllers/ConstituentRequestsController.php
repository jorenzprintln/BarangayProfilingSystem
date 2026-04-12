<?php

require_once __DIR__ . '/BaseController.php';

class ConstituentRequestsController extends BaseController
{
    private $profileRequestModel;
    private $transactionsModel;
    private $constituentsModel;
    private $userModel;
    private $barangayOfficialsModel;

    public function __construct()
    {
        date_default_timezone_set('Asia/Manila');
        $this->profileRequestModel    = new ConstituentProfileRequest();
        $this->transactionsModel      = new Transactions();
        $this->constituentsModel      = new Constituents();
        $this->userModel              = new User();
        $this->barangayOfficialsModel = new BarangayOfficials();
    }

    private function verifyAdmin(): void
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=constituent');
            exit;
        }
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
            $classificationNames[$classId] = $classificationMap[$classId]; // ← key by DB id
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
        'classification_names'   => $classificationNames,   // now [3=>'Senior Citizen', 7=>'PWD', 12=>'OFW']
        'classification_org_ids' => is_array($payload['classification_org_ids'] ?? null)
            ? $payload['classification_org_ids'] : [],      // still ['7' => '001234']
    ];
}

    // Map transaction label → form type key used for filenames
    private function getFormTypeKey(string $transaction): ?string
    {
        $map = [
            'Barangay Certificate'              => 'bc_general',
            'Barangay Indigency'                => 'co_indigency',
            'Certificate of Good Moral'         => 'bc_good_moral',
            'Barangay Certificate for Business' => 'bc_business',
            'Certificate of Unemployment'       => 'bc_unemployment',
            'Certificate of Solo Parent'        => 'co_solo_parent',
            'Barangay Certificate for OFW'      => 'bc_ofw',
        ];
        return $map[$transaction] ?? null;
    }

    // Map transaction label → which FormsController render view path
    private function getFormViewPath(string $transaction): ?string
    {
        $map = [
            'Barangay Certificate'              => 'forms/bc_general/bc_general_output',
            'Barangay Indigency'                => 'forms/co_indigency/co_indigency_output',
            'Certificate of Good Moral'         => 'forms/bc_good_moral/bc_gm_output',
            'Barangay Certificate for Business' => 'forms/bc_business/bc_business_output',
            'Certificate of Unemployment'       => 'forms/bc_unemployment/bc_unemployment_output',
            'Certificate of Solo Parent'        => 'forms/co_solo_parent/co_solo_parent_output',
            'Barangay Certificate for OFW'      => 'forms/bc_ofw/bc_ofw_output',
        ];
        return $map[$transaction] ?? null;
    }

    private function generateFilename(string $formTypeKey): string
    {
        $directory = 'public/forms';
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        $timestamp = date('Y-m-d_H-i-s');
        return str_replace(' ', '_', strtolower($formTypeKey)) . '_' . $timestamp . '.pdf';
    }

    public function profileRequests(): void
    {
        $this->verifyAdmin();

        $pendingProfileRequests = [];
        try {
            $pendingProfileRequests = $this->profileRequestModel->getPendingRequests();
            $classificationsModel   = new Classifications();
            $classifications        = $classificationsModel->getAllClassifications();
            $classificationMap      = [];
            foreach ($classifications as $classification) {
                $classificationMap[(int)$classification['id']] = $classification['code'];
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

        $this->render('home/constituent_requests/profile_requests', [
            'title'                  => 'Profile Requests',
            'pendingProfileRequests' => $pendingProfileRequests,
        ]);
    }

    public function documentRequests(): void
    {
        $this->verifyAdmin();
    
        $documentTypes = [
            'Barangay Certificate',
            'Barangay Indigency',
            'Certificate of Good Moral',
            'Barangay Certificate for Business',
            'Certificate of Unemployment',
            'Certificate of Solo Parent',
            'Barangay Certificate for OFW',
        ];

        $pendingDocumentRequests = [];
        try {
            $pendingDocumentRequests = $this->transactionsModel->getPendingConstituentRequests($documentTypes);

            foreach ($pendingDocumentRequests as &$docRequest) {
                $username    = (string)($docRequest['requested_by'] ?? '');
                $user        = $this->userModel->findByUsername($username);
                $constituent = null;

                if ($user && !empty($user['constituent_id'])) {
                    $constituent = $this->constituentsModel->get((int)$user['constituent_id']);
                }

                if ($constituent) {
                    // FIX: Pass individual name parts to the view so it can
                    // build "First [Middle] Last[, Suffix]" cleanly with no double spaces
                    $docRequest['first_name']  = $constituent['first_name']  ?? '';
                    $docRequest['middle_name'] = $constituent['middle_name'] ?? '';
                    $docRequest['last_name']   = $constituent['last_name']   ?? '';
                    $docRequest['suffix']      = $constituent['suffix']      ?? '';

                    // Keep requester_fullname as a pre-built fallback
                    $nameParts = array_filter([
                        trim($constituent['first_name']  ?? ''),
                        trim($constituent['middle_name'] ?? ''),
                        trim($constituent['last_name']   ?? ''),
                    ]);
                    $suffix = trim($constituent['suffix'] ?? '');
                    $docRequest['requester_fullname'] = implode(' ', $nameParts)
                        . ($suffix !== '' ? ', ' . $suffix : '');
                } else {
                    $docRequest['first_name']         = '';
                    $docRequest['middle_name']        = '';
                    $docRequest['last_name']          = '';
                    $docRequest['suffix']             = '';
                    $docRequest['requester_fullname'] = $username;
                }
            }
            unset($docRequest);

        } catch (Exception $e) {
            $pendingDocumentRequests = [];
        }

        $this->render('home/constituent_requests/document_requests', [
            'title'                   => 'Document Requests',
            'pendingDocumentRequests' => $pendingDocumentRequests,
        ]);
    }

    public function processDocumentRequest(): void
    {
        $this->verifyAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            Session::setFlash('error', 'Invalid request.');
            header('Location: index.php?controller=constituentRequests&action=documentRequests');
            exit;
        }

        $transaction = $this->transactionsModel->getById($id);
        if (!$transaction || strtoupper($transaction['generated_by']) !== 'PENDING') {
            Session::setFlash('error', 'Request not found or already processed.');
            header('Location: index.php?controller=constituentRequests&action=documentRequests');
            exit;
        }

        $transactionLabel = (string)($transaction['transaction'] ?? '');
        $formTypeKey      = $this->getFormTypeKey($transactionLabel);
        $viewPath         = $this->getFormViewPath($transactionLabel);

        if (!$formTypeKey || !$viewPath) {
            Session::setFlash('error', 'Unsupported document type: ' . $transactionLabel);
            header('Location: index.php?controller=constituentRequests&action=documentRequests');
            exit;
        }

        // Look up constituent via the user account that made the request
        $requestedByUsername = (string)($transaction['requested_by'] ?? '');
        $user        = $this->userModel->findByUsername($requestedByUsername);
        $constituent = null;

        if ($user && !empty($user['constituent_id'])) {
            $constituent = $this->constituentsModel->get((int)$user['constituent_id']);
        }

        if (!$constituent) {
            Session::setFlash('error', 'Could not find a linked constituent profile for user: ' . $requestedByUsername . '. Please link the account to a constituent first.');
            header('Location: index.php?controller=constituentRequests&action=documentRequests');
            exit;
        }

        $purpose        = (string)($transaction['purpose'] ?? '');
        $date           = date('jS \d\a\y \o\f F, Y');
        $generatedBy    = $_SESSION['username'] ?? 'Unknown User';
        $punongBarangay = $this->barangayOfficialsModel->getOfficialByRole('PUNONG BARANGAY');
        $barangaySecretary = $this->barangayOfficialsModel->getOfficialByRole('SECRETARY');

        $filename = $this->generateFilename($formTypeKey);

        // Build data array — same structure as FormsController
        $data = [
            'constituent'       => $constituent,
            'purpose'           => $purpose,
            'date'              => $date,
            'punongBarangay'    => $punongBarangay,
            'barangaySecretary' => $barangaySecretary,
            'filename'          => $filename,
        ];

        // Solo Parent needs dependents — default to empty since not collected at request time
        if ($formTypeKey === 'co_solo_parent') {
            $data['dependents'] = '';
        }

        // Indigency needs age
        if ($formTypeKey === 'co_indigency') {
            $constituentsModel  = new Constituents();
            $data['age']        = $constituentsModel->getAge($constituent['birthdate'] ?? '');
        }

        // OFW uses fullName key
        if ($formTypeKey === 'bc_ofw') {
            $data['fullName'] = strtoupper(trim(
                ($constituent['first_name']  ?? '') . ' ' .
                ($constituent['middle_name'] ?? '') . ' ' .
                ($constituent['last_name']   ?? '')
            ));
        }

        // Update the existing transaction record to mark it processed
        $this->transactionsModel->markProcessed(
            $id,
            $generatedBy,
            "public/forms/$filename"
        );

        // Clear output buffers and render the PDF view — exactly like FormsController does
        // Clear output buffers and stream the PDF — exactly like FormsController does
        while (ob_get_level()) {
            ob_end_clean();
        }
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);

        // Extract all data keys into local variables so the view can access them
        extract($data);

        require_once 'app/Views/' . $viewPath . '.php';
        exit;
    }

    public function rejectDocumentRequest(): void
    {
        $this->verifyAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=constituentRequests&action=documentRequests');
            exit;
        }

        $id     = (int)($_POST['request_id'] ?? 0);
        $reason = trim((string)($_POST['reason'] ?? ''));

        if (!$id) {
            Session::setFlash('error', 'Invalid request.');
            header('Location: index.php?controller=constituentRequests&action=documentRequests');
            exit;
        }

        if ($reason === '') {
            Session::setFlash('error', 'A rejection reason is required.');
            header('Location: index.php?controller=constituentRequests&action=documentRequests');
            exit;
        }

        $transaction = $this->transactionsModel->getById($id);
        if (!$transaction || strtoupper($transaction['generated_by']) !== 'PENDING') {
            Session::setFlash('error', 'Request not found or already processed.');
            header('Location: index.php?controller=constituentRequests&action=documentRequests');
            exit;
        }

        if ($this->transactionsModel->rejectConstituentRequest($id, $reason)) {
            Session::setFlash('success', 'Document request rejected successfully.');
        } else {
            Session::setFlash('error', 'Failed to reject request. Please try again.');
        }

        header('Location: index.php?controller=constituentRequests&action=documentRequests');
        exit;
    }
}