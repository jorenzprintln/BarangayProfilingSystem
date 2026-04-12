<?php

require_once __DIR__ . '/../Models/Vehicle.php';
require_once __DIR__ . '/../Models/VehicleRequest.php';

class VehiclesController extends BaseController
{
    /** @var \Vehicle */
    private $vehicleModel;
    private PDO     $db;

    public function __construct()
    {
        $vehicleClass       = class_exists('App\\Models\\Vehicle') ? 'App\\Models\\Vehicle' : 'Vehicle';
        $this->vehicleModel = new $vehicleClass();
        $this->db           = (new Database())->connect();
    }

    private function getActiveConstituents(): array
    {
        $stmt = $this->db->query(
            "SELECT id, first_name, middle_name, last_name, suffix
             FROM constituents
             WHERE removed_at IS NULL
             ORDER BY last_name, first_name"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── INDEX ──────────────────────────────────────────────────────────────
    public function index(): void
    {
        $perPage     = 10;
        $currentPage = max(1, (int)($_GET['page'] ?? 1));

        $filters = [
            'search'       => trim($_GET['search']       ?? ''),
            'vehicle_type' => trim($_GET['vehicle_type'] ?? ''),
            'fuel_type'    => trim($_GET['fuel_type']    ?? ''),
            'color'        => trim($_GET['color']        ?? ''),
        ];

        $offset       = ($currentPage - 1) * $perPage;
        $totalRecords = $this->vehicleModel->countAll($filters);
        $totalPages   = (int)ceil($totalRecords / $perPage);
        $vehicles     = $this->vehicleModel->getAll($filters, $perPage, $offset);

        $title = 'Vehicles';
        require_once 'app/Views/vehicles/index.php';
    }

    // ── ADD FORM ───────────────────────────────────────────────────────────
    public function add(): void
    {
        $constituents = $this->getActiveConstituents();
        $vehicle      = [];
        $title        = 'Register Vehicle';
        require_once 'app/Views/vehicles/form.php';
    }

    // ── STORE (create) ─────────────────────────────────────────────────────
    public function store(): void
    {
        $data   = $this->sanitizeVehicleInput($_POST);
        $errors = $this->validate($data);

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            header('Location: index.php?controller=vehicles&action=add');
            exit;
        }

        $this->vehicleModel->create($data);
        Session::setFlash('success', 'Vehicle registered successfully.');
        header('Location: index.php?controller=vehicles');
        exit;
    }

    // ── VIEW ───────────────────────────────────────────────────────────────
    public function view(): void
    {
        $id      = (int)($_GET['id'] ?? 0);
        $vehicle = $this->vehicleModel->findById($id);

        if (!$vehicle) {
            Session::setFlash('error', 'Vehicle not found.');
            header('Location: index.php?controller=vehicles');
            exit;
        }

        $title = 'Vehicle Details';
        require_once 'app/Views/vehicles/view.php';
    }

    // ── EDIT FORM ──────────────────────────────────────────────────────────
    public function edit(): void
    {
        $id      = (int)($_GET['id'] ?? 0);
        $vehicle = $this->vehicleModel->findById($id);

        if (!$vehicle) {
            Session::setFlash('error', 'Vehicle not found.');
            header('Location: index.php?controller=vehicles');
            exit;
        }

        $constituents = $this->getActiveConstituents();
        $title        = 'Edit Vehicle';
        require_once 'app/Views/vehicles/form.php';
    }

    // ── UPDATE ─────────────────────────────────────────────────────────────
    public function update(): void
    {
        $id     = (int)($_POST['id'] ?? 0);
        $data   = $this->sanitizeVehicleInput($_POST);
        $errors = $this->validate($data, $id);

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            header('Location: index.php?controller=vehicles&action=edit&id=' . $id);
            exit;
        }

        $this->vehicleModel->update($id, $data);
        Session::setFlash('success', 'Vehicle updated successfully.');
        header('Location: index.php?controller=vehicles');
        exit;
    }

    // ── DELETE ─────────────────────────────────────────────────────────────
    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if (!$this->vehicleModel->findById($id)) {
            Session::setFlash('error', 'Vehicle not found.');
            header('Location: index.php?controller=vehicles');
            exit;
        }

