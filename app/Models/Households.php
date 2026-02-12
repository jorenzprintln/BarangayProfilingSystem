<?php

class Households
{
    private $db;
    private $table = 'households';

    public function __construct()
    {
        $this->db = new Database();
    }

    public function create($data)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("INSERT INTO {$this->table} (household_number, region, province, city_municipality, barangay_code, barangay_name, street_name, zip_code, purok, block_number, lot_number, house_building_number, unit_number) VALUES (:household_number, :region, :province, :city_municipality, :barangay_code, :barangay_name, :street_name, :zip_code, :purok, :block_number, :lot_number, :house_building_number, :unit_number)");
        $stmt->bindParam(':household_number', $data['household_number'], PDO::PARAM_STR);
        $stmt->bindParam(':region', $data['region'], PDO::PARAM_STR);
        $stmt->bindParam(':province', $data['province'], PDO::PARAM_STR);
        $stmt->bindParam(':city_municipality', $data['city_municipality'], PDO::PARAM_STR);
        $stmt->bindParam(':barangay_code', $data['barangay_code'], PDO::PARAM_STR);
        $stmt->bindParam(':barangay_name', $data['barangay_name'], PDO::PARAM_STR);
        $stmt->bindParam(':street_name', $data['street_name'], PDO::PARAM_STR);
        $stmt->bindParam(':zip_code', $data['zip_code'], PDO::PARAM_STR);
        $stmt->bindParam(':purok', $data['purok'], PDO::PARAM_STR);
        $stmt->bindParam(':block_number', $data['block_number'], PDO::PARAM_STR);
        $stmt->bindParam(':lot_number', $data['lot_number'], PDO::PARAM_STR);
        $stmt->bindParam(':house_building_number', $data['house_building_number'], PDO::PARAM_STR);
        $stmt->bindParam(':unit_number', $data['unit_number'], PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLastInsertedId()
    {
        $connection = $this->db->connect();
        return $connection->lastInsertId();
    }

    public function checkHouseholdNumberExist($household_number)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("SELECT COUNT(*) FROM {$this->table} WHERE household_number = :household_number");
        $stmt->bindParam(':household_number', $household_number, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function getHousehold($household_id)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("SELECT * FROM {$this->table} WHERE id = :household_id");
        $stmt->bindParam(':household_id', $household_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllHouseholds()
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            SELECT 
                h.id,
                h.household_number,
                CONCAT(c.first_name, ' ', c.middle_name, ' ', c.last_name, ' ', IFNULL(c.suffix, '')) AS full_name,
                (SELECT COUNT(DISTINCT cfh2.family_id) 
                 FROM constituents_families_households cfh2 
                 WHERE cfh2.household_id = h.id) AS total_families,
                (SELECT COUNT(*) 
                 FROM constituents_families_households cfh3 
                 WHERE cfh3.household_id = h.id) AS total_members
            FROM {$this->table} h
            LEFT JOIN constituents_families_households cfh ON h.id = cfh.household_id AND cfh.is_head = 'YES'
            LEFT JOIN constituents c ON cfh.constituent_id = c.id
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHouseholdMembersWithDetails($household_id)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            SELECT 
                cfh.id,
                cfh.constituent_id,
                CONCAT(c.first_name, ' ', c.middle_name, ' ', c.last_name) AS full_name,
                cfh.role,
                cfh.is_head,
                TIMESTAMPDIFF(YEAR, c.birthdate, CURDATE()) AS age,
                c.contact AS contact,
                c.sex,
                c.civil_status
            FROM constituents_families_households cfh
            INNER JOIN constituents c ON cfh.constituent_id = c.id 
            WHERE cfh.household_id = :household_id
        ");
        $stmt->bindParam(':household_id', $household_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHouseholdMembersInformation($household_id)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            SELECT 
                c.last_name,
                c.first_name, 
                c.middle_name,
                c.suffix,
                c.birthplace AS place_of_birth,
                c.birthdate AS date_of_birth,
                TIMESTAMPDIFF(YEAR, c.birthdate, CURDATE()) AS age,
                c.sex,
                c.civil_status,
                CASE c.citizenship 
                    WHEN 'FILIPINO' THEN 'FILIPINO'
                    ELSE 'OTHERS'
                END AS citizenship,
                c.occupation,
                GROUP_CONCAT(cl.code SEPARATOR ', ') AS classification
            FROM constituents_families_households ch
            INNER JOIN constituents c ON ch.constituent_id = c.id
            LEFT JOIN constituents_classifications cc ON c.id = cc.constituent_id
            LEFT JOIN classifications cl ON cc.classification_id = cl.id
            WHERE ch.household_id = :household_id
            GROUP BY c.id
        ");
        $stmt->bindParam(':household_id', $household_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getConstituentsNotInHousehold()
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("SELECT * FROM constituents WHERE id NOT IN (SELECT constituent_id FROM constituents_families_households)");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkHouseholdHeadExists($household_id)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("SELECT COUNT(*) FROM constituents_families_households WHERE household_id = :household_id AND is_head = 'YES'");
        $stmt->bindParam(':household_id', $household_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function addConstituentsToHousehold($household_id, $constituent_id, $role = null, $is_head = null)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("INSERT INTO constituents_families_households (household_id, constituent_id, role, is_head) VALUES (:household_id, :constituent_id, :role, :is_head)");
        $stmt->bindParam(':household_id', $household_id, PDO::PARAM_INT);
        $stmt->bindParam(':constituent_id', $constituent_id, PDO::PARAM_INT);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->bindParam(':is_head', $is_head, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function getHouseholdHead($household_id)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("SELECT CONCAT(c.first_name, ' ', c.middle_name, ' ', c.last_name, ' ', IFNULL(c.suffix, '')) AS full_name FROM constituents_families_households ch INNER JOIN constituents c ON ch.constituent_id = c.id WHERE ch.household_id = :household_id AND ch.is_head = 'YES' LIMIT 1");
        $stmt->bindParam(':household_id', $household_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getConstituentHousehold($constituent_id)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("SELECT c.*, h.* FROM {$this->table} c LEFT JOIN constituents_families_households ch ON c.id = ch.constituent_id LEFT JOIN households h ON ch.household_id = h.id WHERE c.id = :constituent_id");
        $stmt->bindParam(':constituent_id', $constituent_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getConstituentHouseholdInfo($constituent_id)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            SELECT h.*, 
                   CONCAT(hc.first_name, ' ', hc.middle_name, ' ', hc.last_name, ' ', IFNULL(hc.suffix, '')) AS head_of_household,
                   CONCAT(h.street_name, ', ', h.barangay_name, ', ', h.city_municipality, ', ', h.province) AS address,
                   h.house_building_number AS house_number,
                   h.unit_number AS unit
            FROM constituents c
            LEFT JOIN constituents_families_households cfh ON c.id = cfh.constituent_id
            LEFT JOIN households h ON cfh.household_id = h.id
            LEFT JOIN constituents_families_households hfh ON h.id = hfh.household_id AND hfh.is_head = 'YES'
            LEFT JOIN constituents hc ON hfh.constituent_id = hc.id
            WHERE c.id = :constituent_id
        ");
        $stmt->bindParam(':constituent_id', $constituent_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Return an empty array if no result was found
        return $result ?: [];
    }

    public function getHouseholdsWithInformation()
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            SELECT 
                h.id,
                h.household_number,
                CONCAT(c.first_name, ' ', c.middle_name, ' ', c.last_name, ' ', IFNULL(c.suffix, '')) AS head_of_household,
                (SELECT COUNT(DISTINCT family_id) FROM constituents_families_households WHERE household_id = h.id) AS number_of_families,
                (SELECT COUNT(*) FROM constituents_families_households ch WHERE ch.household_id = h.id) AS number_of_members
            FROM households h
            LEFT JOIN constituents_families_households ch ON h.id = ch.household_id AND ch.is_head = 'YES'
            LEFT JOIN constituents c ON ch.constituent_id = c.id
            WHERE EXISTS (
                SELECT 1 FROM constituents_families_households cfh 
                WHERE cfh.household_id = h.id
            )
            ORDER BY h.household_number
        ");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getTotalHouseholdsCount()
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("SELECT COUNT(*) FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function delete($householdId)
    {
        try {
            $connection = $this->db->connect();
            // Start a transaction
            $connection->beginTransaction();
            
            error_log("Starting transaction for household deletion ID: $householdId");
            
            // Delete any relations in constituents_families_households if they exist
            $stmt = $connection->prepare("DELETE FROM constituents_families_households WHERE household_id = :household_id");
            $stmt->bindParam(':household_id', $householdId, PDO::PARAM_INT);
            $stmt->execute();
            error_log("Removed related constituents_families_households records: " . $stmt->rowCount());
            
            // Delete the household
            $stmt = $connection->prepare("DELETE FROM {$this->table} WHERE id = :household_id");
            $stmt->bindParam(':household_id', $householdId, PDO::PARAM_INT);
            $result = $stmt->execute();
            error_log("Household delete result: " . ($result ? "success" : "failed") . ", Rows affected: " . $stmt->rowCount());
            
            // Commit the transaction
            $connection->commit();
            error_log("Transaction committed for household deletion");
            
            return $result;
        } catch (Exception $e) {
            // Roll back the transaction if something failed
            if (isset($connection)) {
                $connection->rollBack();
                error_log("Transaction rolled back due to error: " . $e->getMessage());
            }
            error_log("Exception in household delete: " . $e->getMessage());
            throw $e;
        }
    }
}
