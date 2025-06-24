<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once __DIR__.'/../accesoDatos/PlatilloDAO.php'; 
require_once __DIR__.'/../modelo/Platillo.php'; 

class PlatilloApiController {

    private $dao;

    public function __construct(){
        $this->dao = new PlatilloDAO();
    }

    public function manejarRequest(){
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_platillo'] ?? null;

        // Manejo de la solicitud OPTIONS para el "preflight" de CORS
        if ($metodo === 'OPTIONS') {
            http_response_code(200);
            exit(); // Termina aquí para el preflight
        }

        // Establece el tipo de contenido como JSON para las respuestas de datos
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
                http_response_code(405); // Método no permitido
                echo json_encode(["mensaje" => "Método no permitido"]);
                exit(); // Termina después de enviar la respuesta
        }
    }

    private function handleGetRequest(?int $id_platillo){
        if ($id_platillo) {
            $platillo = $this->dao->obtenerPorId($id_platillo);
            if ($platillo) {
                echo json_encode($platillo->toPublicArray());
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "Platillo no encontrado"]);
            }
        } else {
            // Obtener todos los platillos
            $platillos = $this->dao->obtenerDatos();

            // Mapear cada objeto Platillo a su representación de array público para JSON
            $platillosArray = array_map(function($platillo) {
                return $platillo->toPublicArray();
            }, $platillos);

            echo json_encode($platillosArray);
        }
        exit(); // Termina el script aquí para evitar cualquier salida adicional
    }

    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['nombre_platillo'], $datos['descripcion'], $datos['precio'], $datos['id_categoria'], $datos['estado'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos para crear platillo. Se requieren nombre_platillo, descripcion, precio, id_categoria y estado."]);
            exit();
        }

        $platillo = new Platillo(
            null, // id_platillo es autoincremental
            $datos['nombre_platillo'],
            $datos['descripcion'],
            $datos['precio'],
            $datos['id_categoria'],
            $datos['estado']
        );

        if ($this->dao->insertar($platillo)) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Platillo creado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear el platillo"]);
        }
        exit();
    }

    private function handlePutRequest(?int $id_platillo){
        if (!$id_platillo) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de platillo necesario para actualizar en la URL."]);
            exit();
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['nombre_platillo'], $datos['descripcion'], $datos['precio'], $datos['id_categoria'], $datos['estado'])) {
             http_response_code(400);
             echo json_encode(["mensaje" => "Datos incompletos para actualizar platillo. Se requieren nombre_platillo, descripcion, precio, id_categoria y estado."]);
             exit();
        }

        $platilloExistente = $this->dao->obtenerPorId($id_platillo);
        if (!$platilloExistente) {
            http_response_code(404);
            echo json_encode(["mensaje" => "Platillo a actualizar no encontrado."]);
            exit();
        }

        $platillo = new Platillo(
            $id_platillo,
            $datos['nombre_platillo'],
            $datos['descripcion'],
            $datos['precio'],
            $datos['id_categoria'],
            $datos['estado']
        );

        if ($this->dao->actualizar($platillo)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Platillo actualizado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar el platillo o platillo no encontrado"]);
        }
        exit();
    }

    private function handleDeleteRequest(?int $id_platillo){
        if (!$id_platillo) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de platillo necesario para eliminar en la URL."]);
            exit();
        }

        if ($this->dao->eliminar($id_platillo)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Platillo eliminado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar el platillo o platillo no encontrado"]);
        }
        exit();
    }
}

// Instanciar y manejar la solicitud
$controlador = new PlatilloApiController();
$controlador->manejarRequest();