        $this->vehicleModel->delete($id);
        Session::setFlash('success', 'Vehicle deleted successfully.');
        header('Location: index.php?controller=vehicles');
        exit;
    }

    // ── HELPERS ────────────────────────────────────────────────────────────

    private function sanitizeVehicleInput(array $post): array
    {
        return [
            'plate_number'           => strtoupper(trim($post['plate_number']           ?? '')),
            'or_number'              => trim($post['or_number']              ?? ''),
            'cr_number'              => trim($post['cr_number']              ?? ''),
            'vehicle_type'           => trim($post['vehicle_type']           ?? ''),
            'vehicle_use'            => trim($post['vehicle_use']            ?? 'Private'),
            'make'                   => trim($post['make']                   ?? ''),
            'model'                  => trim($post['model']                  ?? ''),
            'year'                   => (int)($post['year']                  ?? 0),
            'color'                  => trim($post['color']                  ?? ''),
            'fuel_type'              => trim($post['fuel_type']              ?? ''),
            'transmission'           => trim($post['transmission']           ?? ''),
            'engine_number'          => trim($post['engine_number']          ?? ''),
            'chassis_number'         => trim($post['chassis_number']         ?? ''),
            'owner_type'             => trim($post['owner_type']             ?? 'constituent'),
            'owner_constituent_id'   => (int)($post['owner_constituent_id'] ?? 0) ?: null,
            'external_owner_name'    => trim($post['external_owner_name']    ?? ''),
            'external_owner_address' => trim($post['external_owner_address'] ?? ''),
            'notes'                  => trim($post['notes']                  ?? ''),
        ];
    }

    private function validate(array $input, int $excludeId = 0): array
    {
        $errors = [];

        $data = array_merge([
            'vehicle_type' => '',
            'make'         => '',
            'year'         => 0,
            'color'        => '',
            'plate_number' => '',
        ], $input);

        if ($data['vehicle_type'] === '') {
            $errors[] = 'Vehicle type is required.';
        }

        if ($data['make'] === '') {
            $errors[] = 'Make / brand is required.';
        }

        if ((int)$data['year'] < 1900 || (int)$data['year'] > (int)date('Y') + 1) {
            $errors[] = 'Please enter a valid year.';
        }

        if ($data['color'] === '') {
            $errors[] = 'Color is required.';
        }

        if ($data['plate_number'] !== '') {
            $existing = $this->vehicleModel->findByPlate($data['plate_number'], $excludeId);
            if ($existing) {
                $errors[] = 'A vehicle with this plate number already exists.';
            }
        }

        return $errors;
    }

    public function archivedVehicles(): void
    {
        $search           = trim($_GET['search'] ?? '');
        $archivedVehicles = $this->vehicleModel->getArchived($search);
        $title            = 'Archived Vehicles';
        require_once 'app/Views/vehicles/archived.php';
    }

    public function restoreVehicle(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if (!$this->vehicleModel->findArchivedById($id)) {
            Session::setFlash('error', 'Vehicle not found.');
            header('Location: index.php?controller=vehicles&action=archivedVehicles');
            exit;
        }

        $this->vehicleModel->restore($id);
        Session::setFlash('success', 'Vehicle restored successfully.');
        header('Location: index.php?controller=vehicles&action=archivedVehicles');
        exit;
    }
    public function vehicleRequests(): void
    {
        $requestClass = class_exists('App\\Models\\VehicleRequest') ? 'App\\Models\\VehicleRequest' : 'VehicleRequest';
        $requestModel = new $requestClass();
        $filterStatus = trim($_GET['status'] ?? 'pending');
        $search       = trim($_GET['search'] ?? '');
        $requests     = $requestModel->getAll($filterStatus, $search);
        $title        = 'Vehicle Requests';
        require_once 'app/Views/vehicles/requests.php';      // the admin queue view
    }
    
    public function approveVehicleRequest(): void
    {
        $id            = (int)($_POST['request_id'] ?? 0);
        $secretaryNote = trim($_POST['secretary_note'] ?? '');
        $reviewerId    = (int)($_SESSION['user_id'] ?? 0);

        $requestClass = class_exists('App\\Models\\VehicleRequest') ? 'App\\Models\\VehicleRequest' : 'VehicleRequest';
        $requestModel = new $requestClass();
        $req          = $requestModel->findById($id);

        if (!$req || $req['status'] !== 'pending') {
            Session::setFlash('error', 'Request not found or already processed.');
            header('Location: index.php?controller=vehicles&action=vehicleRequests');
            exit;
        }

        $result = $requestModel->approve($id, $reviewerId, $secretaryNote);

        if ($result === true) {
            Session::setFlash('success', 'Vehicle request approved and added to the registry.');
        } elseif ($result === 'duplicate_plate') {
            $plate = htmlspecialchars($req['plate_number'] ?? '');
            Session::setFlash('error', "Cannot approve: plate number \"{$plate}\" is already registered in the system. Please reject this request and ask the constituent to re-submit with the correct plate number.");
        } else {
            Session::setFlash('error', 'Something went wrong. Please try again.');
        }

        header('Location: index.php?controller=vehicles&action=vehicleRequests');
        exit;
    }
    
    public function rejectVehicleRequest(): void
    {
        $id            = (int)($_POST['request_id'] ?? 0);
        $secretaryNote = trim($_POST['secretary_note'] ?? '');
        $reviewerId    = (int)($_SESSION['user_id'] ?? 0);
    
        if ($secretaryNote === '') {
            Session::setFlash('error', 'A rejection reason is required.');
            header('Location: index.php?controller=vehicles&action=vehicleRequests');
            exit;
        }
    
        $requestClass = class_exists('App\\Models\\VehicleRequest') ? 'App\\Models\\VehicleRequest' : 'VehicleRequest';
        $requestModel = new $requestClass();
        $req          = $requestModel->findById($id);
    
        if (!$req || $req['status'] !== 'pending') {
            Session::setFlash('error', 'Request not found or already processed.');
            header('Location: index.php?controller=vehicles&action=vehicleRequests');
            exit;
        }
    
        $requestModel->reject($id, $reviewerId, $secretaryNote);
        Session::setFlash('success', 'Vehicle request rejected.');
        header('Location: index.php?controller=vehicles&action=vehicleRequests');
        exit;
    }
}