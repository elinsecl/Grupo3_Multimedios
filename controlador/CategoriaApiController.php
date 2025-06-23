<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once __DIR__.'/../accesoDatos/CategoriaDAO.php'; 
require_once __DIR__.'/../modelo/Categoria.php'; 

class CategoriaApiController {

    private $dao;

    public function __construct(){
        $this->dao = new CategoriaDAO();
    }

    /**
     * Maneja las solicitudes HTTP (GET, POST, PUT, DELETE) para el recurso Categoria.
     */
    public function manejarRequest(){
        $metodosPermitidos = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];
        $metodo = $_SERVER['REQUEST_METHOD'];
        
        if (!in_array($metodo, $metodosPermitidos)) {
            http_response_code(405);
            header('Allow: ' . implode(', ', $metodosPermitidos));
            echo json_encode(["mensaje" => "Método no permitido"]);
            return;
        }

        // Manejo de la solicitud OPTIONS para el "preflight" de CORS
        if ($metodo === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        header('Content-Type: application/json');

        $id = $_GET['id_categoria'] ?? null;

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
        }
    }

    private function getJsonInput(){
        $input = file_get_contents("php://input");
        $datos = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(["mensaje" => "JSON inválido"]);
            exit();
        }
        
        return $datos;
    }

    private function validarDatosCategoria(array $datos): bool {
        if (empty($datos['nombre_categoria']) || strlen($datos['nombre_categoria']) > 50) {
            return false;
        }
        
        if (isset($datos['descripcion']) && strlen($datos['descripcion']) > 255) {
            return false;
        }
        
        return true;
    }

    private function handleGetRequest(?int $id_categoria){
        if ($id_categoria) {
            $categoria = $this->dao->obtenerPorId($id_categoria);
            if ($categoria) {
                echo json_encode($categoria);
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "Categoría no encontrada"]);
            }
        } else {
            $categorias = $this->dao->obtenerDatos();
            echo json_encode($categorias);
        }
    }

    private function handlePostRequest(){
        $datos = $this->getJsonInput();

        if (!isset($datos['nombre_categoria'], $datos['descripcion'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos para crear categoría"]);
            return;
        }

        if (!$this->validarDatosCategoria($datos)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos de categoría inválidos"]);
            return;
        }

        if ($this->dao->existeCategoriaConNombre($datos['nombre_categoria'])) {
            http_response_code(409);
            echo json_encode(["mensaje" => "Ya existe una categoría con ese nombre"]);
            return;
        }

        $categoria = new Categoria(
            null,
            $datos['nombre_categoria'],
            $datos['descripcion']
        );

        if ($this->dao->insertar($categoria)) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Categoría creada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear la categoría"]);
        }
    }

    private function handlePutRequest(?int $id_categoria){
        if (!$id_categoria) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de categoría necesario para actualizar"]);
            return;
        }

        if (!$this->dao->obtenerPorId($id_categoria)) {
            http_response_code(404);
            echo json_encode(["mensaje" => "Categoría no encontrada"]);
            return;
        }

        $datos = $this->getJsonInput();

        if (!isset($datos['nombre_categoria'], $datos['descripcion'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos para actualizar categoría"]);
            return;
        }

        if (!$this->validarDatosCategoria($datos)) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos de categoría inválidos"]);
            return;
        }

        // Verificar si el nuevo nombre ya existe en otra categoría
        $categoriaExistente = $this->dao->obtenerPorNombre($datos['nombre_categoria']);
        if ($categoriaExistente && $categoriaExistente->getIdCategoria() != $id_categoria) {
            http_response_code(409);
            echo json_encode(["mensaje" => "Ya existe otra categoría con ese nombre"]);
            return;
        }

        $categoria = new Categoria(
            $id_categoria,
            $datos['nombre_categoria'],
            $datos['descripcion']
        );

        if ($this->dao->actualizar($categoria)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Categoría actualizada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar la categoría"]);
        }
    }

    private function handleDeleteRequest(?int $id_categoria){
        if (!$id_categoria) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de categoría necesario para eliminar"]);
            return;
        }

        if (!$this->dao->obtenerPorId($id_categoria)) {
            http_response_code(404);
            echo json_encode(["mensaje" => "Categoría no encontrada"]);
            return;
        }

        if ($this->dao->tienePlatillosAsociados($id_categoria)) {
            http_response_code(409);
            echo json_encode(["mensaje" => "No se puede eliminar la categoría porque tiene platillos asociados"]);
            return;
        }

        if ($this->dao->eliminar($id_categoria)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Categoría eliminada exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar la categoría"]);
        }
    }
}
?>