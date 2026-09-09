<?php
require_once __DIR__ . '/../config/database.php';

class Tarea {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function findAll() {
        $stmt = $this->db->query('SELECT * FROM tareas ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare('SELECT * FROM tareas WHERE id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function create($data) {
        $stmt = $this->db->prepare('INSERT INTO tareas (titulo, completada, fecha_creacion) VALUES (?, ?, NOW())');
        $stmt->execute([$data['titulo'], $data['completada'] ? 1 : 0]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare('UPDATE tareas SET titulo = ?, completada = ? WHERE id = ?');
        return $stmt->execute([$data['titulo'], $data['completada'] ? 1 : 0, $id]);
    }
}
