<?php
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo "<pre>ERROR [$errno]: $errstr in $errfile on line $errline</pre>";
    die();
});

set_exception_handler(function($e) {
    echo "<pre>EXCEPTION: " . $e->getMessage() . "\nin " . $e->getFile() . " on line " . $e->getLine() . "\n\n" . $e->getTraceAsString() . "</pre>";
    die();
});
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Composer Autoloader (PHPMailer)
require_once __DIR__ . '/../vendor/autoload.php';

// Load Configurations
require_once 'app/config/config.php';
require_once 'app/config/database.php';

// Load utility classes
require_once 'utils/Session.php';
require_once 'utils/Validator.php';

// Load Models
require_once 'app/models/Database.php';
require_once 'app/models/User.php';
require_once 'app/models/Constituents.php';
require_once 'app/models/Classifications.php';
require_once 'app/models/ConstituentsClassifications.php';
require_once 'app/models/Households.php';
require_once 'app/models/Family.php';
require_once 'app/models/Transactions.php';
require_once 'app/models/ConstituentProfileRequest.php';
require_once 'app/models/Vehicle.php';
require_once 'app/models/VehicleRequest.php';
require_once 'app/Helpers/EmailOtp.php';
require_once 'app/Helpers/Mailer.php';

// Load Controllers
require_once 'app/Controllers/AuthController.php';
require_once 'app/Controllers/BaseController.php';
require_once 'app/Controllers/ConstituentsController.php';
require_once 'app/Controllers/HomeController.php';
require_once 'app/Controllers/HouseholdsController.php';
require_once 'app/Controllers/BarangayOfficialsController.php';
require_once 'app/Controllers/FormsController.php';
require_once 'app/Controllers/UsersController.php';
require_once 'app/Controllers/ConstituentRequestsController.php';
require_once 'app/Controllers/ConstituentController.php';
require_once 'app/Controllers/ClassificationsController.php';
require_once 'app/Controllers/FamilyController.php';
require_once 'app/Controllers/VehiclesController.php';

// Load Validators
require_once 'app/Controllers/Validators/ConstituentsValidator.php';
require_once 'app/Controllers/Validators/ClassificationsValidator.php';

// Routing
$controller = $_GET['controller'] ?? 'auth';
$action     = $_GET['action']     ?? 'login';

