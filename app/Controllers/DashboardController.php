<?php

require_once __DIR__ . '/../Models/Constituents.php';
require_once __DIR__ . '/../Models/Households.php';
require_once __DIR__ . '/../Models/Family.php';
require_once __DIR__ . '/../Models/Transactions.php';
require_once __DIR__ . '/BaseController.php';

class DashboardController extends BaseController
{
    private $constituentsModel;
    private $householdModel;
    private $familyModel;
    private $transactionsModel;

    public function __construct()
    {
        $this->constituentsModel = new Constituents();
        $this->householdModel = new Households();
        $this->familyModel = new Family();
        $this->transactionsModel = new Transactions();
    }

    public function index()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        $perPage =10;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $currentPage = max(1, $currentPage);

        $totalConstituents = $this->constituentsModel->getTotalConstituentsCount();
        $totalHouseholds = $this->householdModel->getTotalHouseholdsCount();
        $totalFamilies = $this->familyModel->countTotalFamilies();
        $totalSeniorCitizens = $this->constituentsModel->getTotalSeniorCitizensCount();

        $offset = ($currentPage - 1) * $perPage;
        $transactions = $this->transactionsModel->getTransactionsPaginated($perPage, $offset);
        $totalRecords = $this->transactionsModel->getTotalTransactionsCount();
        $totalRecords = (int)$totalRecords;
        $totalPages = (int)ceil($totalRecords / $perPage);

        $this->render('home/dashboard/dashboard', [
            'title' => 'Dashboard',
            'totalConstituents' => $totalConstituents,
            'totalHouseholds' => $totalHouseholds,
            'totalFamilies' => $totalFamilies,
            'totalSeniorCitizens' => $totalSeniorCitizens,
            'transactions' => $transactions,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'perPage' => $perPage
        ]);
    }
    public function getCounts()
    {
        if (!Session::isLoggedIn()) {
            http_response_code(401);
            exit;
        }

        // Prevent caching of this endpoint
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo json_encode([
            'constituents'   => $this->constituentsModel->getTotalConstituentsCount(),
            'households'     => $this->householdModel->getTotalHouseholdsCount(),
            'families'       => $this->familyModel->countTotalFamilies(),
            'seniorCitizens' => $this->constituentsModel->getTotalSeniorCitizensCount(),
        ]);
        exit;
    }
}