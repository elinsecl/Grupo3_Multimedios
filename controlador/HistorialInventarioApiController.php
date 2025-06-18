<?php

require_once __DIR__.'/../accesoDatos/HistorialInventarioDAO.php';
require_once __DIR__.'/../modelo/HistorialInventario.php'; 

class HistorialInventarioApiController {

    private $dao;

    public function __construct(){
        $this->dao = new HistorialInventarioDAO();
    }

    public function manejarRequest(){
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_inventario'] ?? null;

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
            $historial_inventario = $this->dao->obtenerPorId($id);
            if ($historial_inventario) {
                echo json_encode($historial_inventario);
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "Historial del inventario no encontrada"]);
            }
        } else {
            $historial_inventario = $this->dao->obtenerDatos();
            echo json_encode($historial_inventario);
        }
    }

    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['id_inventario'], $datos['ingrediente_id'],$datos['cambio_stock'],$datos['fecha'],$datos['tipo_cambio'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos para crear el historial del inventario"]);
            return;
        }

        $historial_pedido = new HistorialPedido(
            null,
            $datos['id_inventario'],
            $datos['ingrediente_id'],
            $datos['cambio_stock'],
            $datos['fecha']
            $datos['tipo_cambio']
        );

        if ($this->dao->insertar($historial_pedido)) {
            http_response_code(201);
            echo json_encode(["mensaje" => "historial de inventario creado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear el historial del inventario"]);
        }
    }

    private function handleDeleteRequest(?int $id){
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID del historial del inventario necesario para eliminar"]);
            return;
        }

        if ($this->dao->eliminar($id)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "historial del inventario eliminada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar historial del inventario"]);
        }   

    }

}

?>