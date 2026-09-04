<?php
/**
 * V2 ProductoResource — Protected by Bearer token authentication.
 * Routes: /api/v2/productos
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/v2/Producto.php';
require_once __DIR__ . '/../../core/V2_AuthFilter.php';

class V2_ProductoResource
{
    private $db;
    private $producto;

    public function __construct()
    {
        $database       = new Database();
        $this->db       = $database->getConnection();
        $this->producto = new V2_Producto($this->db);
    }

    // GET /api/v2/productos  (protected)
    public function index()
    {
        header("Content-Type: application/json");
        V2_AuthFilter::handle($this->db);

        $stmt = $this->producto->read();
        $num  = $stmt->rowCount();

        if ($num > 0) {
            $productos_arr            = [];
            $productos_arr["records"] = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                array_push($productos_arr["records"], [
                    "id"          => $id,
                    "sku"         => $sku,
                    "name"        => $name,
                    "description" => $description,
                    "price"       => $price,
                    "stock"       => $stock,
                    "created_at"  => $created_at,
                    "updated_at"  => $updated_at
                ]);
            }
            http_response_code(200);
            echo json_encode($productos_arr);
        } else {
            http_response_code(200);
            echo json_encode(["records" => []]);
        }
    }

    // GET /api/v2/productos/{id}  (protected)
    public function show($id)
    {
        header("Content-Type: application/json");
        V2_AuthFilter::handle($this->db);

        $this->producto->id = $id;

        if ($this->producto->readOne()) {
            http_response_code(200);
            echo json_encode([
                "id"          => $this->producto->id,
                "sku"         => $this->producto->sku,
                "name"        => $this->producto->name,
                "description" => $this->producto->description,
                "price"       => $this->producto->price,
                "stock"       => $this->producto->stock,
                "created_at"  => $this->producto->created_at,
                "updated_at"  => $this->producto->updated_at
            ]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Producto no encontrado"]);
        }
    }

    // POST /api/v2/productos  (protected)
    public function store()
    {
        header("Content-Type: application/json");
        V2_AuthFilter::handle($this->db);

        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->sku) && !empty($data->name) && isset($data->price) && isset($data->stock)) {
            $this->producto->sku         = $data->sku;
            $this->producto->name        = $data->name;
            $this->producto->description = isset($data->description) ? $data->description : null;
            $this->producto->price       = $data->price;
            $this->producto->stock       = $data->stock;

            if ($this->producto->create()) {
                http_response_code(201);
                echo json_encode([
                    "message" => "Producto creado exitosamente",
                    "id"      => $this->producto->id
                ]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "No se pudo crear el producto"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    // PUT /api/v2/productos/{id}  (protected)
    public function update($id)
    {
        header("Content-Type: application/json");
        V2_AuthFilter::handle($this->db);

        $data = json_decode(file_get_contents("php://input"));
        $this->producto->id = $id;

        if (!empty($data->sku) && !empty($data->name) && isset($data->price) && isset($data->stock)) {
            $this->producto->sku         = $data->sku;
            $this->producto->name        = $data->name;
            $this->producto->description = isset($data->description) ? $data->description : null;
            $this->producto->price       = $data->price;
            $this->producto->stock       = $data->stock;

            if ($this->producto->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Producto actualizado exitosamente"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "No se pudo actualizar el producto"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Datos incompletos"]);
        }
    }

    // DELETE /api/v2/productos/{id}  (protected)
    public function destroy($id)
    {
        header("Content-Type: application/json");
        V2_AuthFilter::handle($this->db);

        $this->producto->id = $id;

        if ($this->producto->delete()) {
            http_response_code(200);
            echo json_encode(["message" => "Producto eliminado exitosamente"]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "No se pudo eliminar el producto"]);
        }
    }
}
?>
