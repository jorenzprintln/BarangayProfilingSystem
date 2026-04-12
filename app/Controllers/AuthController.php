<?php

require_once 'BaseController.php';

class AuthController extends BaseController
{
    private $userModel;
    // private $otpModel; // [OTP DISABLED] - uncomment when email verification is re-enabled

    public function __construct()
    {
        $this->userModel = new User();
        // $this->otpModel  = new EmailOtp(); // [OTP DISABLED] - uncomment when email verification is re-enabled
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            $ip = $_SERVER['REMOTE_ADDR'];
            $lockoutRemaining = $this->getLockoutRemaining($ip);
            if ($lockoutRemaining > 0) {
                $errors = ['login' => 'Too many failed login attempts. Please try again in:'];
                $this->render('auth/login', ['errors' => $errors, 'username' => $username, 'lockout_seconds' => $lockoutRemaining]);
                return;
            }

            $errors = [];
            if (empty($username)) {
                $errors['username'] = 'Username is required';
            }
            if (empty($password)) {
                $errors['password'] = 'Password is required';
            }

            if (empty($errors)) {
                $user = $this->userModel->findByUsername($username);

                $passwordValid = false;
                if ($user) {
                    if (password_verify($password, $user['password'])) {
                        $passwordValid = true;
                    } elseif (md5($password) === $user['password']) {
                        $passwordValid = true;
                        $this->userModel->update($user['id'], [
                            'password' => password_hash($password, PASSWORD_DEFAULT)
                        ]);
                    }
                }

                if ($user && $passwordValid) {
                    $status = $user['status'] ?? 'approved';
                    if ($status === 'pending') {
                        $errors['login'] = 'Your account is pending approval. Please wait for the Barangay Secretary to approve your account.';
                    } elseif ($status === 'rejected') {
                        $errors['login'] = 'Your account has been rejected. Please visit or contact the Barangay Secretary for assistance.';
                    } elseif ($status === 'deactivated') {
                        $errors['login'] = 'Your account has been deactivated. Please visit or contact the Barangay Secretary for assistance.';
                    } else {
                        $this->clearAttempts($ip);
                        Session::set('user_id', $user['id']);
                        Session::set('username', $user['username']);
                        Session::set('fullname', $user['fullname'] ?? '');
                        Session::set('role', $user['role'] ?? 'admin');
                        Session::set('logged_in', true);
                        Session::setFlash('toast_success', 'You have successfully logged in.');

                        if ($user['role'] === 'constituent') {
                            header('Location: index.php?controller=constituent&_=' . time());
                        } else {
                            // Check if admin is still using default password
                            if (password_verify('admin123', $user['password'])) {
                                Session::set('force_change_password', true);
                                header('Location: index.php?controller=users&action=edit&id=' . $user['id'] . '&_=' . time());
                            } else {
                                header('Location: index.php?controller=home&_=' . time());
                            }
                        }
                        exit;
                    }
                } else if (!$user || !$passwordValid) {
                    $this->recordFailedAttempt($ip, $username);
                    $errors['login'] = 'Invalid login credentials';
                }
            }

            $this->render('auth/login', ['errors' => $errors, 'username' => $username]);
        } else {
            if (Session::isLoggedIn()) {
                if (($_SESSION['role'] ?? '') === 'constituent') {
                    header('Location: index.php?controller=constituent&_=' . time());
                } else {
                    header('Location: index.php?controller=home&_=' . time());
                }
                exit;
            }
            $this->render('auth/login');
        }
    }

    // public function register()
    // {
    //     header('Location: index.php?controller=auth&action=login');
    //     exit; 
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $this->verifyCsrf();
    //         $username        = trim($_POST['username']);
    //         $fullname        = trim($_POST['fullname'] ?? '');
    //         // $email           = trim($_POST['email'] ?? ''); // [EMAIL DISABLED] - uncomment when email verification is re-enabled
    //         $password        = $_POST['password'];
    //         $confirmPassword = $_POST['confirm_password'];

    //         // Honeypot check
    //         if (!empty($_POST['website'] ?? '')) {
    //             $this->render('auth/register', [
    //                 'success' => 'Registration successful! Your account is pending approval from the Barangay Secretary.'
    //             ]);
    //             return;
    //         }

    //         $errors = [];

    //         if (empty($fullname)) {
    //             $errors['fullname'] = 'Full name is required';
    //         } elseif (strlen($fullname) < 2) {
    //             $errors['fullname'] = 'Full name must be at least 2 characters';
    //         }

    //         // [EMAIL DISABLED] - uncomment this block when email verification is re-enabled
    //         // if (empty($email)) {
    //         //     $errors['email'] = 'Email address is required';
    //         // } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //         //     $errors['email'] = 'Please enter a valid email address';
    //         // } elseif ($this->userModel->findByEmail($email)) {
    //         //     $errors['email'] = 'This email is already registered';
    //         // }

    //         if (empty($username)) {
    //             $errors['username'] = 'Username is required';
    //         } elseif ($this->userModel->findByUsername($username)) {
    //             $errors['username'] = 'Username is already taken';
    //         }

    //         if (empty($password)) {
    //             $errors['password'] = 'Password is required';
    //         } elseif (strlen($password) < 8) {
    //             $errors['password'] = 'Password must be at least 8 characters';
    //         }

    //         if ($password !== $confirmPassword) {
    //             $errors['confirm_password'] = 'Passwords do not match';
    //         }

    //         if (!empty($errors)) {
    //             $this->render('auth/register', [
    //                 'errors'   => $errors,
    //                 'username' => $username,
    //                 'fullname' => $fullname,
    //                 // 'email'    => $email, // [EMAIL DISABLED]
    //             ]);
    //             return;
    //         }

    //         // [OTP DISABLED] - uncomment this block when email verification is re-enabled
    //         // // Rate limit: max 3 OTP requests per email per hour
    //         // if ($this->otpModel->countRecentOtps($email) >= 3) {
    //         //     $errors['register'] = 'Too many attempts. Please wait before trying again.';
    //         //     $this->render('auth/register', [
    //         //         'errors'   => $errors,
    //         //         'username' => $username,
    //         //         'fullname' => $fullname,
    //         //         'email'    => $email,
    //         //     ]);
    //         //     return;
    //         // }

    //         // [OTP DISABLED] - uncomment these lines when email verification is re-enabled
    //         // $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    //         // $this->otpModel->create($email, $otp);

    //         // [OTP DISABLED] - uncomment this block when email verification is re-enabled
    //         // // Store registration data in session temporarily
    //         // $_SESSION['pending_registration'] = [
    //         //     'fullname' => $fullname,
    //         //     'email'    => $email,
    //         //     'username' => $username,
    //         //     'password' => password_hash($password, PASSWORD_DEFAULT),
    //         // ];

    //         // [OTP DISABLED] - uncomment these lines when email verification is re-enabled
    //         // $sent = Mailer::sendOtp($email, $fullname, $otp);
    //         // if (!$sent) {
    //         //     $errors['register'] = 'Failed to send OTP email. Please try again.';
    //         //     $this->render('auth/register', [
    //         //         'errors'   => $errors,
    //         //         'username' => $username,
    //         //         'fullname' => $fullname,
    //         //         'email'    => $email,
    //         //     ]);
    //         //     return;
    //         // }
    //         // header('Location: index.php?controller=auth&action=verifyOtp');
    //         // exit;

    //         // [DIRECT REGISTRATION - no OTP] create account directly as pending
    //         $created = $this->userModel->create(
    //             $username,
    //             password_hash($password, PASSWORD_DEFAULT),
    //             'constituent',
    //             'pending',
    //             $fullname,
    //             '' // email left empty since email field is disabled
    //         );

    //         if ($created) {
    //             $this->render('auth/register', [
    //                 'success' => 'Registration successful! Your account is pending approval from the Barangay Secretary. You will be able to log in once approved.'
    //             ]);
    //         } else {
    //             $errors['register'] = 'Account creation failed. Please try registering again.';
    //             $this->render('auth/register', [
    //                 'errors'   => $errors,
    //                 'username' => $username,
    //                 'fullname' => $fullname,
    //             ]);
    //         }

    //     } else {
    //         if (Session::isLoggedIn()) {
    //             header('Location: index.php?controller=home&_=' . time());
    //             exit;
    //         }
    //         $this->render('auth/register');
    //     }
    // }

    // [OTP DISABLED] - uncomment this entire method when email verification is re-enabled
    // public function verifyOtp() { ... }

    // [FORGOT PASSWORD DISABLED] - uncomment this method when forgot password is re-enabled
    // public function forgotPassword() { ... }

    // [FORGOT PASSWORD DISABLED] - uncomment this entire method when forgot password is re-enabled
    // public function sendResetLink() { ... }

    // [FORGOT PASSWORD DISABLED] - uncomment this method when forgot password is re-enabled
    // public function resetPassword() { ... }

    // [FORGOT PASSWORD DISABLED] - uncomment this method when forgot password is re-enabled
    // public function processReset() { ... }

    private function isRateLimited(string $ip): bool
    {
        return $this->getLockoutRemaining($ip) > 0;
    }

    private function getLockoutRemaining(string $ip): int
    {
        $db   = (new Database())->connect();
        $stmt = $db->prepare(
            'SELECT COUNT(*) as cnt, TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(MAX(attempted_at), INTERVAL 15 MINUTE)) as remaining 
             FROM login_attempts 
             WHERE ip_address = :ip AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)'
        );
        $stmt->execute([':ip' => $ip]);
        $row = $stmt->fetch();
        if ($row['cnt'] >= 5 && $row['remaining'] > 0) {
            return (int)$row['remaining'];
        }
        return 0;
    }

    private function recordFailedAttempt(string $ip, string $username): void
    {
        $db   = (new Database())->connect();
        $stmt = $db->prepare(
            'INSERT INTO login_attempts (ip_address, username) VALUES (:ip, :username)'
        );
        $stmt->execute([':ip' => $ip, ':username' => $username]);
    }

    private function clearAttempts(string $ip): void
    {
        $db   = (new Database())->connect();
        $stmt = $db->prepare('DELETE FROM login_attempts WHERE ip_address = :ip');
        $stmt->execute([':ip' => $ip]);
    }

    public function logout()
    {
        Session::destroy();
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Location: index.php?controller=auth&action=login&logged_out=1&_=' . time());
        exit;
    }

    /**
     * Show admin recovery page
     */
    public function adminRecovery()
    {
        // Already logged in? Redirect away
        if (Session::isLoggedIn()) {
            header('Location: index.php?controller=home');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $step = $_POST['step'] ?? '1';

            // ── Step 1: Verify secret key ──
            if ($step === '1') {
                $recoveryKey = $_POST['recovery_key'] ?? '';

                if (!hash_equals(ADMIN_RECOVERY_KEY, $recoveryKey)) {
                    $this->render('auth/admin_recovery', [
                        'error' => 'Invalid recovery key. Please try again.',
                    ]);
                    return;
                }

                // Key is correct — store in session and go to step 2
                $_SESSION['recovery_verified_key'] = $recoveryKey;

                $this->render('auth/admin_recovery', [
                    'step2' => true,
                ]);
                return;
            }

            // ── Step 2: Reset the password ──
            if ($step === '2') {
                // Verify key again from session
                $sessionKey = $_SESSION['recovery_verified_key'] ?? '';
                if (!hash_equals(ADMIN_RECOVERY_KEY, $sessionKey)) {
                    unset($_SESSION['recovery_verified_key']);
                    $this->render('auth/admin_recovery', [
                        'error' => 'Recovery session expired. Please start again.',
                    ]);
                    return;
                }

                $newPassword     = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                if (strlen($newPassword) < 8) {
                    $this->render('auth/admin_recovery', [
                        'step2' => true,
                        'error' => 'Password must be at least 8 characters.',
                    ]);
                    return;
                }

                if ($newPassword !== $confirmPassword) {
                    $this->render('auth/admin_recovery', [
                        'step2' => true,
                        'error' => 'Passwords do not match.',
                    ]);
                    return;
                }

                // Get the admin user (first admin account)
                $admin = $this->userModel->getFirstAdmin();

                if (!$admin) {
                    $this->render('auth/admin_recovery', [
                        'error' => 'No admin account found.',
                    ]);
                    return;
                }

                // Update the password
                $this->userModel->update($admin['id'], [
                    'password' => password_hash($newPassword, PASSWORD_DEFAULT)
                ]);

                // Clear recovery session
                unset($_SESSION['recovery_verified_key']);

                $this->render('auth/admin_recovery', [
                    'success' => 'Password reset successfully! You can now log in with your new password.',
                ]);
                return;
            }
        }

        // GET request — show step 1
        unset($_SESSION['recovery_verified_key']);
        $this->render('auth/admin_recovery');
    }

}