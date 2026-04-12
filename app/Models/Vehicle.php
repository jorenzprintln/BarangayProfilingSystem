<?php

class Vehicle
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    // ── READ ───────────────────────────────────────────────────────────────

    public function getAll(array $filters = [], int $limit = 10, int $offset = 0): array
    {
        [$where, $params] = $this->buildWhere($filters);

        $sql = "
            SELECT
                v.*,
                CASE
                    WHEN v.owner_type = 'constituent' AND c.id IS NOT NULL
                    THEN TRIM(CONCAT(c.last_name, ', ', c.first_name, ' ', COALESCE(c.middle_name,''), ' ', COALESCE(c.suffix,'')))
                    ELSE v.external_owner_name
                END AS owner_name,
                CASE
                    WHEN v.owner_type = 'constituent' THEN c.id
                    ELSE NULL
                END AS owner_id
            FROM vehicles v
            LEFT JOIN constituents c ON v.owner_constituent_id = c.id AND c.removed_at IS NULL
            $where
            ORDER BY v.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll(array $filters = []): int
    {
        [$where, $params] = $this->buildWhere($filters);

        $sql = "
            SELECT COUNT(*) FROM vehicles v
            LEFT JOIN constituents c ON v.owner_constituent_id = c.id AND c.removed_at IS NULL
            $where
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id): array|false
    {
        $sql = "
            SELECT
                v.*,
                CASE
                    WHEN v.owner_type = 'constituent' AND c.id IS NOT NULL
                    THEN TRIM(CONCAT(c.last_name, ', ', c.first_name, ' ', COALESCE(c.middle_name,''), ' ', COALESCE(c.suffix,'')))
                    ELSE v.external_owner_name
                END AS owner_name,
                CASE
                    WHEN v.owner_type = 'constituent' THEN c.id
                    ELSE NULL
                END AS owner_id
            FROM vehicles v
            LEFT JOIN constituents c ON v.owner_constituent_id = c.id AND c.removed_at IS NULL
            WHERE v.id = :id AND v.deleted_at IS NULL
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByPlate(string $plate, int $excludeId = 0): array|false
    {
        $sql = "SELECT id FROM vehicles WHERE plate_number = :plate AND id != :exclude AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':plate' => $plate, ':exclude' => $excludeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ── WRITE ──────────────────────────────────────────────────────────────

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO vehicles (
                plate_number, or_number, cr_number,
                vehicle_type, vehicle_use, make, model, year, color,
                fuel_type, transmission, engine_number, chassis_number,
                owner_type, owner_constituent_id,
                external_owner_name, external_owner_address,
                notes, created_at, updated_at
            ) VALUES (
                :plate_number, :or_number, :cr_number,
                :vehicle_type, :vehicle_use, :make, :model, :year, :color,
                :fuel_type, :transmission, :engine_number, :chassis_number,
                :owner_type, :owner_constituent_id,
                :external_owner_name, :external_owner_address,
                :notes, NOW(), NOW()
            )
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->bindData($data));

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE vehicles SET
                plate_number           = :plate_number,
                or_number              = :or_number,
                cr_number              = :cr_number,
                vehicle_type           = :vehicle_type,
                vehicle_use            = :vehicle_use,
                make                   = :make,
                model                  = :model,
                year                   = :year,
                color                  = :color,
                fuel_type              = :fuel_type,
                transmission           = :transmission,
                engine_number          = :engine_number,
                chassis_number         = :chassis_number,
                owner_type             = :owner_type,
                owner_constituent_id   = :owner_constituent_id,
                external_owner_name    = :external_owner_name,
                external_owner_address = :external_owner_address,
                notes                  = :notes,
                updated_at             = NOW()
            WHERE id = :id
        ";

        $params        = $this->bindData($data);
        $params[':id'] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE vehicles SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ── HELPERS ────────────────────────────────────────────────────────────

    private function buildWhere(array $filters): array
    {
        $conditions = ['v.deleted_at IS NULL'];
        $params     = [];

        if (!empty($filters['search'])) {
            $conditions[] = "(
                v.plate_number           LIKE :search1
                OR v.make                LIKE :search2
                OR v.model               LIKE :search3
                OR TRIM(CONCAT(c.last_name, ' ', c.first_name)) LIKE :search4
                OR v.external_owner_name LIKE :search5
            )";
            $searchVal           = '%' . $filters['search'] . '%';
            $params[':search1']  = $searchVal;
            $params[':search2']  = $searchVal;
            $params[':search3']  = $searchVal;
            $params[':search4']  = $searchVal;
            $params[':search5']  = $searchVal;
        }

        if (!empty($filters['vehicle_type'])) {
            $conditions[]            = 'v.vehicle_type = :vehicle_type';
            $params[':vehicle_type'] = $filters['vehicle_type'];
        }

        if (!empty($filters['fuel_type'])) {
            $conditions[]          = 'v.fuel_type = :fuel_type';
            $params[':fuel_type']  = $filters['fuel_type'];
        }

        if (!empty($filters['color'])) {
            $conditions[]     = 'v.color LIKE :color';
            $params[':color'] = '%' . $filters['color'] . '%';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return [$where, $params];
    }

    private function bindData(array $data): array
    {
        return [
            ':plate_number'           => $data['plate_number']           ?: null,
            ':or_number'              => $data['or_number']              ?: null,
            ':cr_number'              => $data['cr_number']              ?: null,
            ':vehicle_type'           => $data['vehicle_type']           ?: null,
            ':vehicle_use'            => $data['vehicle_use']            ?: 'Private',
            ':make'                   => $data['make']                   ?: null,
            ':model'                  => $data['model']                  ?: null,
            ':year'                   => $data['year']                   ?: null,
            ':color'                  => $data['color']                  ?: null,
            ':fuel_type'              => $data['fuel_type']              ?: null,
            ':transmission'           => $data['transmission']           ?: null,
            ':engine_number'          => $data['engine_number']          ?: null,
            ':chassis_number'         => $data['chassis_number']         ?: null,
            ':owner_type'             => $data['owner_type']             ?: 'constituent',
            ':owner_constituent_id'   => $data['owner_constituent_id']   ?: null,
            ':external_owner_name'    => $data['external_owner_name']    ?: null,
            ':external_owner_address' => $data['external_owner_address'] ?: null,
            ':notes'                  => $data['notes']                  ?: null,
        ];
    }
    public function getArchived(string $search = ''): array
    {
        $sql = "
            SELECT v.*,
                CASE
                    WHEN v.owner_type = 'constituent' AND c.id IS NOT NULL
                    THEN TRIM(CONCAT(c.last_name, ', ', c.first_name, ' ', COALESCE(c.middle_name,''), ' ', COALESCE(c.suffix,'')))
                    ELSE v.external_owner_name
                END AS owner_name
            FROM vehicles v
            LEFT JOIN constituents c ON v.owner_constituent_id = c.id
            WHERE v.deleted_at IS NOT NULL
        ";

        $params = [];
        if (!empty($search)) {
            $sql .= " AND (v.plate_number LIKE :search OR v.make LIKE :search OR v.model LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY v.deleted_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function restore(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE vehicles SET deleted_at = NULL WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
    public function findArchivedById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT id FROM vehicles WHERE id = :id AND deleted_at IS NOT NULL LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getByOwnerId(int $ownerId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM vehicles WHERE owner_constituent_id = :owner_id AND deleted_at IS NULL ORDER BY created_at DESC"
        );
        $stmt->execute([':owner_id' => $ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}