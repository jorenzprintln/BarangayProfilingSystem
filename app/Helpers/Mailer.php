<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    public static function sendOtp(string $toEmail, string $toName, string $otp): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP - Barangay 36-A Registration';
            $mail->Body    = self::otpEmailTemplate($toName, $otp);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Mailer error: ' . $e->getMessage());
            return false;
        }
    }

    private static function otpEmailTemplate(string $name, string $otp): string
    {
        return "
        <div style='font-family:Arial,sans-serif;max-width:480px;margin:auto;padding:30px;border:1px solid #e2e8f0;border-radius:12px;'>
            <h2 style='color:#1d4ed8;text-align:center;'>Barangay 36-A</h2>
            <p>Hi <strong>" . htmlspecialchars($name) . "</strong>,</p>
            <p>Use the OTP below to verify your email. It expires in <strong>10 minutes</strong>.</p>
            <div style='text-align:center;margin:28px 0;'>
                <span style='font-size:2.2rem;font-weight:700;letter-spacing:10px;color:#1d4ed8;background:#eff6ff;padding:14px 28px;border-radius:10px;'>
                    {$otp}
                </span>
            </div>
            <p style='color:#64748b;font-size:0.85rem;'>If you did not request this, ignore this email.</p>
        </div>";
    }

    public static function sendPasswordReset(string $toEmail, string $toName, string $resetLink): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = 'Barangay 36-A - Password Reset';
            $mail->Body    = self::resetEmailTemplate($toName, $resetLink);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Password reset mail error: ' . $e->getMessage());
            return false;
        }
    }

    private static function resetEmailTemplate(string $name, string $resetLink): string
    {
        return "
        <div style='font-family:Arial,sans-serif;max-width:480px;margin:auto;padding:30px;
                    border:1px solid #e2e8f0;border-radius:12px;'>
            <h2 style='color:#1d4ed8;text-align:center;'>Barangay 36-A</h2>
            <p>Hi <strong>" . htmlspecialchars($name) . "</strong>,</p>
            <p>You requested a password reset. Click the button below to set a new password.</p>
            <p>This link will expire in <strong>1 hour</strong>.</p>
            <div style='text-align:center;margin:28px 0;'>
                <a href='{$resetLink}'
                style='background:#1d4ed8;color:#fff;padding:12px 28px;border-radius:8px;
                        text-decoration:none;font-weight:600;font-size:1rem;'>
                    Reset Password
                </a>
            </div>
            <p style='color:#64748b;font-size:0.85rem;'>
                If you did not request this, ignore this email.
            </p>
            <p style='color:#94a3b8;font-size:0.78rem;'>
                Or copy this link: <a href='{$resetLink}'>{$resetLink}</a>
            </p>
        </div>";
    }
}