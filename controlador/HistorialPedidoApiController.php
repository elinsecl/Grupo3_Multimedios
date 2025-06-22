<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once __DIR__.'/../accesoDatos/HistorialPedidoDAO.php';
require_once __DIR__.'/../modelo/HistorialPedido.php'; 

class HistorialPedidoApiController {

    
    private $dao;
    private $dao2;

    public function __construct(){
        $this->dao = new HistorialPedidoDAO();
        $this->dao2 = new HistorialPedidoDAO();
    }


    public function manejarRequest(){
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['pedido_id'] ?? null;
        $historial_pedido = $_GET['id_historial_pedido'] ?? null;

        header('Content-Type: application/json');

        switch ($metodo) {
            case 'GET':
                $this->handleGetRequest($id, $historial_pedido);
                break;
            case 'POST':
                $this->handlePostRequest();
                break;
            case 'DELETE':
                $this->handleDeleteRequest($id);
                break;
            case 'PUT':
                $this->handlePutRequest($historial_pedido);
                break;
            default:
                http_response_code(405);
                echo json_encode(["mensaje" => "Método no permitido"]);
                break;
        }
    }


    private function handleGetRequest(?int $id = null, ?int $id_historial = null){
        if ($id_historial) {
            $historial = $this->dao->obtenerPorIdHistorial($id_historial);
            if ($historial) {
                echo json_encode($historial);
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "Historial con ese ID no encontrado"]);
            }
        } elseif ($id) {
            $historial = $this->dao->obtenerPorPedidoId($id);
            if ($historial) {
                echo json_encode($historial);
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "Historial del pedido no encontrado"]);
            }
        } else {
            $historiales = $this->dao->obtenerDatos();
            echo json_encode($historiales);
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

    private function handlePutRequest(?int $historial_pedido){
        if (!$historial_pedido) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Se requiere el ID del pedido para actualizar el estado de entrega"]);
            return;
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['estado_entrega'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Falta el campo estado_entrega"]);
            return;
        }

        $nuevoEstado = $datos['estado_entrega'];

        if ($this->dao->actualizarEstadoEntrega($historial_pedido, $nuevoEstado)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Estado de entrega actualizado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar el estado de entrega"]);
        }
    }

}

?>