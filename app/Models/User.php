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

    public function findByEmail($email)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function create($username, $email, $password)
    {
        $conn = $this->db->connect();
        $stmt = $conn->prepare("INSERT INTO {$this->table} (username, email, password) VALUES (:username, :email, :password)");

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);

        return $stmt->execute();
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
                  JOIN constituents c ON u.constituent_id = c.id
                  WHERE u.id = :user_id";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user) {
            // Format middle name as initial (if exists)
            $middleInitial = !empty($user['middle_name']) ? substr($user['middle_name'], 0, 1) . '.' : '';

            // Construct full name
            $user['full_name'] = $user['first_name'] . ' ' .
                ($middleInitial ? $middleInitial . ' ' : '') .
                $user['last_name'];
        }

        return $user;
    }
}
