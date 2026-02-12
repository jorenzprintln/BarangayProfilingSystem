<?php

class HomeController extends BaseController
{
    public function index()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $constituentsModel = new Constituents();
        $constituentsClassificationsModel = new ConstituentsClassifications();
        $householdModel = new Households();
        $familyModel = new Family();
        $transactionsModel = new Transactions();

        $totalConstituents = $constituentsModel->getTotalCount();
        $totalHouseholds = count($householdModel->getAllHouseholds());
        $totalFamilies = $familyModel->countTotalFamilies();
        $totalSeniorCitizens = $constituentsClassificationsModel->getTotalSeniorCitizens();
        $totalSeniorCitizensbyAge = $constituentsModel->getTotalSeniorCitizensByAge();
        $transactions = $transactionsModel->getAllTransactions();

        $this->render('home/dashboard', [
            'title' => 'Dashboard',
            'user' => Session::get('username'),
            'totalConstituents' => $totalConstituents,
            'totalHouseholds' => $totalHouseholds,
            'totalFamilies' => $totalFamilies,
            'totalSeniorCitizens' => (!empty($totalSeniorCitizens) && $totalSeniorCitizens !== 0) ? $totalSeniorCitizens : $totalSeniorCitizensbyAge['TOTAL']['total'],
            'transactions' => $transactions
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
        $households = $householdModel->getHouseholdsWithInformation();

        $this->render('forms/rbi_form_A/rbi_select_household', [
            'title' => 'Forms',
            'households' => $households
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
