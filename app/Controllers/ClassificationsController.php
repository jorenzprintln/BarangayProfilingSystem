<?php

class ClassificationsController extends BaseController
{
    private $classificationsModel;
    private $classificationsValidator;

    public function __construct()
    {
        $this->classificationsModel = new Classifications();
        $this->classificationsValidator = new ClassificationsValidator();
    }

    public function verifySession()
    {
        if (!Session::isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
        }
    }

    private function sanitizeInput($input, $default = null, $toUpper = true)
    {
        $input = isset($input) ? trim($input) : $default;
        return $toUpper && is_string($input) ? strtoupper($input) : $input;
    }

    private function sanitizeData($postData)
    {
        return [
            'code' => $this->sanitizeInput($postData['code']),
            'name' => $this->sanitizeInput($postData['name']),
            'organization' => $this->sanitizeInput($postData['organization']),
        ];
    }

    public function create()
    {
        $this->verifySession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->sanitizeData($_POST);
            $errors = $this->classificationsValidator->createValidate($data);

            if (empty($errors)) {
                try {
                    if ($this->classificationsModel->create($data)) {
                        Session::setFlash('success', 'Classification created successfully!');
                    }
                } catch (Exception $e) {
                    $errors['create_classification'] = $e->getMessage();
                }
            }
        }
    }

    public function getAll()
    {
        $this->verifySession();

        try {
            $classifications = $this->classificationsModel->getAllClassifications();
            echo json_encode($classifications);
            exit;
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    public function addConstituentClassifications()
    {
        $this->verifySession();

        
    }

}
