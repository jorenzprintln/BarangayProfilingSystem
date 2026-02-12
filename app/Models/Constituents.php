<?php

class Constituents
{
    private $db;
    private $table = 'constituents';

    public function __construct()
    {
        $this->db = new Database();
    }

    public function create($data)
    {
        $connection = $this->db->connect();
        $connection->beginTransaction();

        try {
            $stmt = $connection->prepare("INSERT INTO {$this->table} (psn, last_name, first_name, middle_name, suffix, sex, birthdate, birthplace, civil_status, religion, citizenship, occupation, contact, email, education_attainment, is_graduate, registered_voter) VALUES (:psn, :last_name, :first_name, :middle_name, :suffix, :sex, :birthdate, :birthplace, :civil_status, :religion, :citizenship, :occupation, :contact, :email, :education_attainment, :is_graduate, :registered_voter)");

            $stmt->bindParam(':psn', $data['psn']);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':middle_name', $data['middle_name']);
            $stmt->bindParam(':suffix', $data['suffix']);
            $stmt->bindParam(':sex', $data['sex']);
            $stmt->bindParam(':birthdate', $data['birthdate']);
            $stmt->bindParam(':birthplace', $data['birthplace']);
            $stmt->bindParam(':civil_status', $data['civil_status']);
            $stmt->bindParam(':religion', $data['religion']);
            $stmt->bindParam(':citizenship', $data['citizenship']);
            $stmt->bindParam(':occupation', $data['occupation']);
            $stmt->bindParam(':contact', $data['contact']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':education_attainment', $data['education_attainment']);
            $stmt->bindParam(':is_graduate', $data['is_graduate']);
            $stmt->bindParam(':registered_voter', $data['registered_voter']);

            $stmt->execute();
            $constituent_id = $connection->lastInsertId();

            $connection->commit();
            return $constituent_id;
        } catch (PDOException $e) {
            $connection->rollBack();
            error_log("PDO Error: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $connection->rollBack();
            error_log("General Error: " . $e->getMessage());
            return false;
        }
    }

