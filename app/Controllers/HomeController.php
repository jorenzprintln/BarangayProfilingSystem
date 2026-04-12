<?php

class HomeController extends BaseController
{
    public function index()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Vary: *");  // ← add this

        $constituentsModel = new Constituents();
        $householdModel = new Households();
        $familyModel = new Family();
        $transactionsModel = new Transactions();

        $perPage = 10;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $currentPage = max(1, $currentPage);

        // ✅ Use the correct filtered methods
        $totalConstituents  = $constituentsModel->getTotalConstituentsCount();   // WHERE removed_at IS NULL
        $totalHouseholds = $householdModel->getTotalHouseholdsCount();
        $totalFamilies = $familyModel->countTotalFamilies();
        $totalSeniorCitizens = $constituentsModel->getTotalSeniorCitizensCount(); // age-based, already fixed

        $offset       = ($currentPage - 1) * $perPage;
        $transactions = $transactionsModel->getTransactionsPaginated($perPage, $offset);
        $totalRecords = (int)$transactionsModel->getTotalTransactionsCount();
        $totalPages   = (int)ceil($totalRecords / $perPage);

        $this->render('home/dashboard/dashboard', [
            'title'              => 'Dashboard',
            'user'               => Session::get('username'),
            'toast_success'      => Session::hasFlash('toast_success') ? Session::getFlash('toast_success') : null,
            'totalConstituents'  => $totalConstituents,
            'totalHouseholds'    => $totalHouseholds,
            'totalFamilies'      => $totalFamilies,
            'totalSeniorCitizens'=> $totalSeniorCitizens,
            'transactions'       => $transactions,
            'currentPage'        => $currentPage,
            'totalPages'         => $totalPages,
            'totalRecords'       => $totalRecords,
            'perPage'            => $perPage
        ]);
    }
    public function forms()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $this->render('forms/index', [
            'title' => 'Forms',
        ]);
    }

        public function rbiASelectHousehold()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $householdModel = new Households();

        // NEW: read search term from GET
        $search = trim($_GET['search'] ?? '');

        // NEW: use targeted query when searching, full list otherwise
        if ($search !== '') {
            $households = $householdModel->searchHouseholdsWithInformation($search);
        } else {
            $households = $householdModel->getHouseholdsWithInformation();
        }

        $this->render('forms/rbi_form_A/rbi_select_household', [
            'title'      => 'Forms',
            'households' => $households,
            'search'     => $search,   // NEW: pass to view
        ]);
    }

    public function rbiBSelectConstituent()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $constituentsModel = new Constituents();
        $constituents = $constituentsModel->getAllNotRemoved();

        $this->render('forms/rbi_form_B/rbi_select_constituent', [
            'title' => 'Select Constituent',
            'constituents' => $constituents
        ]);
    }
}
