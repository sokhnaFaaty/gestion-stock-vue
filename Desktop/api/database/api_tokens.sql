-- =============================================================
-- API Bibliothèque — table des tokens d'accès
-- A exécuter sur la base 'bibliotheque' (déjà créée par le projet web).
-- =============================================================

USE bibliotheque;

CREATE TABLE IF NOT EXISTS api_tokens (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_api_tokens_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE = InnoDB;