    public function update($data)
    {
        $connection = $this->db->connect();
        $connection->beginTransaction();

        try {
            $stmt = $connection->prepare("UPDATE {$this->table} SET psn = :psn, last_name = :last_name, first_name = :first_name, middle_name = :middle_name, suffix = :suffix, sex = :sex, birthdate = :birthdate, birthplace = :birthplace, civil_status = :civil_status, religion = :religion, citizenship = :citizenship, occupation = :occupation, contact = :contact, email = :email, education_attainment = :education_attainment, is_graduate = :is_graduate, registered_voter = :registered_voter WHERE id = :id");

            $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);
            $stmt->bindParam(':psn', $data['psn'], $data['psn'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':middle_name', $data['middle_name']);
            $stmt->bindParam(':suffix', $data['suffix']);
            $stmt->bindParam(':sex', $data['sex']);
            $stmt->bindParam(':birthdate', $data['birthdate']);
            $stmt->bindParam(':birthplace', $data['birthplace']);
            $stmt->bindParam(':civil_status', $data['civil_status']);
            $stmt->bindParam(':religion', $data['religion']);
            $stmt->bindParam(':citizenship', $data['citizenship']);
            $stmt->bindParam(':occupation', $data['occupation']);
            $stmt->bindParam(':contact', $data['contact']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':education_attainment', $data['education_attainment']);
            $stmt->bindParam(':is_graduate', $data['is_graduate']);
            $stmt->bindParam(':registered_voter', $data['registered_voter']);

            $stmt->execute();
            $connection->commit();
            return true;
        } catch (PDOException $e) {
            $connection->rollBack();
            error_log("PDO Error: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            $connection->rollBack();
            error_log("General Error: " . $e->getMessage());
            return false;
        }
    }

    public function getAll()
    {
        $stmt = $this->db->connect()->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllNotRemoved()
    {
        $stmt = $this->db->connect()->prepare("SELECT * FROM {$this->table} WHERE removed_at IS NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRemoved()
    {
        $stmt = $this->db->connect()->prepare("SELECT * FROM {$this->table} WHERE removed_at IS NOT NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount()
    {
        $stmt = $this->db->connect()->prepare("SELECT COUNT(*) FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function get($id)
    {
        $stmt = $this->db->connect()->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAge($birthdate)
    {
        $stmt = $this->db->connect()->prepare("SELECT TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) AS age FROM {$this->table} WHERE birthdate = :birthdate");
        $stmt->bindParam(':birthdate', $birthdate);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function remove($id)
    {
        $stmt = $this->db->connect()->prepare("UPDATE {$this->table} SET removed_at = NOW() WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function restore($id)
    {
        $stmt = $this->db->connect()->prepare("UPDATE {$this->table} SET removed_at = NULL WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function getDependents()
    {
        $query = "
            SELECT 
                c.id,
                c.first_name,
                SUBSTRING(c.middle_name, 1, 1) AS middle_initial,
                c.last_name,
                DATE_FORMAT(c.birthdate, '%m/%d/%Y') AS formatted_birthdate,
                cfh.family_id
            FROM 
                {$this->table} c
            JOIN 
                constituents_families_households cfh ON c.id = cfh.constituent_id
            WHERE 
                c.removed_at IS NULL
            ORDER BY
                cfh.family_id, c.last_name, c.first_name
        ";
        
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getConsituentsCountByAgeCategory()
    {
        $query = "
            SELECT 
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 0 AND 4 THEN '0-4'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 5 AND 9 THEN '5-9'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 10 AND 14 THEN '10-14'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 15 AND 19 THEN '15-19'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 20 AND 24 THEN '20-24'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 25 AND 29 THEN '25-29'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 30 AND 34 THEN '30-34'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 35 AND 39 THEN '35-39'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 40 AND 44 THEN '40-44'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 45 AND 49 THEN '45-49'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 50 AND 54 THEN '50-54'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 55 AND 59 THEN '55-59'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 60 AND 64 THEN '60-64'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 65 AND 69 THEN '65-69'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 70 AND 74 THEN '70-74'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 75 AND 79 THEN '75-79'
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 80 THEN '80 and above'
                    ELSE 'Unknown'
                END AS age_range,
                sex,
                COUNT(*) AS count
            FROM {$this->table}
            WHERE removed_at IS NULL AND birthdate IS NOT NULL
            GROUP BY age_range, sex
            ORDER BY 
                CASE age_range
                    WHEN '0-4' THEN 1
                    WHEN '5-9' THEN 2
                    WHEN '10-14' THEN 3
                    WHEN '15-19' THEN 4
                    WHEN '20-24' THEN 5
                    WHEN '25-29' THEN 6
                    WHEN '30-34' THEN 7
                    WHEN '35-39' THEN 8
                    WHEN '40-44' THEN 9
                    WHEN '45-49' THEN 10
                    WHEN '50-54' THEN 11
                    WHEN '55-59' THEN 12
                    WHEN '60-64' THEN 13
                    WHEN '65-69' THEN 14
                    WHEN '70-74' THEN 15
                    WHEN '75-79' THEN 16
                    WHEN '80 and above' THEN 17
                    ELSE 18
                END, sex
        ";

        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restructure data for easier consumption
        $ageRanges = [
            '0-4',
            '5-9',
            '10-14',
            '15-19',
            '20-24',
            '25-29',
            '30-34',
            '35-39',
            '40-44',
            '45-49',
            '50-54',
            '55-59',
            '60-64',
            '65-69',
            '70-74',
            '75-79',
            '80 and above'
        ];

        $data = [];
        foreach ($ageRanges as $range) {
            $data[$range] = [
                'MALE' => 0,
                'FEMALE' => 0,
                'total' => 0
            ];
        }

        // Fill in the actual counts
        foreach ($results as $row) {
            $ageRange = $row['age_range'];
            $sex = $row['sex'];
            $count = (int) $row['count'];

            if (isset($data[$ageRange])) {
                $data[$ageRange][$sex] = $count;
                $data[$ageRange]['total'] += $count;
            }
        }

        // Calculate totals for each gender and overall
        $totals = [
            'MALE' => 0,
            'FEMALE' => 0,
            'total' => 0
        ];

        foreach ($data as $ageRange => $counts) {
            $totals['MALE'] += $counts['MALE'];
            $totals['FEMALE'] += $counts['FEMALE'];
            $totals['total'] += $counts['total'];
        }

        $data['TOTAL'] = $totals;

        return $data;
    }

    public function getConsituentsCountByEducationAttainment()
    {
        $query = "
            SELECT 
                CASE 
                    WHEN education_attainment = 1 THEN 'Daycare'
                    WHEN education_attainment = 2 THEN 'Nursery'
                    WHEN education_attainment = 3 THEN 'Kinder'
                    WHEN education_attainment = 4 AND is_graduate = 'NO' THEN 'Elementary Level'
                    WHEN education_attainment = 4 AND is_graduate = 'YES' THEN 'Elementary Graduate'
                    WHEN education_attainment = 5 THEN 'ALS High School'
                    WHEN education_attainment = 6 AND is_graduate = 'NO' THEN 'High School Level'
                    WHEN education_attainment = 6 AND is_graduate = 'YES' THEN 'High School Graduate'
                    WHEN education_attainment = 7 THEN 'Junior High'
                    WHEN education_attainment = 8 THEN 'Senior High'
                    WHEN education_attainment = 9 THEN 'Vocational'
                    WHEN education_attainment = 10 AND is_graduate = 'NO' THEN 'College Level'
                    WHEN education_attainment = 10 AND is_graduate = 'YES' THEN 'College Graduate'
                    WHEN education_attainment = 11 THEN 'Post Graduate'
                    ELSE 'Unknown'
                END AS education_level,
                sex,
                COUNT(*) AS count
            FROM {$this->table}
            WHERE removed_at IS NULL
            GROUP BY education_level, sex
            ORDER BY education_level, sex
        ";

        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restructure data for easier consumption
        $educationLevels = [
            'Daycare',
            'Nursery',
            'Kinder',
            'Elementary Level',
            'Elementary Graduate',
            'ALS High School',
            'High School Level',
            'High School Graduate',
            'Junior High',
            'Senior High',
            'Vocational',
            'College Level',
            'College Graduate',
            'Post Graduate'
        ];

        $data = [];
        foreach ($educationLevels as $level) {
            $data[$level] = [
                'MALE' => 0,
                'FEMALE' => 0,
                'total' => 0
            ];
        }

        // Fill in the actual counts
        foreach ($results as $row) {
            $educationLevel = $row['education_level'];
            $sex = $row['sex'];
            $count = (int) $row['count'];

            if (isset($data[$educationLevel])) {
                $data[$educationLevel][$sex] = $count;
                $data[$educationLevel]['total'] += $count;
            }
        }

        // Calculate totals for each gender and overall
        $totals = [
            'MALE' => 0,
            'FEMALE' => 0,
            'total' => 0
        ];

        foreach ($data as $educationLevel => $counts) {
            $totals['MALE'] += $counts['MALE'];
            $totals['FEMALE'] += $counts['FEMALE'];
            $totals['total'] += $counts['total'];
        }

        $data['TOTAL'] = $totals;

        return $data;
    }

    public function getConsituentsCountByOccupation()
    {
        $query = "
            SELECT 
                occupation,
                sex,
                COUNT(*) AS count
            FROM {$this->table}
            WHERE removed_at IS NULL
            GROUP BY occupation, sex
            ORDER BY occupation, sex
        ";

        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Define occupation categories
        $occupations = [
            'Government Employee',
            'Private Employee',
            'Barangay Official',
            'Barangay Volunteers',
            'OFW',
            'Business',
            'Carpenter',
            'Laborer/Construction',
            'Driver',
            'Sari-Sari Store',
            'Self-Employed'
        ];

        // Initialize data structure
        $data = [];
        foreach ($occupations as $occupation) {
            $data[$occupation] = [
                'MALE' => 0,
                'FEMALE' => 0,
                'total' => 0
            ];
        }

        // Fill in the actual counts
        foreach ($results as $row) {
            $occupation = $row['occupation'];
            $sex = $row['sex'];
            $count = (int) $row['count'];

            if (isset($data[$occupation])) {
                $data[$occupation][$sex] = $count;
                $data[$occupation]['total'] += $count;
            }
        }

        // Calculate totals for each gender and overall
        $totals = [
            'MALE' => 0,
            'FEMALE' => 0,
            'total' => 0
        ];

        foreach ($data as $occupation => $counts) {
            $totals['MALE'] += $counts['MALE'];
            $totals['FEMALE'] += $counts['FEMALE'];
            $totals['total'] += $counts['total'];
        }

        $data['TOTAL'] = $totals;

        return $data;
    }

    public function getTotalSeniorCitizensByAge()
    {
        $query = "
            SELECT 
                TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) AS age,
                sex,
                COUNT(*) AS count
            FROM {$this->table}
            WHERE 
                removed_at IS NULL 
                AND birthdate IS NOT NULL
                AND TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 60
            GROUP BY age, sex
            ORDER BY age, sex
        ";

        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Structure the data
        $data = [];
        foreach ($results as $row) {
            $age = $row['age'];
            $sex = $row['sex'];
            $count = (int) $row['count'];

            if (!isset($data[$age])) {
                $data[$age] = [
                    'MALE' => 0,
                    'FEMALE' => 0,
                    'total' => 0
                ];
            }

            $data[$age][$sex] = $count;
            $data[$age]['total'] += $count;
        }

        // Calculate totals
        $totals = [
            'MALE' => 0,
            'FEMALE' => 0,
            'total' => 0
        ];

        foreach ($data as $age => $counts) {
            $totals['MALE'] += $counts['MALE'];
            $totals['FEMALE'] += $counts['FEMALE'];
            $totals['total'] += $counts['total'];
        }

        $data['TOTAL'] = $totals;

        return $data;
    }
}
