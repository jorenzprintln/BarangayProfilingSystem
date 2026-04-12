<?php


class BaseController
{
    protected function render($view, $data = [])
    {
        if (Session::isLoggedIn()) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');

            if (Session::get('force_change_password')) {
                $userId = $_SESSION['user_id'] ?? 0;
                $allowedView = 'home/users/edit';
                if ($view !== $allowedView) {
                    header('Location: index.php?controller=users&action=edit&id=' . $userId);
                    exit;
                }
            }
        }

        extract($data);
        require_once VIEW_PATH . $view . '.php';
    }
    protected function verifyCsrf()
    {
        $token = $_POST['csrf_token'] ?? $_POST['_csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($token)) {
            http_response_code(403);
            die('Invalid CSRF token. Please refresh and try again.');
        }
    }
}
