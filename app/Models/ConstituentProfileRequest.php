<?php

class ConstituentProfileRequest
{
    private $db;
    private $table = 'constituent_profile_requests';

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getLatestByUser(int $userId)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY id DESC LIMIT 1");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getHistoryByUser(int $userId, int $limit = 10)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare(
            "SELECT *
             FROM {$this->table}
             WHERE user_id = :user_id
             ORDER BY id DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findPendingByUser(int $userId)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id AND status = 'pending' ORDER BY id DESC LIMIT 1");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $conn = $this->db->connect();
        $now  = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s');
        $stmt = $conn->prepare(
            "INSERT INTO {$this->table} (user_id, constituent_id, payload_json, status, created_at, updated_at)
            VALUES (:user_id, :constituent_id, :payload_json, 'pending', :now, :now2)"
        );
        return $stmt->execute([
            ':user_id'        => $data['user_id'],
            ':constituent_id' => $data['constituent_id'],
            ':payload_json'   => $data['payload_json'],
            ':now'            => $now,
            ':now2'           => $now,
        ]);
    }

    public function updatePending(int $id, ?int $constituentId, string $payloadJson)
    {
        $conn = $this->db->connect();
        $now  = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s');
        $stmt = $conn->prepare(
            "UPDATE {$this->table}
            SET constituent_id = :constituent_id,
                payload_json = :payload_json,
                status = 'pending',
                admin_notes = NULL,
                reviewed_by = NULL,
                reviewed_at = NULL,
                updated_at = :now
            WHERE id = :id"
        );

        return $stmt->execute([
            ':id'             => $id,
            ':constituent_id' => $constituentId,
            ':payload_json'   => $payloadJson,
            ':now'            => $now,
        ]);
    }

    public function getPendingRequests()
    {
        $conn = $this->db->connect();
        $sql = "SELECT r.*, u.username, u.fullname, u.constituent_id AS user_constituent_id
                FROM {$this->table} r
                INNER JOIN users u ON u.id = r.user_id
                WHERE r.status = 'pending' AND u.deleted_at IS NULL
                ORDER BY r.created_at DESC";
        $stmt = $conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPendingRequests(): int
    {
        $conn = $this->db->connect();
        $stmt = $conn->query("SELECT COUNT(*) FROM {$this->table} WHERE status = 'pending'");
        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id)
    {
        $conn = $this->db->connect();
        $sql = "SELECT r.*, u.username, u.fullname, u.constituent_id AS user_constituent_id
                FROM {$this->table} r
                INNER JOIN users u ON u.id = r.user_id
                WHERE r.id = :id
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function markApproved(int $id, int $adminId)
    {
        $conn = $this->db->connect();
        $now  = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s');
        $stmt = $conn->prepare(
            "UPDATE {$this->table}
            SET status = 'approved',
                reviewed_by = :reviewed_by,
                reviewed_at = :now,
                updated_at = :now2
            WHERE id = :id"
        );
        return $stmt->execute([
            ':id'          => $id,
            ':reviewed_by' => $adminId,
            ':now'         => $now,
            ':now2'        => $now,
        ]);
    }

    public function markRejected(int $id, int $adminId, string $reason = '')
    {
        $conn = $this->db->connect();
        $now  = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s');
        $stmt = $conn->prepare(
            "UPDATE {$this->table}
            SET status = 'rejected',
                admin_notes = :admin_notes,
                reviewed_by = :reviewed_by,
                reviewed_at = :now,
                updated_at = :now2
            WHERE id = :id"
        );
        return $stmt->execute([
            ':id'          => $id,
            ':reviewed_by' => $adminId,
            ':admin_notes' => $reason,
            ':now'         => $now,
            ':now2'        => $now,
        ]);
    }
}
