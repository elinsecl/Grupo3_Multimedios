<?php

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
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_categoria'] ?? null; // Obtiene el ID si está presente en la URL (para GET, PUT, DELETE)

        header('Content-Type: application/json'); // Establece el tipo de contenido como JSON

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
                break;
        }
    }

    /**
     * Maneja las solicitudes GET.
     * Si se proporciona un ID, obtiene una categoría específica; de lo contrario, obtiene todas las categorías.
     * @param int|null $id_categoria El ID de la categoría a obtener.
     */
    private function handleGetRequest(?int $id_categoria){
        if ($id_categoria) {
            $categoria = $this->dao->obtenerPorId($id_categoria);
            if ($categoria) {
                echo json_encode($categoria);
            } else {
                http_response_code(404); // No encontrado
                echo json_encode(["mensaje" => "Categoría no encontrada"]);
            }
        } else {
            $categorias = $this->dao->obtenerDatos();
            echo json_encode($categorias);
        }
    }

    /**
     * Maneja las solicitudes POST para crear una nueva categoría.
     */
    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        // Validar que los datos necesarios estén presentes
        if (!isset($datos['nombre_categoria'], $datos['descripcion'])) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "Datos incompletos para crear categoría"]);
            return;
        }

        $categoria = new Categoria(
            null, // id_categoria es null para la inserción
            $datos['nombre_categoria'],
            $datos['descripcion']
        );

        if ($this->dao->insertar($categoria)) {
            http_response_code(201); // Creado
            echo json_encode(["mensaje" => "Categoría creada exitosamente"]);
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["mensaje" => "Error al crear la categoría"]);
        }
    }

    /**
     * Maneja las solicitudes PUT para actualizar una categoría existente.
     * @param int|null $id_categoria El ID de la categoría a actualizar.
     */
    private function handlePutRequest(?int $id_categoria){
        if (!$id_categoria) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "ID de categoría necesario para actualizar"]);
            return;
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        // Validar que los datos necesarios estén presentes para la actualización
        if (!isset($datos['nombre_categoria'], $datos['descripcion'])) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "Datos incompletos para actualizar categoría"]);
            return;
        }

        // Crear un objeto Categoria con el ID y los datos actualizados
        $categoria = new Categoria(
            $id_categoria,
            $datos['nombre_categoria'],
            $datos['descripcion']
        );

        if ($this->dao->actualizar($categoria)) {
            http_response_code(200); // OK
            echo json_encode(["mensaje" => "Categoría actualizada exitosamente"]);
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["mensaje" => "Error al actualizar la categoría o categoría no encontrada"]);
        }
    }

    /**
     * Maneja las solicitudes DELETE para eliminar una categoría.
     * @param int|null $id_categoria El ID de la categoría a eliminar.
     */
    private function handleDeleteRequest(?int $id_categoria){
        if (!$id_categoria) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "ID de categoría necesario para eliminar"]);
            return;
        }

        if ($this->dao->eliminar($id_categoria)) {
            http_response_code(200); // OK
            echo json_encode(["mensaje" => "Categoría eliminada exitosamente"]);
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["mensaje" => "Error al eliminar la categoría o categoría no encontrada"]);
        }
    }
}

?>
