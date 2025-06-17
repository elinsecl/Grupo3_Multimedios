<?php

require_once __DIR__.'/../accesoDatos/PedidoDAO.php';
require_once __DIR__.'/../modelo/Pedido.php';

class PedidoApiController {

    private $dao;

    public function __construct(){
        $this->dao = new PedidoDAO();
    }

    public function manejarRequest(){
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_pedido'] ?? null;

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
            $pedido = $this->dao->obtenerPorId($id);
            if ($pedido) {
                echo json_encode($pedido);
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "Pedido no encontrado"]);
            }
        } else {
            $pedidos = $this->dao->obtenerDatos();
            echo json_encode($pedidos);
        }
    }

    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['cliente_id'], $datos['mesa_id'], $datos['hora_pedido'], $datos['total'], $datos['metodo_pago'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos para crear pedido."]);
            return;
        }

        $pedido = new Pedido(
            null,
            $datos['cliente_id'],
            $datos['mesa_id'],
            $datos['fecha_pedido'] ?? date('Y-m-d H:i:s'),
            $datos['hora_pedido'],
            $datos['total'],
            $datos['estado'] ?? 'pendiente',
            $datos['metodo_pago']
        );

        if ($this->dao->insertar($pedido)) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Pedido creado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear el pedido"]);
        }
    }

    private function handlePutRequest(?int $id){
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de pedido necesario para actualizar"]);
            return;
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['cliente_id'], $datos['mesa_id'], $datos['hora_pedido'], $datos['total'], $datos['metodo_pago'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos para actualizar pedido"]);
            return;
        }

        $pedido = new Pedido(
            $id,
            $datos['cliente_id'],
            $datos['mesa_id'],
            $datos['fecha_pedido'] ?? date('Y-m-d H:i:s'),
            $datos['hora_pedido'],
            $datos['total'],
            $datos['estado'] ?? 'pendiente',
            $datos['metodo_pago']
        );

        if ($this->dao->actualizar($pedido)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Pedido actualizado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar el pedido"]);
        }
    }

    private function handleDeleteRequest(?int $id){
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de pedido necesario para eliminar"]);
            return;
        }

        if ($this->dao->eliminar($id)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Pedido eliminado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar el pedido"]);
        }
    }
}
