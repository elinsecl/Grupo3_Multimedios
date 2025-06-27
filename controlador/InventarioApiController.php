<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once __DIR__.'/../accesoDatos/InventarioDAO.php';
require_once __DIR__.'/../accesoDatos/HistorialInventarioDAO.php';
require_once __DIR__.'/../accesoDatos/HistorialInventarioDAO.php';
require_once __DIR__.'/../modelo/HistorialInventario.php'; 

class InventarioApiController {
    private $dao;
    private $dao2;
    

    public function __construct() {
        $this->dao = new InventarioDAO();
        $this->dao2 = new HistorialInventarioDAO();
    }

    public function manejarRequest() {
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id'] ?? null;
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
        }
    }

    private function handleGetRequest($id) {
        if ($id) {
            $registro = $this->dao->obtenerPorId($id);
            echo json_encode($registro ?: ["mensaje" => "No encontrado"]);
        } else {
            echo json_encode($this->dao->obtenerDatos());
        }
    }

 private function handlePostRequest() {
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['ingrediente_id'], $datos['cantidad_stock'], $datos['proveedor_id'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos"]);
            return;
        }

        date_default_timezone_set('America/Costa_Rica');

        $registro = new Inventario(
            null,
            $datos['ingrediente_id'],
            $datos['cantidad_stock'],
            (!empty($datos['fecha_entrada']) && trim($datos['fecha_entrada']) !== '') ? $datos['fecha_entrada'] : date('Y-m-d H:i:s'),
            $datos['proveedor_id']
        );

        $registro_id = $this->dao->insertar2($registro); // Modifica tu método insertar() para retornar el ID

         if ($registro_id !== false) {
           
            // Crear objeto HistorialInventario
             $historial = new HistorialInventario(
                null,
                $registro_id,
                $datos['ingrediente_id'],
                $datos['cantidad_stock'],
                 (!empty($datos['fecha_entrada']) && trim($datos['fecha_entrada']) !== '') ? $datos['fecha_entrada'] : date('Y-m-d H:i:s'),
                $datos['tipo_cambio'] ?? 'compra'
             );

            // Insertar historial (asegúrate de que dao2 esté correctamente inicializado)
            $this->dao2->insertar($historial);

            http_response_code(201);
            echo json_encode([
                "mensaje" => "Inventario e historial registrados exitosamente",
                "inventario_id" => $registro_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al registrar el inventario"]);
        }
    }

     
    private function handlePutRequest($id) {
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID necesario"]);
            return;
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['ingrediente_id'], $datos['cantidad_stock'], $datos['proveedor_id'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos"]);
            return;
        }

        $registro = new Inventario(
            $id,
            $datos['ingrediente_id'],
            $datos['cantidad_stock'],
            $datos['fecha_entrada'] ?? date('Y-m-d H:i:s'),
            $datos['proveedor_id']
        );

        if ($this->dao->actualizar($registro)) {
            echo json_encode(["mensaje" => "Inventario actualizado"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar"]);
        }
    }

    private function handleDeleteRequest($id) {
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID necesario"]);
            return;
        }

        if ($this->dao->eliminar($id)) {
            echo json_encode(["mensaje" => "Inventario eliminado"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar"]);
        }
    }
}
?>
