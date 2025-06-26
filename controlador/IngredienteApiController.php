<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once __DIR__.'/../accesoDatos/IngredienteDAO.php';
require_once __DIR__.'/../modelo/Ingrediente.php';

class IngredienteApiController {

    private $dao;

    public function __construct(){
        $this->dao = new IngredienteDAO();
    }

    public function manejarRequest(){
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_ingrediente'] ?? null;

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

    private function handleGetRequest(?int $id_ingrediente){
        if ($id_ingrediente) {
            $ingrediente = $this->dao->obtenerPorId($id_ingrediente);
            if ($ingrediente) {
                echo json_encode($ingrediente->toPublicArray());
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "Ingrediente no encontrado"]);
            }
        } else {
            $ingredientes = $this->dao->obtenerDatos();
            $ingredientesArray = array_map(function($ingrediente) {
                return $ingrediente->toPublicArray();
            }, $ingredientes);
            echo json_encode($ingredientesArray);
        }
        exit();
    }

    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['nombre_ingrediente'], $datos['cantidad_stock'], $datos['unidad'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos. Se requieren nombre_ingrediente, cantidad_stock y unidad."]);
            exit();
        }

        $ingrediente = new Ingrediente(
            null,
            $datos['nombre_ingrediente'],
            $datos['descripcion'] ?? null,
            $datos['cantidad_stock'],
            $datos['unidad']
        );

        if ($this->dao->insertar($ingrediente)) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Ingrediente creado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear el ingrediente"]);
        }
        exit();
    }

    private function handlePutRequest(?int $id_ingrediente){
        if (!$id_ingrediente) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de ingrediente necesario para actualizar"]);
            exit();
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['nombre_ingrediente'], $datos['cantidad_stock'], $datos['unidad'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos. Se requieren nombre_ingrediente, cantidad_stock y unidad."]);
            exit();
        }

        $ingredienteExistente = $this->dao->obtenerPorId($id_ingrediente);
        if (!$ingredienteExistente) {
            http_response_code(404);
            echo json_encode(["mensaje" => "Ingrediente a actualizar no encontrado"]);
            exit();
        }

        $ingrediente = new Ingrediente(
            $id_ingrediente,
            $datos['nombre_ingrediente'],
            $datos['descripcion'] ?? null,
            $datos['cantidad_stock'],
            $datos['unidad']
        );

        if ($this->dao->actualizar($ingrediente)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Ingrediente actualizado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar el ingrediente"]);
        }
        exit();
    }

    private function handleDeleteRequest(?int $id_ingrediente){
        if (!$id_ingrediente) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de ingrediente necesario para eliminar"]);
            exit();
        }

        if ($this->dao->eliminar($id_ingrediente)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Ingrediente eliminado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar el ingrediente"]);
        }
        exit();
    }
}

// Instanciar y manejar la solicitud
$controlador = new IngredienteApiController();
$controlador->manejarRequest();