<?php

class Classifications
{
    private $db;
    private $table = 'classifications';

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAllClassifications()
    {
        $stmt = $this->db->connect()->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $connection = $this->db->connect();
        $connection->beginTransaction();

        try {
            $stmt = $connection->prepare("INSERT INTO {$this->table} (code, name, organization) VALUES (:code, :name, :organization)");

            $stmt->bindParam(':code', $data['code']);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':organization', $data['organization']);

            $stmt->execute();
            $connection->commit();
            return true; 
        } catch (PDOException $e) {
            $connection->rollBack();
            throw $e;
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public function getConstituentsByClassification($classification)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            SELECT CONCAT(c.first_name, ' ', IFNULL(c.middle_name, ''), ' ', c.last_name, ' ', IFNULL(c.suffix, '')) AS full_name 
            FROM constituents c 
            INNER JOIN constituents_classifications cc ON c.id = cc.constituent_id
            INNER JOIN classifications cl ON cc.classification_id = cl.id 
            WHERE cl.code = :classification
        ");
        $stmt->bindParam(':classification', $classification, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
