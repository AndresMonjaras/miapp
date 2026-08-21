CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert de un usuario de prueba (contraseña: 1234, hash generado con password_hash)
INSERT INTO usuarios (nombre, email, password) VALUES
('Admin', 'admin@miapp.local', '$2y$12$abcdefghijklmnopqrstuvwxyz...'); 
