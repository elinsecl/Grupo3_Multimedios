<?php

require_once __DIR__.'/../accesoDatos/HistorialPedidoDAO.php';
require_once __DIR__.'/../modelo/HistorialPedido.php'; 

class HistorialPedidoApiController {

    private $dao;

    public function __construct(){
        $this->dao = new HistorialPedidoDAO();
    }


    public function manejarRequest(){
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['pedido_id'] ?? null;

        header('Content-Type: application/json');

        switch ($metodo) {
            case 'GET':
                $this->handleGetRequest($id);
                break;
            case 'POST':
                $this->handlePostRequest();
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
            $historial_pedido = $this->dao->obtenerPorId($id);
            if ($historial_pedido) {
                echo json_encode($historial_pedido);
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "Historial del pedido no encontrada"]);
            }
        } else {
            $historial_pedido = $this->dao->obtenerDatos();
            echo json_encode($historial_pedido);
        }
    }

    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['pedido_id'], $datos['fecha_entrega'],$datos['estado_entrega'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos para crear el historial del pedido"]);
            return;
        }

        $historial_pedido = new HistorialPedido(
            null,
            $datos['pedido_id'],
            $datos['fecha_entrega'],
            $datos['estado_entrega']
        );

        if ($this->dao->insertar($historial_pedido)) {
            http_response_code(201);
            echo json_encode(["mensaje" => "historial del pedido creado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear el historial de pedido"]);
        }
    }

    private function handleDeleteRequest(?int $id){
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID del historial del pedido necesario para eliminar"]);
            return;
        }

        if ($this->dao->eliminar($id)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "historial del pedido eliminada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar historial del pedido"]);
        }
    }
}

?>