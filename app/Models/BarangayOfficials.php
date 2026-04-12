<?php

class BarangayOfficials
{
    private $db;
    private $table = 'officials';

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllOfficials()
    {
        $query = "SELECT bo.id, 
                TRIM(CONCAT_WS(' ', 
                    c.first_name, 
                    NULLIF(TRIM(c.middle_name), ''), 
                    c.last_name,
                    NULLIF(TRIM(c.suffix), '')
                )) AS full_name, 
                bo.role
        FROM officials bo
        JOIN constituents c ON bo.constituent_id = c.id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    public function getOfficialById($id)
    {
        $query = "SELECT * FROM officials WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }


    public function addOfficials($officials)
    {
        $query = "INSERT INTO officials (constituent_id, role) 
                  VALUES (:constituent_id, :role)";
        $stmt = $this->db->connect()->prepare($query);

        foreach ($officials as $official) {
            $stmt->bindParam(':constituent_id', $official['id'], PDO::PARAM_INT);
            $stmt->bindParam(':role', $official['role'], PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    public function updateOfficial($id, $constituent_id, $role)
    {
        $query = "UPDATE officials 
                  SET constituent_id = :constituent_id, role = :role 
                  WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':constituent_id', $constituent_id, PDO::PARAM_INT);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deleteOfficial($id)
    {
        $query = "DELETE FROM officials WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }


    // Updates here below

    public function getConstituentsNotInOfficials()
    {
        $query = "SELECT id,
                TRIM(CONCAT_WS(' ',
                    first_name,
                    NULLIF(TRIM(middle_name), ''),
                    last_name,
                    NULLIF(TRIM(suffix), '')
                )) AS full_name,
                first_name, middle_name, last_name, suffix
                FROM constituents    
                WHERE id NOT IN (SELECT constituent_id FROM officials) 
                AND removed_at IS NULL
                ORDER BY last_name, first_name";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get official by role
     * @param mixed $role Can be either role ID (int) or role name (string like 'SECRETARY', 'PUNONG BARANGAY')
     * @return array|false Returns array with 'full_name' key or false if not found
     */
    public function getOfficialByRole($role)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            SELECT TRIM(CONCAT_WS(' ',
                c.first_name,
                NULLIF(TRIM(c.middle_name), ''),
                c.last_name,
                NULLIF(TRIM(c.suffix), '')
            )) AS full_name 
            FROM officials bo 
            INNER JOIN constituents c ON bo.constituent_id = c.id 
            WHERE bo.role = :role 
            LIMIT 1
        ");
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function hasRoleAssigned($role)
    {
        $query = "SELECT COUNT(*) as count FROM officials WHERE role = :role";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}