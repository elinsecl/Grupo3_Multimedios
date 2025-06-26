<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once __DIR__.'/../accesoDatos/PedidoDao.php';
require_once __DIR__.'/../modelo/Pedido.php';

class PedidoApiController {
    private $dao;

    public function __construct() {
        $this->dao = new PedidoDao();
    }

    public function manejarRequest() {
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_pedido'] ?? null;

        if ($metodo == 'OPTIONS') {
            http_response_code(200);
            exit();
        }

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
        }
    }

    private function handleGetRequest($id) {
        if ($id) {
            $pedido = $this->dao->obtenerPorId($id);
            if ($pedido) {
                echo json_encode($pedido->toPublicArray());
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "Pedido no encontrado"]);
            }
        } else {
            $pedidos = $this->dao->obtenerDatos();
            echo json_encode(array_map(fn($p) => $p->toPublicArray(), $pedidos));
        }
    }

    private function handlePostRequest() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_usuario'], $data['estado'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan datos: id_usuario y estado"]);
            return;
        }

        $fecha = $data['fecha_pedido'] ?? date('Y-m-d H:i:s');

        $pedido = new Pedido(null, $data['id_usuario'], $fecha, $data['estado']);

        if ($this->dao->insertar($pedido)) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Pedido creado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear pedido"]);
        }
    }

    private function handlePutRequest($id) {
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Falta el id_pedido en la URL"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $pedidoExistente = $this->dao->obtenerPorId($id);

        if (!$pedidoExistente) {
            http_response_code(404);
            echo json_encode(["mensaje" => "Pedido no encontrado"]);
            return;
        }

        $pedido = new Pedido(
            $id,
            $data['id_usuario'] ?? $pedidoExistente->id_usuario,
            $data['fecha_pedido'] ?? $pedidoExistente->fecha_pedido,
            $data['estado'] ?? $pedidoExistente->estado
        );

        if ($this->dao->actualizar($pedido)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Pedido actualizado"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar pedido"]);
        }
    }

    private function handleDeleteRequest($id) {
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Falta el id_pedido en la URL"]);
            return;
        }

        if ($this->dao->eliminar($id)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Pedido eliminado"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "No se pudo eliminar el pedido"]);
        }
    }
}

$controlador = new PedidoApiController();
$controlador->manejarRequest();

