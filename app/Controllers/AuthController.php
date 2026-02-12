<?php

require_once 'BaseController.php'; // Adjust the path as necessary

class AuthController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Handle user login.
     * Validates user input, checks credentials, and sets session variables if successful.
     * Redirects to home page on success, or re-renders login page with errors on failure.
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            // Validate input
            $errors = [];
            if (empty($username)) {
                $errors['username'] = 'Username is required';
            }
            if (empty($password)) {
                $errors['password'] = 'Password is required';
            }

            if (empty($errors)) {
                $user = $this->userModel->findByUsername($username);

                if ($user && md5($password) === $user['password']) {
                    Session::set('user_id', $user['id']);
                    Session::set('username', $user['username']);
                    Session::set('logged_in', true);

                    header('Location: index.php?controller=home');
                    exit;
                } else {
                    $errors['login'] = 'Invalid login credentials';
                }
            }

            $this->render('auth/login', ['errors' => $errors, 'username' => $username]);
        } else {
            if (Session::isLoggedIn()) {
                header('Location: index.php?controller=home');
                exit;
            }
            $this->render('auth/login');
        }
    }

    /**
     * Handle user registration.
     * Validates user input, checks for existing username/email, and creates a new user if valid.
     * Redirects to login page on success, or re-renders registration page with errors on failure.
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            $errors = [];

            if (empty($username)) {
                $errors['username'] = 'Username is required';
            } elseif ($this->userModel->findByUsername($username)) {
                $errors['username'] = 'Username is already taken';
            }

            if (empty($email)) {
                $errors['email'] = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email is invalid';
            } elseif ($this->userModel->findByEmail($email)) {
                $errors['email'] = 'Email is already taken';
            }

            if (empty($password)) {
                $errors['password'] = 'Password is required';
            } elseif (strlen($password) < 8) {
                $errors['password'] = 'Password must be at least 8 characters';
            }

            if ($password !== $confirmPassword) {
                $errors['confirm_password'] = 'Passwords do not match';
            }

            if (empty($errors)) {
                $hashedPassword = md5($password); // Using MD5 for shorter hash length

                if ($this->userModel->create($username, $email, $hashedPassword)) {
                    Session::setFlash('success', 'You are registered and can log in');
                    header('Location: index.php?controller=auth&action=login');
                    exit;
                } else {
                    $errors['register'] = 'Registration failed, please try again';
                }
            }

            $this->render('auth/register', [
                'errors' => $errors,
                'username' => $username,
                'email' => $email
            ]);
        } else {
            if (Session::isLoggedIn()) {
                header('Location: index.php?controller=home');
                exit;
            }
            $this->render('auth/register');
        }
    }

    /**
     * Handle user logout.
     * Destroys the session and redirects to the login page.
     */
    public function logout()
    {
        Session::destroy();
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
}
