CREATE DATABASE IF NOT EXISTS short_url
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE short_url;

CREATE TABLE IF NOT EXISTS urls (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    short_code VARCHAR(8) NOT NULL,
    original_url VARCHAR(2048) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY urls_short_code_unique (short_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
