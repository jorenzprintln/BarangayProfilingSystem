<?php

class VehicleRequest
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    // ── READ ───────────────────────────────────────────────────────────────

    /** All requests (admin queue), optionally filtered by status */
    public function getAll(string $status = '', string $search = ''): array
    {
        $conditions = [];
        $params     = [];

        if ($status !== '') {
            $conditions[]      = 'vr.status = :status';
            $params[':status'] = $status;
        }
        if ($search !== '') {
            $conditions[] = '(vr.plate_number LIKE :s1 OR vr.make LIKE :s2 OR vr.model LIKE :s3
                              OR CONCAT(c.last_name," ",c.first_name) LIKE :s4)';
            $sv                = '%' . $search . '%';
            $params[':s1']     = $sv;
            $params[':s2']     = $sv;
            $params[':s3']     = $sv;
            $params[':s4']     = $sv;
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "
            SELECT vr.*,
                u.username,
                TRIM(CONCAT(c.last_name, ', ', c.first_name, ' ',
                     COALESCE(c.middle_name,''), ' ', COALESCE(c.suffix,''))) AS owner_name,
                c.id AS constituent_id_resolved
            FROM vehicle_requests vr
            LEFT JOIN users        u ON vr.user_id       = u.id
            LEFT JOIN constituents c ON vr.constituent_id = c.id
            $where
            ORDER BY vr.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Single request by id */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT vr.*,
                u.username,
                TRIM(CONCAT(c.last_name, ', ', c.first_name, ' ',
                     COALESCE(c.middle_name,''), ' ', COALESCE(c.suffix,''))) AS owner_name
            FROM vehicle_requests vr
            LEFT JOIN users        u ON vr.user_id       = u.id
            LEFT JOIN constituents c ON vr.constituent_id = c.id
            WHERE vr.id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** Requests submitted by a specific user (constituent portal) */
    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM vehicle_requests
            WHERE user_id = :uid
            ORDER BY created_at DESC
        ");
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Count pending requests (for sidebar badge) */
    public function countPending(): int
    {
        return (int)$this->db->query(
            "SELECT COUNT(*) FROM vehicle_requests WHERE status = 'pending'"
        )->fetchColumn();
    }

    /** Does this user have an unseen approved/rejected result? */
    public function hasUnseenResult(int $userId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM vehicle_requests
            WHERE user_id = :uid
              AND status IN ('approved','rejected')
              AND seen_at IS NULL
        ");
        $stmt->execute([':uid' => $userId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    // ── WRITE ──────────────────────────────────────────────────────────────

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO vehicle_requests (
                user_id, constituent_id,
                plate_number, or_number, cr_number,
                vehicle_type, vehicle_use, make, model, year, color,
                fuel_type, transmission, engine_number, chassis_number,
                notes, created_at, updated_at
            ) VALUES (
                :user_id, :constituent_id,
                :plate_number, :or_number, :cr_number,
                :vehicle_type, :vehicle_use, :make, :model, :year, :color,
                :fuel_type, :transmission, :engine_number, :chassis_number,
                :notes, NOW(), NOW()
            )
        ");
        $stmt->execute([
            ':user_id'        => $data['user_id'],
            ':constituent_id' => $data['constituent_id'] ?: null,
            ':plate_number'   => $data['plate_number']   ?: null,
            ':or_number'      => $data['or_number']      ?: null,
            ':cr_number'      => $data['cr_number']      ?: null,
            ':vehicle_type'   => $data['vehicle_type']   ?: null,
            ':vehicle_use'    => $data['vehicle_use']    ?: 'Private',
            ':make'           => $data['make'],
            ':model'          => $data['model']          ?: null,
            ':year'           => $data['year']           ?: null,
            ':color'          => $data['color']          ?: null,
            ':fuel_type'      => $data['fuel_type']      ?: null,
            ':transmission'   => $data['transmission']   ?: null,
            ':engine_number'  => $data['engine_number']  ?: null,
            ':chassis_number' => $data['chassis_number'] ?: null,
            ':notes'          => $data['notes']          ?: null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Approve: create vehicle record + mark request approved */
    public function approve(int $id, int $reviewerId, string $note = ''): bool|string
    {
        $req = $this->findById($id);
        if (!$req) return false;

        // ── Duplicate plate check (only for new registrations) ──
        if (($req['request_type'] ?? 'new') !== 'edit' && !empty($req['plate_number'])) {
            $check = $this->db->prepare(
                "SELECT id FROM vehicles WHERE plate_number = :plate AND deleted_at IS NULL LIMIT 1"
            );
            $check->execute([':plate' => $req['plate_number']]);
            if ($check->fetch()) {
                return 'duplicate_plate';
            }
        }

        $this->db->beginTransaction();
        try {
            if (($req['request_type'] ?? 'new') === 'edit' && !empty($req['vehicle_id'])) {
                // UPDATE existing vehicle
                $stmt = $this->db->prepare("
                    UPDATE vehicles SET
                        plate_number   = :plate_number,
                        or_number      = :or_number,
                        cr_number      = :cr_number,
                        vehicle_type   = :vehicle_type,
                        vehicle_use    = :vehicle_use,
                        make           = :make,
                        model          = :model,
                        year           = :year,
                        color          = :color,
                        fuel_type      = :fuel_type,
                        transmission   = :transmission,
                        engine_number  = :engine_number,
                        chassis_number = :chassis_number,
                        notes          = :notes,
                        updated_at     = NOW()
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':plate_number'   => $req['plate_number'],
                    ':or_number'      => $req['or_number'],
                    ':cr_number'      => $req['cr_number'],
                    ':vehicle_type'   => $req['vehicle_type'],
                    ':vehicle_use'    => $req['vehicle_use'],
                    ':make'           => $req['make'],
                    ':model'          => $req['model'],
                    ':year'           => $req['year'],
                    ':color'          => $req['color'],
                    ':fuel_type'      => $req['fuel_type'],
                    ':transmission'   => $req['transmission'],
                    ':engine_number'  => $req['engine_number'],
                    ':chassis_number' => $req['chassis_number'],
                    ':notes'          => $req['notes'],
                    ':id'             => (int)$req['vehicle_id'],
                ]);
            } else {
                // INSERT new vehicle
                $stmt = $this->db->prepare("
                    INSERT INTO vehicles (
                        plate_number, or_number, cr_number,
                        vehicle_type, vehicle_use, make, model, year, color,
                        fuel_type, transmission, engine_number, chassis_number,
                        owner_type, owner_constituent_id,
                        notes, created_at, updated_at
                    ) VALUES (
                        :plate_number, :or_number, :cr_number,
                        :vehicle_type, :vehicle_use, :make, :model, :year, :color,
                        :fuel_type, :transmission, :engine_number, :chassis_number,
                        'constituent', :owner_constituent_id,
                        :notes, NOW(), NOW()
                    )
                ");
                $stmt->execute([
                    ':plate_number'         => $req['plate_number'],
                    ':or_number'            => $req['or_number'],
                    ':cr_number'            => $req['cr_number'],
                    ':vehicle_type'         => $req['vehicle_type'],
                    ':vehicle_use'          => $req['vehicle_use'],
                    ':make'                 => $req['make'],
                    ':model'                => $req['model'],
                    ':year'                 => $req['year'],
                    ':color'                => $req['color'],
                    ':fuel_type'            => $req['fuel_type'],
                    ':transmission'         => $req['transmission'],
                    ':engine_number'        => $req['engine_number'],
                    ':chassis_number'       => $req['chassis_number'],
                    ':owner_constituent_id' => $req['constituent_id'],
                    ':notes'                => $req['notes'],
                ]);
            }

            // Mark request approved
            $upd = $this->db->prepare("
                UPDATE vehicle_requests
                SET status = 'approved', secretary_note = :note,
                    reviewed_by = :rev, reviewed_at = NOW(), updated_at = NOW()
                WHERE id = :id
            ");
            $upd->execute([':note' => $note ?: null, ':rev' => $reviewerId, ':id' => $id]);

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /** Reject: just mark the request rejected */
    public function reject(int $id, int $reviewerId, string $note = ''): bool
    {
        $stmt = $this->db->prepare("
            UPDATE vehicle_requests
            SET status = 'rejected', secretary_note = :note,
                reviewed_by = :rev, reviewed_at = NOW(), updated_at = NOW()
            WHERE id = :id
        ");
        return $stmt->execute([':note' => $note ?: null, ':rev' => $reviewerId, ':id' => $id]);
    }

    /** Mark all of a user's resolved requests as seen */
    public function markSeen(int $userId): void
    {
        $this->db->prepare("
            UPDATE vehicle_requests
            SET seen_at = NOW()
            WHERE user_id = :uid AND status IN ('approved','rejected') AND seen_at IS NULL
        ")->execute([':uid' => $userId]);
    }
    public function createEditRequest(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO vehicle_requests (
                user_id, constituent_id, vehicle_id, request_type,
                plate_number, or_number, cr_number,
                vehicle_type, vehicle_use, make, model, year, color,
                fuel_type, transmission, engine_number, chassis_number,
                notes, created_at, updated_at
            ) VALUES (
                :user_id, :constituent_id, :vehicle_id, 'edit',
                :plate_number, :or_number, :cr_number,
                :vehicle_type, :vehicle_use, :make, :model, :year, :color,
                :fuel_type, :transmission, :engine_number, :chassis_number,
                :notes, NOW(), NOW()
            )
        ");
        $stmt->execute([
            ':user_id'        => $data['user_id'],
            ':constituent_id' => $data['constituent_id'] ?: null,
            ':vehicle_id'     => $data['vehicle_id'],
            ':plate_number'   => $data['plate_number']   ?: null,
            ':or_number'      => $data['or_number']      ?: null,
            ':cr_number'      => $data['cr_number']      ?: null,
            ':vehicle_type'   => $data['vehicle_type']   ?: null,
            ':vehicle_use'    => $data['vehicle_use']    ?: 'Private',
            ':make'           => $data['make'],
            ':model'          => $data['model']          ?: null,
            ':year'           => $data['year']           ?: null,
            ':color'          => $data['color']          ?: null,
            ':fuel_type'      => $data['fuel_type']      ?: null,
            ':transmission'   => $data['transmission']   ?: null,
            ':engine_number'  => $data['engine_number']  ?: null,
            ':chassis_number' => $data['chassis_number'] ?: null,
            ':notes'          => $data['notes']          ?: null,
        ]);
        return (int)$this->db->lastInsertId();
    }
}