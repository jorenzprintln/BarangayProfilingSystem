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
        $stmt->bindParam(':household_number',      $data['household_number'],      PDO::PARAM_STR);
        $stmt->bindParam(':region',                $data['region'],                PDO::PARAM_STR);
        $stmt->bindParam(':province',              $data['province'],              PDO::PARAM_STR);
        $stmt->bindParam(':city_municipality',     $data['city_municipality'],     PDO::PARAM_STR);
        $stmt->bindParam(':barangay_code',         $data['barangay_code'],         PDO::PARAM_STR);
        $stmt->bindParam(':barangay_name',         $data['barangay_name'],         PDO::PARAM_STR);
        $stmt->bindParam(':street_name',           $data['street_name'],           PDO::PARAM_STR);
        $stmt->bindParam(':zip_code',              $data['zip_code'],              PDO::PARAM_STR);
        $stmt->bindParam(':purok',                 $data['purok'],                 PDO::PARAM_STR);
        $stmt->bindParam(':block_number',          $data['block_number'],          PDO::PARAM_STR);
        $stmt->bindParam(':lot_number',            $data['lot_number'],            PDO::PARAM_STR);
        $stmt->bindParam(':house_building_number', $data['house_building_number'], PDO::PARAM_STR);
        $stmt->bindParam(':unit_number',           $data['unit_number'],           PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLastInsertedId()
    {
        return $this->db->connect()->lastInsertId();
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
                CASE
                    WHEN c.id IS NULL THEN 'N/A'
                    WHEN c.removed_at IS NOT NULL THEN 'N/A (Archived)'
                    ELSE CONCAT(c.first_name, ' ', IFNULL(c.middle_name, ''), ' ', c.last_name, ' ', IFNULL(c.suffix, ''))
                END AS full_name,
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

    // ── NEW: Server-side filtered + paginated fetch ───────────────────────────

    /**
     * Shared WHERE + JOIN snippet builder used by both fetch and count methods.
     * Returns ['sql' => string, 'params' => array]
     */
    private function buildHouseholdFilterQuery(string $search): array
    {
        $like   = '%' . $search . '%';
        $params = [];

        $baseJoin = "
            FROM {$this->table} h
            LEFT JOIN constituents_families_households cfh
                   ON h.id = cfh.household_id AND cfh.is_head = 'YES'
            LEFT JOIN constituents c ON cfh.constituent_id = c.id
        ";

        if ($search !== '') {
            $where = "
                WHERE (
                    h.household_number LIKE :like_number
                    OR CONCAT(
                        IFNULL(c.first_name, ''), ' ',
                        IFNULL(c.middle_name, ''), ' ',
                        IFNULL(c.last_name, ''), ' ',
                        IFNULL(c.suffix, '')
                    ) LIKE :like_name
                )
            ";
            $params[':like_number'] = $like;
            $params[':like_name']   = $like;
        } else {
            $where = '';
        }

        return [
            'join'   => $baseJoin,
            'where'  => $where,
            'params' => $params,
        ];
    }

    /**
     * Fetch a filtered + paginated page of households.
     *
     * @param string $search  Search term (household number or head name)
     * @param int    $limit
     * @param int    $offset
     * @return array
     */
    public function getFilteredHouseholds(string $search = '', int $limit = 10, int $offset = 0): array
    {
        $connection = $this->db->connect();
        $parts      = $this->buildHouseholdFilterQuery($search);

        $sql = "
            SELECT
                h.id,
                h.household_number,
                CASE
                    WHEN c.id IS NULL THEN 'N/A'
                    WHEN c.removed_at IS NOT NULL THEN 'N/A (Archived)'
                    ELSE CONCAT(c.first_name, ' ', IFNULL(c.middle_name, ''), ' ', c.last_name, ' ', IFNULL(c.suffix, ''))
                END AS full_name,
                (SELECT COUNT(DISTINCT cfh2.family_id)
                 FROM constituents_families_households cfh2
                 WHERE cfh2.household_id = h.id) AS total_families,
                (SELECT COUNT(*)
                 FROM constituents_families_households cfh3
                 WHERE cfh3.household_id = h.id) AS total_members
            {$parts['join']}
            {$parts['where']}
            ORDER BY h.household_number ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $connection->prepare($sql);
        foreach ($parts['params'] as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count total matching households for the same search (used for pagination math).
     *
     * @param string $search
     * @return int
     */
    public function countFilteredHouseholds(string $search = ''): int
    {
        $connection = $this->db->connect();
        $parts      = $this->buildHouseholdFilterQuery($search);

        $sql = "SELECT COUNT(*) {$parts['join']} {$parts['where']}";

        $stmt = $connection->prepare($sql);
        foreach ($parts['params'] as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Search households by household number or head of household name.
     * Returns all matching rows (kept for backward compatibility).
     */
    public function searchHouseholds(string $search): array
    {
        $connection = $this->db->connect();
        $like = '%' . $search . '%';

        $stmt = $connection->prepare("
            SELECT
                h.id,
                h.household_number,
                CASE
                    WHEN c.id IS NULL THEN 'N/A'
                    WHEN c.removed_at IS NOT NULL THEN 'N/A (Archived)'
                    ELSE CONCAT(c.first_name, ' ', IFNULL(c.middle_name, ''), ' ', c.last_name, ' ', IFNULL(c.suffix, ''))
                END AS full_name,
                (SELECT COUNT(DISTINCT cfh2.family_id)
                 FROM constituents_families_households cfh2
                 WHERE cfh2.household_id = h.id) AS total_families,
                (SELECT COUNT(*)
                 FROM constituents_families_households cfh3
                 WHERE cfh3.household_id = h.id) AS total_members
            FROM {$this->table} h
            LEFT JOIN constituents_families_households cfh ON h.id = cfh.household_id AND cfh.is_head = 'YES'
            LEFT JOIN constituents c ON cfh.constituent_id = c.id
            WHERE
                h.household_number LIKE :like_number
                OR CONCAT(
                    c.first_name, ' ',
                    IFNULL(c.middle_name, ''), ' ',
                    c.last_name, ' ',
                    IFNULL(c.suffix, '')
                ) LIKE :like_name
            ORDER BY h.household_number ASC
        ");

        $stmt->bindParam(':like_number', $like, PDO::PARAM_STR);
        $stmt->bindParam(':like_name',   $like, PDO::PARAM_STR);
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
                CONCAT(c.first_name, ' ', IFNULL(c.middle_name, ''), ' ', c.last_name, ' ', IFNULL(c.suffix, '')) AS full_name,
                cfh.role,
                cfh.is_head,
                TIMESTAMPDIFF(YEAR, c.birthdate, CURDATE()) AS age,
                c.contact AS contact,
                c.sex,
                c.civil_status,
                CASE WHEN c.removed_at IS NOT NULL THEN 1 ELSE 0 END AS is_archived
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
                c.birthdate  AS date_of_birth,
                TIMESTAMPDIFF(YEAR, c.birthdate, CURDATE()) AS age,
                c.sex,
                c.civil_status,
                c.citizenship,
                c.occupation,
                GROUP_CONCAT(cl.code SEPARATOR ', ') AS classification
            FROM constituents_families_households ch
            INNER JOIN constituents c ON ch.constituent_id = c.id
            LEFT JOIN constituents_classifications cc ON c.id = cc.constituent_id
            LEFT JOIN classifications cl ON cc.classification_id = cl.id
            WHERE ch.household_id = :household_id
              AND c.removed_at IS NULL
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
        $stmt->bindParam(':household_id',   $household_id,   PDO::PARAM_INT);
        $stmt->bindParam(':constituent_id', $constituent_id, PDO::PARAM_INT);
        $stmt->bindParam(':role',           $role,           PDO::PARAM_STR);
        $stmt->bindParam(':is_head',        $is_head,        PDO::PARAM_STR);
        $stmt->execute();
    }

    public function getHouseholdHead($household_id)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            SELECT CONCAT(c.first_name, ' ', IFNULL(c.middle_name, ''), ' ', c.last_name, ' ', IFNULL(c.suffix, '')) AS full_name
            FROM constituents_families_households ch
            INNER JOIN constituents c ON ch.constituent_id = c.id
            WHERE ch.household_id = :household_id
              AND ch.is_head = 'YES'
              AND c.removed_at IS NULL
            LIMIT 1
        ");
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
        return $result ?: [];
    }

    public function getHouseholdsWithInformation()
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            SELECT
                h.id,
                h.household_number,
                CASE
                    WHEN c.id IS NULL THEN 'N/A'
                    WHEN c.removed_at IS NOT NULL THEN 'N/A (Archived)'
                    ELSE TRIM(REGEXP_REPLACE(
                        CONCAT(
                            c.first_name, ' ',
                            IFNULL(c.middle_name, ''), ' ',
                            c.last_name, ' ',
                            IFNULL(c.suffix, '')
                        ), ' +', ' '
                    ))
                END AS head_of_household,
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalHouseholdsCount()
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            SELECT COUNT(*) FROM {$this->table}
            WHERE EXISTS (
                SELECT 1 FROM constituents_families_households cfh
                WHERE cfh.household_id = {$this->table}.id
            )
        ");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function delete($householdId)
    {
        try {
            $connection = $this->db->connect();
            $connection->beginTransaction();

            error_log("Starting transaction for household deletion ID: $householdId");

            $stmt = $connection->prepare("DELETE FROM constituents_families_households WHERE household_id = :household_id");
            $stmt->bindParam(':household_id', $householdId, PDO::PARAM_INT);
            $stmt->execute();
            error_log("Removed related constituents_families_households records: " . $stmt->rowCount());

            $stmt = $connection->prepare("DELETE FROM {$this->table} WHERE id = :household_id");
            $stmt->bindParam(':household_id', $householdId, PDO::PARAM_INT);
            $result = $stmt->execute();
            error_log("Household delete result: " . ($result ? "success" : "failed") . ", Rows affected: " . $stmt->rowCount());

            $connection->commit();
            error_log("Transaction committed for household deletion");

            return $result;
        } catch (Exception $e) {
            if (isset($connection)) {
                $connection->rollBack();
                error_log("Transaction rolled back due to error: " . $e->getMessage());
            }
            error_log("Exception in household delete: " . $e->getMessage());
            throw $e;
        }
    }

    public function removeMemberFromHousehold($constituentId, $householdId)
    {
        $connection = $this->db->connect();
        $connection->beginTransaction();

        try {
            $stmt = $connection->prepare("
                UPDATE families
                SET head_constituent_id = NULL
                WHERE head_constituent_id = :constituent_id
                  AND household_id = :household_id
            ");
            $stmt->bindParam(':constituent_id', $constituentId, PDO::PARAM_INT);
            $stmt->bindParam(':household_id',   $householdId,   PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $connection->prepare("
                DELETE FROM constituents_families_households
                WHERE constituent_id = :constituent_id
                  AND household_id = :household_id
            ");
            $stmt->bindParam(':constituent_id', $constituentId, PDO::PARAM_INT);
            $stmt->bindParam(':household_id',   $householdId,   PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $connection->prepare("
                DELETE FROM families
                WHERE household_id = :household_id_a
                  AND id NOT IN (
                    SELECT DISTINCT family_id
                    FROM constituents_families_households
                    WHERE household_id = :household_id_b
                      AND family_id IS NOT NULL
                  )
            ");
            $stmt->bindParam(':household_id_a', $householdId, PDO::PARAM_INT);
            $stmt->bindParam(':household_id_b', $householdId, PDO::PARAM_INT);
            $stmt->execute();

            $connection->commit();
            return true;
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public function demoteHouseholdHead($householdId)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            UPDATE constituents_families_households
            SET is_head = 'NO'
            WHERE household_id = :household_id
              AND is_head = 'YES'
        ");
        $stmt->bindParam(':household_id', $householdId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function promoteHouseholdHead($householdId, $constituentId)
    {
        $connection = $this->db->connect();
        $stmt = $connection->prepare("
            UPDATE constituents_families_households
            SET is_head = 'YES'
            WHERE household_id = :household_id
              AND constituent_id = :constituent_id
        ");
        $stmt->bindParam(':household_id',   $householdId,   PDO::PARAM_INT);
        $stmt->bindParam(':constituent_id', $constituentId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function searchHouseholdsWithInformation(string $search): array
    {
        $connection = $this->db->connect();
        $like = '%' . $search . '%';

        $stmt = $connection->prepare("
            SELECT
                h.id,
                h.household_number,
                CASE
                    WHEN c.id IS NULL THEN 'N/A'
                    WHEN c.removed_at IS NOT NULL THEN 'N/A (Archived)'
                    ELSE CONCAT(c.first_name, ' ', c.middle_name, ' ', c.last_name, ' ', IFNULL(c.suffix, ''))
                END AS head_of_household,
                (SELECT COUNT(DISTINCT family_id) FROM constituents_families_households WHERE household_id = h.id) AS number_of_families,
                (SELECT COUNT(*) FROM constituents_families_households ch WHERE ch.household_id = h.id) AS number_of_members
            FROM households h
            LEFT JOIN constituents_families_households ch ON h.id = ch.household_id AND ch.is_head = 'YES'
            LEFT JOIN constituents c ON ch.constituent_id = c.id
            WHERE EXISTS (
                SELECT 1 FROM constituents_families_households cfh
                WHERE cfh.household_id = h.id
            )
            AND (
                h.household_number LIKE :like_number
                OR CONCAT(
                    c.first_name, ' ',
                    IFNULL(c.middle_name, ''), ' ',
                    c.last_name, ' ',
                    IFNULL(c.suffix, '')
                ) LIKE :like_name
            )
            ORDER BY h.household_number
        ");

        $stmt->bindParam(':like_number', $like, PDO::PARAM_STR);
        $stmt->bindParam(':like_name',   $like, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}