<?php

require_once __DIR__ . '/../Models/BarangayOfficials.php';
require_once __DIR__ . '/BaseController.php';

class BarangayOfficialsController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new BarangayOfficials();
    }

    public function index()
    {
        $officials = $this->model->getAllOfficials() ?? []; // Ensure $officials is always an array
        $constituents = $this->model->getConstituentsNotInOfficials() ?? []; // Get available constituents
        $this->render('home/officials/index', [
            'title' => 'Barangay Officials', 
            'officials' => $officials,
            'constituents' => $constituents // Pass constituents to the view
        ]);
    }

    public function show($id)
    {
        $official = $this->model->getOfficialById($id);
        $this->render('home/officials/show', ['official' => $official]);
    }

    public function addOfficial()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $barangayOfficialsModel = new BarangayOfficials();
        $availableConstituents = $barangayOfficialsModel->getConstituentsNotInOfficials();

        $this->render('home/officials/add_official', [
            'title' => 'Add Barangay Official',
            'constituents' => $availableConstituents
        ]);
    }

    public function create($data)
    {
        if (isset($data['constituents']) && is_array($data['constituents'])) {
            $this->model->addOfficials($data['constituents']);
        }
        header('Location: index.php?controller=officials');
    }

    public function edit($id, $data)
    {
        $this->model->updateOfficial($id, $data['constituent_id'], $data['role']);
        header('Location: /officials');
    }

    public function delete()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = intval($_GET['id']);
            $this->model->deleteOfficial($id);
        }

        header('Location: index.php?controller=officials');
        exit;
    }
}
