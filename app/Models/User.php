<?php

class User
{
    private $db;
    private $table = 'users';

    public function __construct()
    {
        $this->db = new Database();
    }

    public function findByUsername($username)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare('SELECT * FROM ' . $this->table . ' WHERE username = :username');
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function create($username, $password, $role = 'admin', $status = 'approved', $fullname = '', $email = null)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("INSERT INTO {$this->table} (username, password, role, status, fullname, email) VALUES (:username, :password, :role, :status, :fullname, :email)");

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email);

        return $stmt->execute();
    }

    /**
     * Create a constituent user account linked to a constituent record.
     * Called automatically by ConstituentsController when the secretary adds a new constituent.
     */
    public function createWithConstituentId(
        string $username,
        string $password,
        string $role,
        string $status,
        string $fullname,
        ?string $email,
        int $constituentId
    ): bool {
        $conn = $this->db->connect();
        $stmt = $conn->prepare(
            "INSERT INTO {$this->table}
             (username, password, role, status, fullname, email, constituent_id)
             VALUES (:username, :password, :role, :status, :fullname, :email, :constituent_id)"
        );
        $stmt->bindParam(':username',       $username);
        $stmt->bindParam(':password',       $password);
        $stmt->bindParam(':role',           $role);
        $stmt->bindParam(':status',         $status);
        $stmt->bindParam(':fullname',       $fullname);
        $stmt->bindParam(':email',          $email);
        $stmt->bindParam(':constituent_id', $constituentId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function findByEmail($email)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("SELECT * FROM {$this->table} WHERE email = :email AND deleted_at IS NULL");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function createPasswordResetToken($email, $token)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (:email, :token)");
        return $stmt->execute([':email' => $email, ':token' => $token]);
    }

    public function findByPasswordResetToken($token)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = :token AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function deletePasswordResetToken($email)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = :email");
        return $stmt->execute([':email' => $email]);
    }

    public function getAll()
    {
        $conn = $this->db->connect();
        $stmt = $conn->query("SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function getAdminUsers()
    {
        $conn = $this->db->connect();
        $stmt = $conn->query("SELECT * FROM {$this->table} WHERE role = 'admin' AND deleted_at IS NULL ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function update($id, $data)
    {
        $conn = $this->db->connect();
        $fields = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function softDelete($id)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // [PENDING APPROVALS DISABLED] - Self-registration is disabled. Accounts are now
    // auto-created by the secretary when adding a constituent. This method will always
    // return an empty array since no accounts will ever have status='pending'.
    // Uncomment the original query if self-registration is re-enabled.
    public function getPendingUsers()
    {
        return []; // No pending users — self-registration is disabled

        // Uncomment below if self-registration is re-enabled:
        // $conn = $this->db->connect();
        // $stmt = $conn->query("SELECT * FROM {$this->table} WHERE status = 'pending' AND deleted_at IS NULL ORDER BY id DESC");
        // return $stmt->fetchAll();
    }

    public function getConstituentAccounts($search = '', $limit = 10, $offset = 0)
    {
        $conn = $this->db->connect();
        $sql = "SELECT * FROM {$this->table} WHERE role = 'constituent' AND status IN ('approved','deactivated') AND deleted_at IS NULL";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (username LIKE :search OR fullname LIKE :search2)";
            $params[':search'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }
        $sql .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";

        $stmt = $conn->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countConstituentAccounts($search = '')
    {
        $conn = $this->db->connect();
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE role = 'constituent' AND status IN ('approved','deactivated') AND deleted_at IS NULL";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (username LIKE :search OR fullname LIKE :search2)";
            $params[':search'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getRejectedAccounts($search = '', $limit = 10, $offset = 0)
    {
        $conn = $this->db->connect();
        $sql = "SELECT * FROM {$this->table} WHERE role = 'constituent' AND status = 'rejected' AND deleted_at IS NULL";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (username LIKE :search OR fullname LIKE :search2)";
            $params[':search'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }
        $sql .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";

        $stmt = $conn->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countRejectedAccounts($search = '')
    {
        $conn = $this->db->connect();
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE role = 'constituent' AND status = 'rejected' AND deleted_at IS NULL";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (username LIKE :search OR fullname LIKE :search2)";
            $params[':search'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function hardDelete($id)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("DELETE FROM {$this->table} WHERE id = :id AND role = 'constituent' AND status = 'rejected'");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function toggleStatus($id)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("UPDATE {$this->table} SET status = CASE WHEN status = 'approved' THEN 'deactivated' WHEN status = 'deactivated' THEN 'approved' ELSE status END WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function findByFullname($fullname)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("SELECT * FROM {$this->table} WHERE fullname = :fullname AND deleted_at IS NULL");
        $stmt->bindParam(':fullname', $fullname);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getCurrentUser()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $user_id = $_SESSION['user_id'];
        $conn = $this->db->connect();

        $query = "SELECT u.*, 
                  c.first_name, 
                  c.middle_name, 
                  c.last_name 
                  FROM {$this->table} u
                  LEFT JOIN constituents c ON u.constituent_id = c.id
                  WHERE u.id = :user_id";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user) {
            $middleInitial = !empty($user['middle_name']) ? substr($user['middle_name'], 0, 1) . '.' : '';
            $user['full_name'] = $user['first_name'] . ' ' .
                ($middleInitial ? $middleInitial . ' ' : '') .
                $user['last_name'];
        }

        return $user;
    }

    /**
     * Get the first admin account
     */
    public function getFirstAdmin(): ?array
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare(
            "SELECT * FROM users
             WHERE role = 'admin'
             AND deleted_at IS NULL
             ORDER BY id ASC
             LIMIT 1"
        );
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}