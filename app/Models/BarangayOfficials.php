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
                         CONCAT(c.first_name, ' ', c.middle_name, ' ', c.last_name) AS full_name, 
                         bo.role
                  FROM officials bo
                  JOIN constituents c ON bo.constituent_id = c.id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll() ?: []; // Return an empty array if no records are found
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
        $query = "SELECT * FROM constituents WHERE id NOT IN (SELECT constituent_id FROM officials)";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getOfficialByRole($role)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("SELECT CONCAT(c.first_name, ' ', c.middle_name, ' ', c.last_name, ' ', IFNULL(c.suffix, '')) AS full_name FROM officials bo INNER JOIN constituents c ON bo.constituent_id = c.id WHERE bo.role = :role LIMIT 1");
        $stmt->bindParam(':role', $role, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