switch ($controller) {

    case 'auth':
        $controller = new AuthController();
        if ($action === 'login') {
            $controller->login();
        } elseif ($action === 'logout') {
            $controller->logout();
        } elseif ($action === 'adminRecovery') {
            $controller->adminRecovery();
        } else {
            $controller->login();
        }
        break;

    case 'home':
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if (($_SESSION['role'] ?? '') === 'constituent') {
            header('Location: index.php?controller=constituent');
            exit;
        }
        $controller = new HomeController();
        switch ($action) {
            case 'forms':
                $controller->forms();
                break;
            case 'rbiASelectHousehold':
                $controller->rbiASelectHousehold();
                break;
            case 'rbiBSelectConstituent':
                $controller->rbiBSelectConstituent();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    case 'constituents':
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=constituent');
            exit;
        }
        $controller = new ConstituentsController();
        switch ($action) {
            case 'view':
                $controller->view();
                break;
            case 'addConstituent':
                $controller->addConstituent();
                break;
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $controller->editConstituent();
                break;
            case 'update':
                $controller->update();
                break;
            case 'removedConstituents':
                $controller->removedConstituents();
                break;
            case 'removeConstituent':
                $controller->removeConstituent();
                break;
            case 'restoreConstituent':
                $controller->restoreConstituent();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    case 'households':
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=constituent');
            exit;
        }
        $controller = new HouseholdsController();
        switch ($action) {
            case 'view':
                $controller->view();
                break;
            case 'create':
                $controller->createView();
                break;
            case 'delete':
                $controller->delete();
                break;
            case 'addConstituents':
                $controller->addConstituents();
                break;
            case 'store':
                $controller->store();
                break;
            case 'storeConstituents':
                $controller->storeConstituents();
                break;
            case 'generate_rbi_A':
                $controller->generate_rbi_A();
                break;
            case 'removeMember':
                $controller->removeMember();
                break;
            case 'setHouseholdHead':
                $controller->setHouseholdHead();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    case 'officials':
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=constituent');
            exit;
        }
        $controller = new BarangayOfficialsController();
        switch ($action) {
            case 'addOfficial':
                $controller->addOfficial();
                break;
            case 'create':
                $controller->create($_POST);
                break;
            case 'delete':
                $controller->delete();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    case 'forms':
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=constituent');
            exit;
        }
        $controller = new FormsController();
        switch ($action) {
            case 'rbi_form_B':
                $controller->rbi_form_B();
                break;
            case 'coaEntry':
                $controller->coaEntry();
                break;
            case 'processCoaEntry':
                $controller->processCoaEntry();
                break;
            case 'bcbEntry':
                $controller->bcbEntry();
                break;
            case 'processBcbEntry':
                $controller->processBcbEntry();
                break;
            case 'bcOfwEntry':
                $controller->bcOfwEntry();
                break;
            case 'processBcOfwEntry':
                $controller->processBcOfwEntry();
                break;
            case 'firEntry':
                $controller->firEntry();
                break;
            case 'processFirEntry':
                $controller->processFirEntry();
                break;
            case 'bcGeneralEntry':
                $controller->bcGeneralEntry();
                break;
            case 'processBcGeneralEntry':
                $controller->processBcGeneralEntry();
                break;
            case 'bcGoodMoralEntry':
                $controller->bcGoodMoralEntry();
                break;
            case 'processBcGoodMoralEntry':
                $controller->processBcGoodMoralEntry();
                break;
            case 'bcUnemploymentEntry':
                $controller->bcUnemploymentEntry();
                break;
            case 'processBcUnemploymentEntry':
                $controller->processBcUnemploymentEntry();
                break;
            case 'coIndigencyEntry':
                $controller->coIndigencyEntry();
                break;
            case 'processCoIndigencyEntry':
                $controller->processCoIndigencyEntry();
                break;
            case 'coSoloParentEntry':
                $controller->coSoloParentEntry();
                break;
            case 'processCoSoloParentEntry':
                $controller->processCoSoloParentEntry();
                break;
            case 'downloadAllFormsToZip':
                $controller->downloadAllFormsToZip();
                break;
            case 'bcCustomEntry':
                $controller->bcCustomEntry();
                break;
            case 'processBcCustomEntry':
                $controller->processBcCustomEntry();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    case 'classifications':
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=constituent');
            exit;
        }
        $controller = new ClassificationsController();
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            default:
                break;
        }
        break;

    case 'family':
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=constituent');
            exit;
        }
        $controller = new FamilyController();
        switch ($action) {
            case 'createHouseholdFamily':
                $controller->createHouseholdFamily();
                break;
            case 'store':
                $controller->store();
                break;
            case 'addMember':
                $controller->addMember();
                break;
            case 'storeMember':
                $controller->storeMember();
                break;
            case 'delete':
                $controller->delete();
                break;
            case 'removeMemberFromFamily':
                $controller->removeMemberFromFamily();
                break;
            case 'getMembersJson':
                $controller->getMembersJson();
                break;
            case 'setFamilyHead':
                $controller->setFamilyHead();
                break;
            default:
                header('Location: index.php?controller=households&action=index');
                break;
        }
        break;

    case 'dashboard':
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=constituent');
            exit;
        }
        require_once 'app/Controllers/DashboardController.php';
        $controller = new DashboardController();
        switch ($action) {
            case 'index':
            default:
                $controller->index();
                break;
        }
        break;

    case 'users':
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=constituent');
            exit;
        }
        $controller = new UsersController();
        switch ($action) {
            case 'createView':
                if (defined('SINGLE_ADMIN_MODE') && SINGLE_ADMIN_MODE === true) {
                    Session::setFlash('error', 'Single-admin mode is enabled. Admin account management is disabled.');
                    header('Location: index.php?controller=users&tab=constituent');
                    exit;
                }
                $controller->createView();
                break;
            case 'store':
                if (defined('SINGLE_ADMIN_MODE') && SINGLE_ADMIN_MODE === true) {
                    Session::setFlash('error', 'Single-admin mode is enabled. Admin account management is disabled.');
                    header('Location: index.php?controller=users&tab=constituent');
                    exit;
                }
                $controller->store();
                break;
            case 'edit':
                if (defined('SINGLE_ADMIN_MODE') && SINGLE_ADMIN_MODE === true && (int)($_GET['id'] ?? 0) !== (int)($_SESSION['user_id'] ?? 0)) {
                    Session::setFlash('error', 'Single-admin mode is enabled. Admin account management is disabled.');
                    header('Location: index.php?controller=users&tab=constituent');
                    exit;
                }
                $controller->edit();
                break;
            case 'update':
                if (defined('SINGLE_ADMIN_MODE') && SINGLE_ADMIN_MODE === true && (int)($_POST['id'] ?? 0) !== (int)($_SESSION['user_id'] ?? 0)) {
                    Session::setFlash('error', 'Single-admin mode is enabled. Admin account management is disabled.');
                    header('Location: index.php?controller=users&tab=constituent');
                    exit;
                }
                $controller->update();
                break;
            case 'delete':
                if (defined('SINGLE_ADMIN_MODE') && SINGLE_ADMIN_MODE === true) {
                    Session::setFlash('error', 'Single-admin mode is enabled. Admin account management is disabled.');
                    header('Location: index.php?controller=users&tab=constituent');
                    exit;
                }
                $controller->delete();
                break;
            case 'toggleStatus':
                $controller->toggleStatus();
                break;
            case 'resetPassword':
                $controller->resetPassword();
                break;
            case 'archivedAccounts':
                $controller->archivedAccounts();
                break;
            case 'reApprove':
                $controller->reApprove();
                break;
            case 'removeRejected':
                $controller->removeRejected();
                break;
            case 'approveProfileRequest':
                $controller->approveProfileRequest();
                break;
            case 'rejectProfileRequest':
                $controller->rejectProfileRequest();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    case 'constituentRequests':
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=constituent');
            exit;
        }
        $controller = new ConstituentRequestsController();
        switch ($action) {
            case 'documentRequests':
                $controller->documentRequests();
                break;
            case 'processDocumentRequest':
                $controller->processDocumentRequest();
                break;
            case 'rejectDocumentRequest':
                $controller->rejectDocumentRequest();
                break;
            case 'profileRequests':
            default:
                $controller->profileRequests();
                break;
        }
        break;

    case 'vehicles':
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: index.php?controller=constituent');
            exit;
        }
        $controller = new VehiclesController();
        switch ($action) {
            case 'add':
                $controller->add();
                break;
            case 'store':
                $controller->store();
                break;
            case 'view':
                $controller->view();
                break;
            case 'edit':
                $controller->edit();
                break;
            case 'update':
                $controller->update();
                break;
            case 'delete':
                $controller->delete();
                break;
            case 'archivedVehicles':
                $controller->archivedVehicles();
                break;
            case 'restoreVehicle':
                $controller->restoreVehicle();
                break;
            case 'vehicleRequests':
                $controller->vehicleRequests();
                break;
            case 'approveVehicleRequest':
                $controller->approveVehicleRequest();
                break;
            case 'rejectVehicleRequest':
                $controller->rejectVehicleRequest();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    case 'constituent':
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
        if (($_SESSION['role'] ?? '') !== 'constituent') {
            header('Location: index.php?controller=home');
            exit;
        }
        $controller = new ConstituentController();
        switch ($action) {
            case 'profile':
                $controller->profile();
                break;
            case 'accountSettings':
                $controller->accountSettings();
                break;
            case 'saveProfile':
                $controller->saveProfile();
                break;
            case 'saveAccountSettings':
                $controller->saveAccountSettings();
                break;
            case 'myRequests':
                $controller->myRequests();
                break;
            case 'requestDocument':
                $controller->requestDocument();
                break;
            case 'requestDocumentPurpose':
                $controller->requestDocumentPurpose();
                break;
            case 'submitDocumentRequest':
                $controller->submitDocumentRequest();
                break;
            case 'changePassword':
                $controller->changePassword();
                break;
            case 'saveChangePassword':
                $controller->saveChangePassword();
                break;
            case 'requestVehicle':
                $controller->requestVehicle();
                break;
            case 'submitVehicleRequest':
                $controller->submitVehicleRequest();
                break;
            case 'myVehicles':
                $controller->myVehicles();
                break;
            case 'myVehicleView':
                $controller->myVehicleView();
                break;
            case 'submitVehicleEditRequest':
                $controller->submitVehicleEditRequest();
                break;
            default:
                $controller->index();
                break;
        }
        break;

    default:
        $controller = new AuthController();
        $controller->login();
        break;
}