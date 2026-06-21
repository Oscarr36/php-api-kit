-- Run this migration to create the users table.
-- mysql -u root -p your_database < database/migrations/001_create_users_table.sql

CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100)     NOT NULL,
    `email`      VARCHAR(191)     NOT NULL,
    `password`   VARCHAR(255)     NOT NULL,
    `role`       ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME         NULL     ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`),
    INDEX `idx_users_role` (`role`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- Optional: seed an admin user (password: Admin1234!)
-- INSERT INTO `users` (`name`, `email`, `password`, `role`)
-- VALUES ('Admin', 'admin@example.com', '$2y$12$...', 'admin');
