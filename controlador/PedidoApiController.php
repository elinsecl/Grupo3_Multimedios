<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once __DIR__.'/../accesoDatos/PedidoDAO.php';
require_once __DIR__.'/../modelo/Pedido.php';
require_once __DIR__.'/../accesoDatos/HistorialPedidoDAO.php';
require_once __DIR__.'/../modelo/HistorialPedido.php';

class PedidoApiController {

    private $dao;
    private $historialDao;

    public function __construct(){
        $this->dao = new PedidoDAO();
        $this->historialDao = new HistorialPedidoDAO();
    }

    public function manejarRequest(){
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_pedido'] ?? null;

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

    private function handleGetRequest(?int $id_pedido){
        try {
            if ($id_pedido) {
                $pedido = $this->dao->obtenerPorId($id_pedido);
                if ($pedido) {
                    echo json_encode($pedido->toPublicArray());
                } else {
                    http_response_code(404);
                    echo json_encode(["mensaje" => "Pedido no encontrado"]);
                }
            } else {
                $pedidos = $this->dao->obtenerDatos();
                $pedidosArray = array_map(function($pedido) {
                    return $pedido->toPublicArray();
                }, $pedidos);
                echo json_encode($pedidosArray);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "mensaje" => "Error al obtener los pedidos.",
                "error" => $e->getMessage()
            ]);
        }
        exit();
    }

    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['cliente_id'], $datos['mesa_id'], $datos['hora_pedido'], $datos['total'], $datos['metodo_pago'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos. Se requieren cliente_id, mesa_id, hora_pedido, total y metodo_pago."]);
            exit();
        }

        $pedido = new Pedido(
            null,
            $datos['cliente_id'],
            $datos['mesa_id'],
            $datos['fecha_pedido'] ?? date('Y-m-d H:i:s'),
            $datos['hora_pedido'],
            $datos['total'],
            $datos['estado'] ?? 'pendiente',
            $datos['metodo_pago']
        );

        try {
            $pedidoId = $this->dao->insertar($pedido);

            if ($pedidoId !== false) {
                $historial = new HistorialPedido(
                    null,
                    $pedidoId,
                    $datos['fecha_entrega'] ?? date('Y-m-d H:i:s'),
                    $datos['estado_entrega'] ?? 'pendiente'
                );

                $this->historialDao->insertar($historial);

                http_response_code(201);
                echo json_encode([
                    "mensaje" => "Pedido creado exitosamente",
                    "pedido_id" => $pedidoId
                ]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "Error al crear el pedido"]);
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                http_response_code(409);
                echo json_encode([
                    "mensaje" => "Error de integridad de datos al crear el pedido.",
                    "error" => $e->getMessage()
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "mensaje" => "Error inesperado al crear el pedido.",
                    "error" => $e->getMessage()
                ]);
            }
        }
        exit();
    }

    private function handlePutRequest(?int $id_pedido){
        if (!$id_pedido) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de pedido necesario para actualizar"]);
            exit();
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['cliente_id'], $datos['mesa_id'], $datos['hora_pedido'], $datos['total'], $datos['metodo_pago'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos. Se requieren cliente_id, mesa_id, hora_pedido, total y metodo_pago."]);
            exit();
        }

        $pedidoExistente = $this->dao->obtenerPorId($id_pedido);
        if (!$pedidoExistente) {
            http_response_code(404);
            echo json_encode(["mensaje" => "Pedido a actualizar no encontrado"]);
            exit();
        }

        $pedido = new Pedido(
            $id_pedido,
            $datos['cliente_id'],
            $datos['mesa_id'],
            $datos['fecha_pedido'] ?? date('Y-m-d H:i:s'),
            $datos['hora_pedido'],
            $datos['total'],
            $datos['estado'] ?? 'pendiente',
            $datos['metodo_pago']
        );

        try {
            if ($this->dao->actualizar($pedido)) {
                http_response_code(200);
                echo json_encode(["mensaje" => "Pedido actualizado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "Error al actualizar el pedido"]);
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                http_response_code(409);
                echo json_encode([
                    "mensaje" => "Error de integridad de datos al actualizar el pedido.",
                    "error" => $e->getMessage()
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "mensaje" => "Error inesperado al actualizar el pedido.",
                    "error" => $e->getMessage()
                ]);
            }
        }
        exit();
    }

    private function handleDeleteRequest(?int $id_pedido){
        if (!$id_pedido) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de pedido necesario para eliminar"]);
            exit();
        }

        try {
            if ($this->dao->eliminar($id_pedido)) {
                http_response_code(200);
                echo json_encode(["mensaje" => "Pedido eliminado exitosamente"]);
            } else {
                http_response_code(500);
                echo json_encode(["mensaje" => "Error al eliminar el pedido"]);
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                http_response_code(409);
                echo json_encode([
                    "mensaje" => "No se puede eliminar el pedido porque está relacionado con otros registros.",
                    "error" => $e->getMessage()
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    "mensaje" => "Error inesperado al eliminar el pedido.",
                    "error" => $e->getMessage()
                ]);
            }
        }
        exit();
    }
}

// Instanciar y manejar la solicitud
$controlador = new PedidoApiController();
$controlador->manejarRequest();
