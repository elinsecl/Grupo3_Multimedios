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
        try {
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
                $reservacionesArray = array_map(fn($r) => $r->toPublicArray(), $reservaciones);
                echo json_encode($reservacionesArray);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "mensaje" => "Error al obtener las reservaciones.",
                "error" => $e->getMessage()
            ]);
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

        try {
            if ($this->dao->insertar($reservacion)) {
                http_response_code(201);
                echo json_encode(["mensaje" => "Reservación creada exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "Error al crear la reservación"]);
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                http_response_code(409);
                echo json_encode([
                    "mensaje" => "Error de integridad de datos al crear la reservación.",
                    "error" => $e->getMessage()
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "mensaje" => "Error inesperado al crear la reservación.",
                    "error" => $e->getMessage()
                ]);
            }
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

        try {
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
                $datos['estado'] ?? $reservacionExistente->estado
            );

            if ($this->dao->actualizar($reservacion)) {
                http_response_code(200);
                echo json_encode(["mensaje" => "Reservación actualizada exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "Error al actualizar la reservación"]);
            }

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                http_response_code(409);
                echo json_encode([
                    "mensaje" => "Error de integridad de datos al actualizar la reservación.",
                    "error" => $e->getMessage()
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "mensaje" => "Error inesperado al actualizar la reservación.",
                    "error" => $e->getMessage()
                ]);
            }
        }
        exit();
    }

    private function handleDeleteRequest(?int $id_reservacion){
        if (!$id_reservacion) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de reservación necesario para eliminar"]);
            exit();
        }

        try {
            if ($this->dao->eliminar($id_reservacion)) {
                http_response_code(200);
                echo json_encode(["mensaje" => "Reservación eliminada exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "Error al eliminar la reservación"]);
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                http_response_code(409);
                echo json_encode([
                    "mensaje" => "No se puede eliminar la reservación porque está relacionada con otros registros.",
                    "error" => $e->getMessage()
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "mensaje" => "Error inesperado al eliminar la reservación.",
                    "error" => $e->getMessage()
                ]);
            }
        }
        exit();
    }
}

$controlador = new ReservacionApiController();
$controlador->manejarRequest();
