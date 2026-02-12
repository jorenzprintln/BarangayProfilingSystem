<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Load Configurations
require_once 'app/config/config.php';
require_once 'app/config/database.php';

// Load utility classes
require_once 'utils/Session.php';
require_once 'utils/Validator.php';

// Load Models
require_once 'app/models/User.php';
require_once 'app/models/Constituents.php';
require_once 'app/models/Database.php';
require_once 'app/models/Classifications.php';
require_once 'app/models/ConstituentsClassifications.php';
require_once 'app/models/Households.php';
require_once 'app/models/Family.php';
require_once 'app/models/Transactions.php';
// Load Controllers
require_once 'app/Controllers/AuthController.php';
require_once 'app/Controllers/BaseController.php';
require_once 'app/Controllers/ConstituentsController.php';
require_once 'app/Controllers/HomeController.php';
require_once 'app/Controllers/HouseholdsController.php';
require_once 'app/Controllers/BarangayOfficialsController.php';
require_once 'app/Controllers/FormsController.php';
require_once 'app/Controllers/ClassificationsController.php';
require_once 'app/Controllers/FamilyController.php';

// Load Validators
require_once 'app/Controllers/Validators/ConstituentsValidator.php';
require_once 'app/Controllers/Validators/ClassificationsValidator.php';

// Routing
$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

// Route to the appropriate controller and action
switch ($controller) {
    case 'auth':
        $controller = new AuthController();
        if ($action === 'login') {
            $controller->login();
        } elseif ($action === 'register') {
            $controller->register();
        } elseif ($action === 'logout') {
            $controller->logout();
        } else {
            $controller->login();
        }
        break;
    case 'home':
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
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
                $controller->generateRBIForm();
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
        $controller = new ClassificationsController();

        switch ($action) {
            case 'create':
                $controller->create();
                break;
            default:
                // $controller->index();
                break;
        }
        break;
    case 'family':
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
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
        }
    default:
        // Default to auth controller if not specified
        $controller = new AuthController();
        $controller->login();
        break;
}
