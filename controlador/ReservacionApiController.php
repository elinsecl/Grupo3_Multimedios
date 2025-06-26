<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

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

        // Manejo de la solicitud OPTIONS para el "preflight" de CORS
        if ($metodo === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

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
                exit();
        }
    }

    private function handleGetRequest(?int $id_reservacion){
        if ($id_reservacion) {
            $reservacion = $this->dao->obtenerPorId($id_reservacion);
            if ($reservacion) {
                echo json_encode($reservacion->toPublicArray());
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "Reservación no encontrada"]);
            }
        } else {
            $reservaciones = $this->dao->obtenerDatos();
            $reservacionesArray = array_map(function($reservacion) {
                return $reservacion->toPublicArray();
            }, $reservaciones);
            
            echo json_encode($reservacionesArray);
        }
        exit();
    }

    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['cliente_id'], $datos['mesa_id'], $datos['fecha_reserva'], $datos['hora_reserva'], $datos['cantidad_personas'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos. Se requieren cliente_id, mesa_id, fecha_reserva, hora_reserva y cantidad_personas"]);
            exit();
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
            echo json_encode(["mensaje" => "Reservación creada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear la reservación"]);
        }
        exit();
    }

    private function handlePutRequest(?int $id_reservacion){
        if (!$id_reservacion) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de reservación necesario para actualizar"]);
            exit();
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['cliente_id'], $datos['mesa_id'], $datos['fecha_reserva'], $datos['hora_reserva'], $datos['cantidad_personas'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos para actualizar reservación"]);
            exit();
        }

        $reservacionExistente = $this->dao->obtenerPorId($id_reservacion);
        if (!$reservacionExistente) {
            http_response_code(404);
            echo json_encode(["mensaje" => "Reservación a actualizar no encontrada"]);
            exit();
        }

        $reservacion = new Reservacion(
            $id_reservacion,
            $datos['cliente_id'],
            $datos['mesa_id'],
            $datos['fecha_reserva'],
            $datos['hora_reserva'],
            $datos['cantidad_personas'],
            $datos['estado'] ?? $reservacionExistente->estado // Usa el estado enviado o el actual
        );

        if ($this->dao->actualizar($reservacion)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Reservación actualizada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar la reservación"]);
        }
        exit();
    }

    private function handleDeleteRequest(?int $id_reservacion){
        if (!$id_reservacion) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de reservación necesario para eliminar"]);
            exit();
        }

        if ($this->dao->eliminar($id_reservacion)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Reservación eliminada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar la reservación"]);
        }
        exit();
    }
}

// Instanciar y manejar la solicitud
$controlador = new ReservacionApiController();
$controlador->manejarRequest();