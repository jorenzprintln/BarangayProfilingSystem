<?php

class EmailOtp
{
    private $db;
    private $table = 'email_otps';

    public function __construct()
    {
        $this->db = new Database();
    }

    public function create(string $email, string $otp): bool
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare(
            "INSERT INTO {$this->table} (email, otp, expires_at)
             VALUES (:email, :otp, DATE_ADD(NOW(), INTERVAL 10 MINUTE))"
        );
        return $stmt->execute([':email' => $email, ':otp' => $otp]);
    }

    public function getValid(string $email, string $otp): ?array
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare(
            "SELECT * FROM {$this->table}
             WHERE email = :email AND otp = :otp
             AND used = 0 AND expires_at > NOW()
             ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([':email' => $email, ':otp' => $otp]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function markUsed(int $id): bool
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare(
            "UPDATE {$this->table} SET used = 1 WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    public function countRecentOtps(string $email): int
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare(
            "SELECT COUNT(*) FROM {$this->table}
             WHERE email = :email
             AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)"
        );
        $stmt->execute([':email' => $email]);
        return (int)$stmt->fetchColumn();
    }
}