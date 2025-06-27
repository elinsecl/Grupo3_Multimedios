<?php
header("Access-Control-Allow-Origin: * ");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

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
        $id_historial = $_GET['id_historial_inventario'] ?? null;

         // Manejo de la solicitud OPTIONS para el "preflight" de CORS
        if ($metodo === 'OPTIONS') {
            http_response_code(200);
            exit(); // Termina la ejecución para el preflight
        }

        header('Content-Type: application/json');

        switch ($metodo) {
            case 'GET':
                $this->handleGetRequest($id_historial);
                break;
            case 'POST':
                $this->handlePostRequest();
                break;
            case 'DELETE':
                $this->handleDeleteRequest($id_historial);
                break;
            default:
                http_response_code(405);
                echo json_encode(["mensaje" => "Método no permitido"]);
                break;
        }
    }

    private function handleGetRequest(?int $id_historial){
        if ($id_historial) {
            $historial_inventario = $this->dao->obtenerPorId($id_historial);
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

        $historial_inventario = new HistorialInventario(
            null,
            $datos['id_inventario'],
            $datos['ingrediente_id'],
            $datos['cambio_stock'],
            $datos['fecha'],
            $datos['tipo_cambio']
        );

        if ($this->dao->insertar($historial_inventario)) {
            http_response_code(201);
            echo json_encode(["mensaje" => "historial de inventario creado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear el historial del inventario"]);
        }
    }

    private function handleDeleteRequest(?int $id_historial){
        if (!$id_historial) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID del historial del inventario necesario para eliminar"]);
            return;
        }

        if ($this->dao->eliminar($id_historial)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "historial del inventario eliminada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar historial del inventario"]);
        }   

    }

}

?>