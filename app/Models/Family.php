<?php

class Family
{
    private $db;
    private $table = 'families';

    public function __construct()
    {
        $this->db = new Database();
    }

 
    public function create($data)
    {
        $connection = $this->db->connect();
        $connection->beginTransaction();

        try {
            $stmt = $connection->prepare("INSERT INTO {$this->table} (household_id, family_name, head_constituent_id, date_resided) VALUES (:household_id, :family_name, :head_constituent_id, :date_resided)");
            $stmt->bindParam(':household_id', $data['household_id'], PDO::PARAM_INT);
            $stmt->bindParam(':family_name', $data['family_name'], PDO::PARAM_STR);
            $stmt->bindParam(':head_constituent_id', $data['head_constituent_id'], PDO::PARAM_INT);
            $stmt->bindParam(':date_resided', $data['date_resided'], PDO::PARAM_STR);
            $stmt->execute();
            
            $familyId = $connection->lastInsertId();
            $connection->commit();
            return $familyId;
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public function getFamiliesByHouseholdId($household_id)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("SELECT * FROM {$this->table} WHERE household_id = :household_id");
        $stmt->bindParam(':household_id', $household_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateConstituentsFamilyIdInHousehold($constituentId, $familyId)
    {
        $connection = $this->db->connect();
        $connection->beginTransaction();

        try {
            $stmt = $connection->prepare("UPDATE constituents_families_households SET family_id = :family_id WHERE constituent_id = :constituent_id");
            $stmt->bindParam(':family_id', $familyId, PDO::PARAM_INT);
            $stmt->bindParam(':constituent_id', $constituentId, PDO::PARAM_INT);
            $stmt->execute();
            
            $connection->commit();
            return true;
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    // Update a family in a specified household
    public function updateFamily($familyId, $familyName = null, $headConstituentId = null)
    {
        $fields = [];
        if ($familyName !== null) {
            $fields[] = "family_name = :family_name";
        }
        if ($headConstituentId !== null) {
            $fields[] = "head_constituent_id = :head_constituent_id";
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :family_id";
        $stmt = $this->db->connect()->prepare($sql);
        if ($familyName !== null) {
            $stmt->bindParam(':family_name', $familyName);
        }
        if ($headConstituentId !== null) {
            $stmt->bindParam(':head_constituent_id', $headConstituentId);
        }
        $stmt->bindParam(':family_id', $familyId);
        return $stmt->execute();
    }

    // Delete a family in a household
    public function deleteFamily($familyId)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :family_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':family_id', $familyId);
        return $stmt->execute();
    }

    // Count the total number of families inside a household
    public function countFamiliesInHousehold($householdId)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE household_id = :household_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':household_id', $householdId);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    // Count the total number of families
    public function countTotalFamilies()
    {
        // Only count families that belong to an existing household
        $sql = "SELECT COUNT(*) as total FROM {$this->table} f
                WHERE EXISTS (
                    SELECT 1 FROM households h WHERE h.id = f.household_id
                )";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetch()['total'];
    }
    public function getConstituentsInHouseholdNotInFamily($householdId)
    {
        $sql = "SELECT c.id, CONCAT(c.first_name, ' ', IFNULL(c.middle_name, ''), ' ', c.last_name, ' ', IFNULL(c.suffix, '')) AS full_name 
                FROM constituents c
                INNER JOIN constituents_families_households cfh ON c.id = cfh.constituent_id
                WHERE cfh.household_id = :household_id
                AND cfh.family_id IS NULL
                ORDER BY c.last_name, c.first_name ASC";
                
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':household_id', $householdId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFamiliesWithMembersByHouseholdId($household_id)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            SELECT 
                f.id AS family_id,
                f.family_name,
                f.head_constituent_id,
                CONCAT(c_head.first_name, ' ', IFNULL(c_head.middle_name, ''), ' ', c_head.last_name, ' ', IFNULL(c_head.suffix, '')) AS head_full_name,
                GROUP_CONCAT(
                    JSON_OBJECT(
                        'id', c_member.id,
                        'full_name', CONCAT(c_member.first_name, ' ', IFNULL(c_member.middle_name, ''), ' ', c_member.last_name, ' ', IFNULL(c_member.suffix, ''))
                    )
                ) AS members
            FROM {$this->table} f
            LEFT JOIN constituents c_head ON f.head_constituent_id = c_head.id
            LEFT JOIN constituents_families_households cfh ON f.id = cfh.family_id
            LEFT JOIN constituents c_member ON cfh.constituent_id = c_member.id
            WHERE f.household_id = :household_id
            GROUP BY f.id
        ");
        $stmt->bindParam(':household_id', $household_id, PDO::PARAM_INT);
        $stmt->execute();
        $families = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Process the members JSON string into an array
        foreach ($families as &$family) {
            if ($family['members']) {
                $members = json_decode('[' . $family['members'] . ']', true);
                $family['members'] = $members;
            } else {
                $family['members'] = [];
            }
        }

        return $families;
    }


    public function getTotalRecentFamiliesCount()
    {
        // This function will get the total number of families residing 5 years and below using the date_resided column
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE date_resided >= DATE_SUB(CURDATE(), INTERVAL 5 YEAR)";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    public function addMemberToFamily($constituentId, $familyId, $householdId)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            UPDATE constituents_families_households 
            SET family_id = :family_id 
            WHERE constituent_id = :constituent_id 
            AND household_id = :household_id
        ");
        $stmt->bindParam(':family_id', $familyId, PDO::PARAM_INT);
        $stmt->bindParam(':constituent_id', $constituentId, PDO::PARAM_INT);
        $stmt->bindParam(':household_id', $householdId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getFamilyById($familyId)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("SELECT * FROM {$this->table} WHERE id = :family_id");
        $stmt->bindParam(':family_id', $familyId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
   public function removeMemberFromFamily($constituentId, $familyId)
    {
        $connection = $this->db->connect();
        $connection->beginTransaction();

        try {
            // Clear head reference if this constituent is the family head
            $stmt = $connection->prepare("
                UPDATE families 
                SET head_constituent_id = NULL 
                WHERE id = :family_id 
                AND head_constituent_id = :constituent_id
            ");
            $stmt->bindParam(':family_id', $familyId, PDO::PARAM_INT);
            $stmt->bindParam(':constituent_id', $constituentId, PDO::PARAM_INT);
            $stmt->execute();

            // Set family_id to NULL in junction table (keeps them in household)
            $stmt = $connection->prepare("
                UPDATE constituents_families_households 
                SET family_id = NULL 
                WHERE constituent_id = :constituent_id 
                AND family_id = :family_id
            ");
            $stmt->bindParam(':constituent_id', $constituentId, PDO::PARAM_INT);
            $stmt->bindParam(':family_id', $familyId, PDO::PARAM_INT);
            $stmt->execute();

            $connection->commit();
            return true;
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }
    public function getFamilyMembersById($familyId)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            SELECT 
                c.id,
                CONCAT(c.first_name, ' ', IFNULL(c.middle_name, ''), ' ', c.last_name, ' ', IFNULL(c.suffix, '')) AS full_name,
                (SELECT head_constituent_id FROM families WHERE id = :family_id_sub) AS head_constituent_id
            FROM constituents_families_households cfh
            INNER JOIN constituents c ON cfh.constituent_id = c.id
            WHERE cfh.family_id = :family_id
            ORDER BY c.last_name, c.first_name ASC
        ");
        $stmt->bindParam(':family_id', $familyId, PDO::PARAM_INT);
        $stmt->bindParam(':family_id_sub', $familyId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
 * Demote the current head of a family to a regular member.
 * Called before promoting a new head.
 */
    public function demoteCurrentHead($familyId)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            UPDATE families 
            SET head_constituent_id = NULL 
            WHERE id = :family_id
        ");
        $stmt->bindParam(':family_id', $familyId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Set a specific constituent as the head of a family.
     */
    public function setFamilyHead($familyId, $constituentId)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            UPDATE families 
            SET head_constituent_id = :constituent_id 
            WHERE id = :family_id
        ");
        $stmt->bindParam(':constituent_id', $constituentId, PDO::PARAM_INT);
        $stmt->bindParam(':family_id', $familyId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}