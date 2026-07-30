-- Tablas de autenticación para Moya Ortodoncia
-- Ejecutar en la base de datos iedeocci_adm

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS user_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token VARCHAR(64) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  INDEX idx_token (token),
  INDEX idx_user_expires (user_id, expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuario admin: password = 'admin'
-- Generado con: password_hash('admin', PASSWORD_DEFAULT)
INSERT INTO usuarios (usuario, password) VALUES
  ('admin', '$2y$10$KCwLAhNItEfese1AZZhTg.rsLiJxsGqsmtI4rIPj6aSPSPP134yQa');
