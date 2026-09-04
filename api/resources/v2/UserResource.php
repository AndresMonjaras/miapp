<?php
/**
 * V2 UserResource — Protected by Bearer token authentication.
 * Routes: /api/v2/users
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/v1/User.php';
require_once __DIR__ . '/../../core/V2_AuthFilter.php';

class V2_UserResource
{
    private $db;
    private $user;

    public function __construct()
    {
        $database   = new Database();
        $this->db   = $database->getConnection();
        $this->user = new V1_User($this->db); // same users table as V1
    }

    // GET /api/v2/users  (protected)
    public function index()
    {
        header("Content-Type: application/json");
        V2_AuthFilter::handle($this->db);

        $stmt = $this->user->read();
        $num  = $stmt->rowCount();

        if ($num > 0) {
            $users_arr            = [];
            $users_arr["records"] = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                array_push($users_arr["records"], [
                    "id"         => $id,
                    "name"       => $name,
                    "email"      => $email,
                    "created_at" => $created_at
                ]);
            }
            http_response_code(200);
            echo json_encode($users_arr);
        } else {
            http_response_code(200);
            echo json_encode(["records" => []]);
        }
    }

    // GET /api/v2/users/{id}  (protected)
    public function show($id)
    {
        header("Content-Type: application/json");
        V2_AuthFilter::handle($this->db);

        $this->user->id = $id;

        if ($this->user->readOne()) {
            http_response_code(200);
            echo json_encode([
                "id"         => $this->user->id,
                "name"       => $this->user->name,
                "email"      => $this->user->email,
                "created_at" => $this->user->created_at
            ]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Usuario no encontrado"]);
        }
    }

    // POST /api/v2/users  (protected)
    public function store()
    {
        header("Content-Type: application/json");
        V2_AuthFilter::handle($this->db);

        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->name) && !empty($data->email)) {
            $this->user->name  = $data->name;
            $this->user->email = $data->email;

            if ($this->user->create()) {
                http_response_code(201);
                echo json_encode([
                    "message" => "Usuario creado exitosamente",
                    "id"      => $this->user->id
                ]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "No se pudo crear el usuario"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    // PUT /api/v2/users/{id}  (protected)
    public function update($id)
    {
        header("Content-Type: application/json");
        V2_AuthFilter::handle($this->db);

        $data = json_decode(file_get_contents("php://input"));
        $this->user->id = $id;

        if (!empty($data->name) && !empty($data->email)) {
            $this->user->name  = $data->name;
            $this->user->email = $data->email;

            if ($this->user->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Usuario actualizado exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "No se pudo actualizar el usuario"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    // DELETE /api/v2/users/{id}  (protected)
    public function destroy($id)
    {
        header("Content-Type: application/json");
        V2_AuthFilter::handle($this->db);

        $this->user->id = $id;

        if ($this->user->delete()) {
            http_response_code(200);
            echo json_encode(["message" => "Usuario eliminado exitosamente"]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "No se pudo eliminar el usuario"]);
        }
    }
}
?>
