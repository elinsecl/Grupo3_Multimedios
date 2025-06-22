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

    /**
     * Maneja las solicitudes HTTP (GET, POST, PUT, DELETE) para el recurso Platillo.
     */
    public function manejarRequest(){
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_platillo'] ?? null; // Obtiene el ID si está presente en la URL (para GET, PUT, DELETE)

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
     * Si se proporciona un ID, obtiene un platillo específico; de lo contrario, obtiene todos los platillos.
     * @param int|null $id_platillo El ID del platillo a obtener.
     */
    private function handleGetRequest(?int $id_platillo){
        if ($id_platillo) {
            $platillo = $this->dao->obtenerPorId($id_platillo);
            if ($platillo) {
                echo json_encode($platillo);
            } else {
                http_response_code(404); // No encontrado
                echo json_encode(["mensaje" => "Platillo no encontrado"]);
            }
        } else {
            $platillos = $this->dao->obtenerDatos();
            echo json_encode($platillos);
        }
    }

    /**
     * Maneja las solicitudes POST para crear un nuevo platillo.
     */
    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        // Validar que los datos necesarios estén presentes
        if (!isset($datos['nombre_platillo'], $datos['descripcion'], $datos['precio'], $datos['id_categoria'], $datos['estado'], $datos['imagen_url'])) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "Datos incompletos para crear platillo"]);
            return;
        }

        $platillo = new Platillo(
            null, // id_platillo es null para la inserción
            $datos['nombre_platillo'],
            $datos['descripcion'],
            $datos['precio'], 
            $datos['id_categoria'],
            $datos['estado'],
            $datos['imagen_url']
        );

        if ($this->dao->insertar($platillo)) {
            http_response_code(201); // Creado
            echo json_encode(["mensaje" => "Platillo creado exitosamente"]);
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["mensaje" => "Error al crear el platillo"]);
        }
    }

    /**
     * Maneja las solicitudes PUT para actualizar un platillo existente.
     * @param int|null $id_platillo El ID del platillo a actualizar.
     */
    private function handlePutRequest(?int $id_platillo){
        if (!$id_platillo) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "ID de platillo necesario para actualizar"]);
            return;
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        // Validar que los datos necesarios estén presentes para la actualización
        if (!isset($datos['nombre_platillo'], $datos['descripcion'], $datos['precio'], $datos['id_categoria'], $datos['estado'], $datos['imagen_url'])) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "Datos incompletos para actualizar platillo"]);
            return;
        }

        // Crear un objeto Platillo con el ID y los datos actualizados
        $platillo = new Platillo(
            $id_platillo,
            $datos['nombre_platillo'],
            $datos['descripcion'],
            $datos['precio'], 
            $datos['id_categoria'],
            $datos['estado'],
            $datos['imagen_url']
        );

        if ($this->dao->actualizar($platillo)) {
            http_response_code(200); // OK
            echo json_encode(["mensaje" => "Platillo actualizado exitosamente"]);
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["mensaje" => "Error al actualizar el platillo o platillo no encontrado"]);
        }
    }

    /**
     * Maneja las solicitudes DELETE para eliminar un platillo.
     * @param int|null $id_platillo El ID del platillo a eliminar.
     */
    private function handleDeleteRequest(?int $id_platillo){
        if (!$id_platillo) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "ID de platillo necesario para eliminar"]);
            return;
        }

        if ($this->dao->eliminar($id_platillo)) {
            http_response_code(200); // OK
            echo json_encode(["mensaje" => "Platillo eliminado exitosamente"]);
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["mensaje" => "Error al eliminar el platillo o platillo no encontrado"]);
        }
    }
}

?>
