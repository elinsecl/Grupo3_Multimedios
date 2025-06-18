<?php
require_once __DIR__ . '/../accesoDatos/DetallePedidoDAO.php';
require_once __DIR__ . '/../modelo/DetallePedido.php';

class DetallePedidoApiController {

    private $dao;

    public function __construct() {
        $this->dao = new DetallePedidoDAO();
    }

    public function manejarRequest() {
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_detalle'] ?? null;
        $id_pedido = $_GET['id_pedido'] ?? null;

        header('Content-Type: application/json');

        switch ($metodo) {
            case 'GET':
                $this->handleGetRequest($id, $id_pedido);
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

    private function handleGetRequest($id, $id_pedido) {
        if ($id) {
            $detalle = $this->dao->obtenerPorId($id);
            if ($detalle) {
                echo json_encode($detalle);
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "Detalle no encontrado"]);
            }
        } elseif ($id_pedido) {
            $detalles = $this->dao->obtenerPorPedido($id_pedido);
            echo json_encode($detalles);
        } else {
            $detalles = $this->dao->obtenerTodos();
            echo json_encode($detalles);
        }
    }

    private function handlePostRequest() {
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['id_pedido'], $datos['id_platillo'], $datos['cantidad'], $datos['subtotal'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan datos para crear el detalle"]);
            return;
        }

        $detalle = new DetallePedido(
            null,
            $datos['id_pedido'],
            $datos['id_platillo'],
            $datos['cantidad'],
            $datos['subtotal']
        );

        if ($this->dao->insertar($detalle)) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Detalle creado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear el detalle"]);
        }
    }

    private function handlePutRequest($id) {
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID requerido para actualizar"]);
            return;
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['id_pedido'], $datos['id_platillo'], $datos['cantidad'], $datos['subtotal'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos"]);
            return;
        }

        $detalle = new DetallePedido(
            $id,
            $datos['id_pedido'],
            $datos['id_platillo'],
            $datos['cantidad'],
            $datos['subtotal']
        );

        if ($this->dao->actualizar($detalle)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Detalle actualizado"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar"]);
        }
    }

    private function handleDeleteRequest($id) {
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID requerido para eliminar"]);
            return;
        }

        if ($this->dao->eliminar($id)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Detalle eliminado"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar"]);
        }
    }
}
