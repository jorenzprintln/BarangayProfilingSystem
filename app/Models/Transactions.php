<?php

class Transactions
{
    private $db;
    private $table = 'transactions';

    public function __construct()
    {
        $this->db = new Database();
    }
    
    public function create($data)
    {
        $connection = $this->db->connect();
        
        $sql = "INSERT INTO $this->table (transaction, requested_by, generated_by, document_location, date_of_transaction, purpose) 
                VALUES (:transaction, :requested_by, :generated_by, :document_location, :date_of_transaction, :purpose)";
        
        try {
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':transaction', $data['transaction']);
            $stmt->bindParam(':requested_by', $data['requested_by']);
            $stmt->bindParam(':generated_by', $data['generated_by']);
            $stmt->bindParam(':document_location', $data['document_location']);
            $stmt->bindParam(':date_of_transaction', $data['date_of_transaction']);
            $stmt->bindParam(':purpose', $data['purpose']);
            
            $stmt->execute();
            return $connection->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAllTransactions()
    {
        $connection = $this->db->connect();
        $sql = "SELECT * FROM $this->table";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
