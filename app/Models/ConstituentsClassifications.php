<?php

class ConstituentsClassifications
{
    private $db;
    private $table = 'constituents_classifications';

    public function __construct()
    {
        $this->db = new Database();
    }

    public function create($data)
    {
        $connection = $this->db->connect();
        
        // Begin transaction
        $connection->beginTransaction();
        
        try {
            $constituent_id = $data['constituent_id'];
            $classifications = $data['classifications'];
            $classification_org_ids = $data['classification_org_ids'] ?? [];
            
            // Insert each classification for the constituent
            foreach ($classifications as $classification_id) {
                $org_id_no = $classification_org_ids[$classification_id] ?? null;
                
                // Ensure empty strings are converted to NULL
                if ($org_id_no === '') {
                    $org_id_no = null;
                }
                    
                $query = "INSERT INTO {$this->table} (constituent_id, classification_id, org_id_no) 
                          VALUES (:constituent_id, :classification_id, :org_id_no)";
                          
                $stmt = $connection->prepare($query);
                $stmt->bindParam(':constituent_id', $constituent_id, PDO::PARAM_INT);
                $stmt->bindParam(':classification_id', $classification_id, PDO::PARAM_INT);
                $stmt->bindParam(':org_id_no', $org_id_no, $org_id_no === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                $stmt->execute();
            }
            
            // Commit transaction
            $connection->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction on error
            $connection->rollBack();
            throw $e;
        }
    }

    public function getConstituentClassifications($constituent_id)
    {
        $stmt = $this->db->connect()->prepare("SELECT * FROM {$this->table} WHERE constituent_id = :constituent_id");
        $stmt->bindParam(':constituent_id', $constituent_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteByConstituentId($constituent_id)
    {
        $connection = $this->db->connect();
        $connection->beginTransaction();
        
        try {
            $stmt = $connection->prepare("DELETE FROM {$this->table} WHERE constituent_id = :constituent_id");
            $stmt->bindParam(':constituent_id', $constituent_id);
            $stmt->execute();
            $connection->commit();
            return true;
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public function getTotalNumberofConstituentsWithSpecifiedClassifications()
    {
        $query = "
            SELECT 
                cl.code AS classification_code,
                c.sex,
                COUNT(DISTINCT c.id) AS total
            FROM 
                {$this->table} cc
            JOIN 
                constituents c ON cc.constituent_id = c.id
            JOIN 
                classifications cl ON cc.classification_id = cl.id
            WHERE 
                cl.code IN ('OSY', 'PWD', 'SC')
                AND c.removed_at IS NULL
            GROUP BY 
                cl.code, c.sex
            ORDER BY 
                cl.code, c.sex
        ";
        
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Restructure data for easier consumption
        $data = [
            'OSY' => ['MALE' => 0, 'FEMALE' => 0, 'total' => 0],
            'PWD' => ['MALE' => 0, 'FEMALE' => 0, 'total' => 0],
            'SC' => ['MALE' => 0, 'FEMALE' => 0, 'total' => 0]
        ];
        
        // Fill in the actual counts
        foreach ($results as $row) {
            $code = $row['classification_code'];
            $sex = $row['sex'];
            $count = (int)$row['total'];
            
            if (isset($data[$code])) {
                $data[$code][$sex] = $count;
                $data[$code]['total'] += $count;
            }
        }
        
        return $data;
    }

    public function getTotalSeniorCitizens()
    {
        $query = "
            SELECT 
                COUNT(DISTINCT c.id) AS total
            FROM 
                {$this->table} cc
            JOIN 
                constituents c ON cc.constituent_id = c.id
            WHERE 
                cc.classification_id = 2
                AND c.removed_at IS NULL
        ";
        
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int)$result['total'];
    }
}
