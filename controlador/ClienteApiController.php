<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");


require_once __DIR__.'/../accesoDatos/ClienteDAO.php';
require_once __DIR__.'/../modelo/Cliente.php'; 

class ClienteApiController {

    private $dao;

    public function __construct(){
        $this->dao = new ClienteDAO();
    }

    public function manejarRequest(){

        // ✅ Manejar preflight request (CORS)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_cliente'] ?? null;

        header('Content-Type: application/json');

        switch ($metodo) {
            case 'GET':
                $this->handleGetRequest($id);
                break;
            case 'POST':
                $this->handlePostRequest();
                break;
            case 'PUT':
                $this->handlePutRequest($id);
                break;
            case 'DELETE':
                $this->handleDeleteRequest($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(["mensaje" => "Método no permitido"]);
                break;
        }
    }

    private function handleGetRequest(?int $id){
        if ($id) {
            $cliente = $this->dao->obtenerPorId($id);
            if ($cliente) {
                echo json_encode($cliente);
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "Cliente no encontrado"]);
            }
        } else {
            $clientes = $this->dao->obtenerDatos();
            echo json_encode($clientes);
        }
    }

    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['nombre'], $datos['correo'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos para crear cliente. Se requieren nombre y correo."]);
            return;
        }

        $cliente = new Cliente(
            null,
            $datos['nombre'],
            $datos['telefono'] ?? null,
            $datos['correo'],
            $datos['direccion'] ?? null,
            $datos['fecha_registro'] ?? date('Y-m-d H:i:s'),
            $datos['estado'] ?? 'activo'
        );

        try {
            if ($this->dao->insertar($cliente)) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Cliente creado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear el cliente"]);
        }
        } catch (PDOException $e) {
            // Manejo de errores BD
            if ($e->getCode() == 23000) {
                // Violación de restricci
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    http_response_code(409);
                    echo json_encode([
                        "mensaje" => "Ya existe un cliente con ese nombre.",
                        "error" => $e->getMessage()
                    ]);
                } else {
                    http_response_code(409);
                    echo json_encode([
                        "mensaje" => "Error de integridad de datos al crear el cliente.",
                        "error" => $e->getMessage()
                    ]);
                }
            } else {
                http_response_code(500);
                echo json_encode([
                    "mensaje" => "Error inesperado al crear el cliente.",
                    "error" => $e->getMessage()
                ]);
            }
        }
        exit();
    }

    private function handlePutRequest(?int $id){
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de cliente necesario para actualizar"]);
            return;
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['nombre'], $datos['correo'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos para actualizar cliente"]);
            return;
        }

        $cliente = new Cliente(
            $id,
            $datos['nombre'],
            $datos['telefono'] ?? null,
            $datos['correo'],
            $datos['direccion'] ?? null,
            $datos['fecha_registro'] ?? date('Y-m-d H:i:s'),
            $datos['estado'] ?? 'activo'
        );

        if ($this->dao->actualizar($cliente)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Cliente actualizado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar el cliente"]);
        }
    }

    private function handleDeleteRequest(?int $id){
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de cliente necesario para eliminar"]);
            return;
        }

            try {
             if ($this->dao->eliminar($id)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Cliente eliminado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar el cliente"]);
        }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                // Violación de clave 
                http_response_code(409);
                echo json_encode([
                    "mensaje" => "No se puede eliminar el cliente porque está relacionado con otros registros.",
                    "error" => $e->getMessage()
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "mensaje" => "Error inesperado al eliminar el cliente.",
                    "error" => $e->getMessage()
                ]);
            }
        }
        exit();
    }
}

