<?php

class Transactions
{
    private $db;
    private $table = 'transactions';

    public function __construct()
    {
        $this->db = new Database();
        error_log("Transactions model instantiated");
    }
    
    public function create($data)
    {
        error_log("=== TRANSACTION CREATE CALLED ===");
        error_log("Incoming data: " . print_r($data, true));
        
        try {
            $connection = $this->db->connect();
            error_log("Database connection obtained");
            
            $sql = "INSERT INTO {$this->table} (transaction, requested_by, generated_by, document_location, date_of_transaction, purpose) 
                    VALUES (:transaction, :requested_by, :generated_by, :document_location, :date_of_transaction, :purpose)";
            
            error_log("SQL: " . $sql);
            
            $stmt = $connection->prepare($sql);
            
            if (!$stmt) {
                error_log("PREPARE FAILED: " . print_r($connection->errorInfo(), true));
                return false;
            }
            
            error_log("Statement prepared successfully");
            
            $bindResult = true;
            $bindResult = $bindResult && $stmt->bindParam(':transaction', $data['transaction']);
            $bindResult = $bindResult && $stmt->bindParam(':requested_by', $data['requested_by']);
            $bindResult = $bindResult && $stmt->bindParam(':generated_by', $data['generated_by']);
            $bindResult = $bindResult && $stmt->bindParam(':document_location', $data['document_location']);
            $bindResult = $bindResult && $stmt->bindParam(':date_of_transaction', $data['date_of_transaction']);
            $bindResult = $bindResult && $stmt->bindParam(':purpose', $data['purpose']);
            
            if (!$bindResult) {
                error_log("BIND PARAMS FAILED");
                return false;
            }
            
            error_log("Parameters bound successfully");
            
            $executeResult = $stmt->execute();
            
            if (!$executeResult) {
                error_log("EXECUTE FAILED: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            
            error_log("Execute successful");
            
            $lastId = $connection->lastInsertId();
            error_log("Last Insert ID: " . $lastId);
            
            $verifyStmt = $connection->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $verifyStmt->execute(['id' => $lastId]);
            $insertedRow = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            error_log("Inserted row verification: " . print_r($insertedRow, true));
            
            error_log("=== TRANSACTION CREATE SUCCESS ===");
            return $lastId;
            
        } catch (PDOException $e) {
            error_log("=== TRANSACTION CREATE FAILED ===");
            error_log("PDO Exception: " . $e->getMessage());
            error_log("Error Code: " . $e->getCode());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        } catch (Exception $e) {
            error_log("=== TRANSACTION CREATE FAILED ===");
            error_log("General Exception: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function getAllTransactions()
    {
        try {
            $connection = $this->db->connect();
            $sql = "SELECT t.*,
                        CONCAT(
                            COALESCE(c.first_name, ''), ' ',
                            COALESCE(NULLIF(c.middle_name, ''), ''), ' ',
                            COALESCE(c.last_name, '')
                        ) AS requester_fullname
                    FROM {$this->table} t
                    LEFT JOIN users u ON t.requested_by = u.username
                    LEFT JOIN constituents c ON u.constituent_id = c.id
                    ORDER BY t.id DESC";
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getAllTransactions error: " . $e->getMessage());
            return [];
        }
    }

    public function getTransactionsPaginated($limit, $offset)
    {
        try {
            $connection = $this->db->connect();
            $query = "SELECT t.*,
                        CONCAT(
                            COALESCE(c.first_name, ''), ' ',
                            COALESCE(NULLIF(c.middle_name, ''), ''), ' ',
                            COALESCE(c.last_name, '')
                        ) AS requester_fullname
                    FROM {$this->table} t
                    LEFT JOIN users u ON t.requested_by = u.username
                    LEFT JOIN constituents c ON u.constituent_id = c.id
                    ORDER BY t.id DESC LIMIT :limit OFFSET :offset";
            $stmt = $connection->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getTransactionsPaginated error: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalTransactionsCount()
    {
        try {
            $connection = $this->db->connect();
            $query = "SELECT COUNT(*) as count FROM {$this->table}";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $result['count'] ?? 0;
            error_log("getTotalTransactionsCount: " . $count);
            return $count;
        } catch (PDOException $e) {
            error_log("getTotalTransactionsCount error: " . $e->getMessage());
            return 0;
        }
    }

    public function createConstituentRequest(array $data)
    {
        try {
            $connection = $this->db->connect();
            $now = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s');

            $stmt = $connection->prepare(
                "INSERT INTO {$this->table}
                    (transaction, requested_by, generated_by, document_location, date_of_transaction, purpose, updated_at)
                VALUES
                    (:transaction, :requested_by, :generated_by, :document_location, :date_of_transaction, :purpose, :updated_at)"
            );

            return $stmt->execute([
                ':transaction'         => (string)($data['transaction'] ?? ''),
                ':requested_by'        => (string)($data['requested_by'] ?? ''),
                ':generated_by'        => 'PENDING',
                ':document_location'   => null,
                ':date_of_transaction' => $now,
                ':purpose'             => (string)($data['purpose'] ?? ''),
                ':updated_at'          => $now,
            ]);
        } catch (PDOException $e) {
            error_log('createConstituentRequest error: ' . $e->getMessage());
            return false;
        }
    }

    public function getRequestsByRequester(string $requestedBy, int $limit = 50, array $transactionTypes = [])
    {
        try {
            $connection = $this->db->connect();
            $params = [':requested_by' => $requestedBy];

            $sql = "SELECT * FROM {$this->table} WHERE requested_by = :requested_by";

            if (!empty($transactionTypes)) {
                $typePlaceholders = [];
                foreach ($transactionTypes as $index => $typeName) {
                    $placeholder = ':type_' . $index;
                    $typePlaceholders[] = $placeholder;
                    $params[$placeholder] = $typeName;
                }
                $sql .= ' AND transaction IN (' . implode(', ', $typePlaceholders) . ')';
            }

            $sql .= ' ORDER BY id DESC LIMIT :limit';
            $stmt = $connection->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('getRequestsByRequester error: ' . $e->getMessage());
            return [];
        }
    }

    public function getPendingConstituentRequests(array $documentTypes): array
    {
        if (empty($documentTypes)) {
            return [];
        }

        try {
            $connection = $this->db->connect();

            $placeholders = [];
            $params = [];
            foreach ($documentTypes as $index => $type) {
                $placeholder = ':type_' . $index;
                $placeholders[] = $placeholder;
                $params[$placeholder] = $type;
            }

            $sql = "SELECT * FROM {$this->table}
                    WHERE transaction IN (" . implode(', ', $placeholders) . ")
                    AND UPPER(generated_by) = 'PENDING'
                    ORDER BY date_of_transaction DESC";

            $stmt = $connection->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log('getPendingConstituentRequests error: ' . $e->getMessage());
            return [];
        }
    }

    public function getById(int $id): ?array
    {
        try {
            $connection = $this->db->connect();
            $stmt = $connection->prepare(
                "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1"
            );
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log('getById error: ' . $e->getMessage());
            return null;
        }
    }

    // FIX: Now sets updated_at in Manila time so the constituent's
    // "Processed" timestamp displays correctly in my_requests.php
    public function markProcessed(int $id, string $generatedBy, string $documentLocation): bool
    {
        try {
            $connection = $this->db->connect();
            $now = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s');

            $stmt = $connection->prepare(
                "UPDATE {$this->table}
                SET generated_by      = :generated_by,
                    document_location = :document_location,
                    updated_at        = :now
                WHERE id = :id"
            );
            return $stmt->execute([
                ':generated_by'      => $generatedBy,
                ':document_location' => $documentLocation,
                ':now'               => $now,
                ':id'                => $id,
            ]);
        } catch (PDOException $e) {
            error_log('markProcessed error: ' . $e->getMessage());
            return false;
        }
    }

    // FIX: Now sets updated_at in Manila time so the constituent's
    // "Rejected" timestamp displays correctly in my_requests.php
    public function rejectConstituentRequest(int $id, string $reason): bool
    {
        try {
            $connection = $this->db->connect();
            $now = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s');

            $stmt = $connection->prepare(
                "UPDATE {$this->table}
                SET generated_by      = 'REJECTED',
                    document_location = :reason,
                    updated_at        = :now
                WHERE id = :id"
            );
            return $stmt->execute([
                ':reason' => $reason,
                ':now'    => $now,
                ':id'     => $id,
            ]);
        } catch (PDOException $e) {
            error_log('rejectConstituentRequest error: ' . $e->getMessage());
            return false;
        }
    }
}