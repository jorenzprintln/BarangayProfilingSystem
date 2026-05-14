-- ─────────────────────────────────────────────────────────────────────────────
-- Migration: vehicle_requests
-- Run this once against your database.
-- ─────────────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS vehicle_requests (
    id                    INT UNSIGNED     NOT NULL AUTO_INCREMENT,

    -- who submitted it
    user_id               INT UNSIGNED     NOT NULL,           -- FK → users.id
    constituent_id        INT UNSIGNED     DEFAULT NULL,       -- FK → constituents.id (nullable until linked)

    -- vehicle fields (mirrors the vehicles table)
    plate_number          VARCHAR(20)      DEFAULT NULL,
    or_number             VARCHAR(30)      DEFAULT NULL,
    cr_number             VARCHAR(30)      DEFAULT NULL,
    vehicle_type          VARCHAR(40)      DEFAULT NULL,
    vehicle_use           ENUM('Private','Public') NOT NULL DEFAULT 'Private',
    make                  VARCHAR(60)      NOT NULL,
    model                 VARCHAR(60)      DEFAULT NULL,
    year                  SMALLINT UNSIGNED DEFAULT NULL,
    color                 VARCHAR(40)      DEFAULT NULL,
    fuel_type             VARCHAR(40)      DEFAULT NULL,
    transmission          VARCHAR(30)      DEFAULT NULL,
    engine_number         VARCHAR(60)      DEFAULT NULL,
    chassis_number        VARCHAR(60)      DEFAULT NULL,
    notes                 TEXT             DEFAULT NULL,

    -- workflow
    status                ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    secretary_note        TEXT             DEFAULT NULL,   -- optional rejection/approval note
    reviewed_by           INT UNSIGNED     DEFAULT NULL,  -- FK → users.id (the secretary)
    reviewed_at           DATETIME         DEFAULT NULL,

    -- constituent notification (mirrors constituent_profile_requests.seen_at pattern)
    seen_at               DATETIME         DEFAULT NULL,  -- NULL = constituent hasn't seen the result yet

    created_at            DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY idx_user_id        (user_id),
    KEY idx_constituent_id (constituent_id),
    KEY idx_status         (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;