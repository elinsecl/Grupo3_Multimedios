<?php

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
            $ingrediente = $this->dao->obtenerPorId($id);
            echo $ingrediente ? json_encode($ingrediente) :
                json_encode(["mensaje" => "Ingrediente no encontrado"]);
        } else {
            echo json_encode($this->dao->obtenerDatos());
        }
    }

    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['nombre_ingrediente'], $datos['cantidad_stock'], $datos['unidad'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Faltan datos requeridos"]);
            return;
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
    }

    private function handlePutRequest(?int $id){
        if (!$id) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID requerido"]);
            return;
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        $ingrediente = new Ingrediente(
            $id,
            $datos['nombre_ingrediente'],
            $datos['descripcion'] ?? null,
            $datos['cantidad_stock'],
            $datos['unidad']
        );

        if ($this->dao->actualizar($ingrediente)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Ingrediente actualizado"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar el ingrediente"]);
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
            echo json_encode(["mensaje" => "Ingrediente eliminado"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar el ingrediente"]);
        }
    }
}
?>
