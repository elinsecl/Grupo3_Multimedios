<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once __DIR__.'/../accesoDatos/MesaDAO.php';
require_once __DIR__.'/../modelo/Mesa.php'; 

class MesaApiController {

    private $dao;

    public function __construct(){
        $this->dao = new MesaDAO();
    }

    public function manejarRequest(){

            // ✅ Manejar preflight request (CORS)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_mesa'] ?? null;

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
            $mesa = $this->dao->obtenerPorId($id);
            if ($mesa) {
                echo json_encode($mesa);
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "Mesa no encontrada"]);
            }
        } else {
            $mesas = $this->dao->obtenerDatos();
            echo json_encode($mesas);
        }
    }

    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['numero_mesa'], $datos['capacidad'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos para crear mesa. Se requieren numero_mesa y capacidad."]);
            return;
        }

        $mesa = new Mesa(
            null,
            $datos['numero_mesa'],
            $datos['capacidad'],
            $datos['ubicacion'] ?? null,
            $datos['estado'] ?? 'disponible'
        );

        if ($this->dao->insertar($mesa)) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Mesa creada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear la mesa"]);
        }
    }

    private function handlePutRequest(?int $id){
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de mesa necesario para actualizar"]);
            return;
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['numero_mesa'], $datos['capacidad'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos para actualizar mesa"]);
            return;
        }

        $mesa = new Mesa(
            $id,
            $datos['numero_mesa'],
            $datos['capacidad'],
            $datos['ubicacion'] ?? null,
            $datos['estado'] ?? 'disponible'
        );

        if ($this->dao->actualizar($mesa)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Mesa actualizada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar la mesa"]);
        }
    }

    private function handleDeleteRequest(?int $id){
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de mesa necesario para eliminar"]);
            return;
        }

        if ($this->dao->eliminar($id)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Mesa eliminada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar la mesa"]);
        }
    }
}
