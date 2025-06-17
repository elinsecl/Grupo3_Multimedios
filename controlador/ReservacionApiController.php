<?php

require_once __DIR__.'/../accesoDatos/ReservacionDAO.php';
require_once __DIR__.'/../modelo/Reservacion.php';

class ReservacionApiController {

    private $dao;

    public function __construct(){
        $this->dao = new ReservacionDAO();
    }

    public function manejarRequest(){
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_reservacion'] ?? null;

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
            $res = $this->dao->obtenerPorId($id);
            echo $res ? json_encode($res) :
                json_encode(["mensaje" => "Reservación no encontrada"]);
        } else {
            echo json_encode($this->dao->obtenerDatos());
        }
    }

    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['cliente_id'], $datos['mesa_id'], $datos['fecha_reserva'], $datos['hora_reserva'], $datos['cantidad_personas'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos"]);
            return;
        }

        $reservacion = new Reservacion(
            null,
            $datos['cliente_id'],
            $datos['mesa_id'],
            $datos['fecha_reserva'],
            $datos['hora_reserva'],
            $datos['cantidad_personas'],
            $datos['estado'] ?? 'activa'
        );

        if ($this->dao->insertar($reservacion)) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Reservación creada"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear la reservación"]);
        }
    }

    private function handlePutRequest(?int $id){
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID requerido"]);
            return;
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        $reservacion = new Reservacion(
            $id,
            $datos['cliente_id'],
            $datos['mesa_id'],
            $datos['fecha_reserva'],
            $datos['hora_reserva'],
            $datos['cantidad_personas'],
            $datos['estado'] ?? 'activa'
        );

        if ($this->dao->actualizar($reservacion)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Reservación actualizada"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar la reservación"]);
        }
    }

    private function handleDeleteRequest(?int $id){
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID requerido para eliminar"]);
            return;
        }

        if ($this->dao->eliminar($id)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Reservación eliminada"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar la reservación"]);
        }
    }
}
?>
