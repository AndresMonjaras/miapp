<?php
require_once __DIR__ . '/../models/Tarea.php';

class TareaResource {
    private $model;

    public function __construct() {
        $this->model = new Tarea();
    }

    private function sendJSON($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function getJSONBody() {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }

    public function list() {
        $tasks = $this->model->findAll();
        $this->sendJSON($tasks);
    }

    public function show($id) {
        $task = $this->model->findById($id);
        if (!$task) {
            $this->sendJSON(['error' => 'Task not found'], 404);
        }
        $this->sendJSON($task);
    }

    public function store() {
        $data = $this->getJSONBody();
        if (!isset($data['titulo']) || !isset($data['completada'])) {
            $this->sendJSON(['error' => 'Missing titulo or completada'], 400);
        }
        if (!is_string($data['titulo']) || !is_bool($data['completada'])) {
            $this->sendJSON(['error' => 'Invalid types: titulo must be string, completada must be boolean'], 400);
        }
        $id = $this->model->create($data);
        $task = $this->model->findById($id);
        $this->sendJSON($task, 201);
    }

    public function update($id) {
        $data = $this->getJSONBody();
        if (!isset($data['titulo']) || !isset($data['completada'])) {
            $this->sendJSON(['error' => 'Missing titulo or completada'], 400);
        }
        if (!is_string($data['titulo']) || !is_bool($data['completada'])) {
            $this->sendJSON(['error' => 'Invalid types: titulo must be string, completada must be boolean'], 400);
        }
        $existing = $this->model->findById($id);
        if (!$existing) {
            $this->sendJSON(['error' => 'Task not found'], 404);
        }
        $this->model->update($id, $data);
        $task = $this->model->findById($id);
        $this->sendJSON($task);
    }
}
