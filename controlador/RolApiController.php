<?php


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once __DIR__.'/../accesoDatos/RolDao.php'; 
require_once __DIR__.'/../modelo/Rol.php';       
class RolApiController {

    private $dao;

    public function __construct(){
        $this->dao = new RolDAO(); // Instancia el DAO de Rol
    }

    /**
     * Maneja las solicitudes HTTP (GET, POST, PUT, DELETE, OPTIONS) para el recurso Rol.
     */
    public function manejarRequest(){
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_rol'] ?? null; // Obtiene el ID si está presente en la URL

        // Manejo de la solicitud OPTIONS para el "preflight" de CORS
        if ($metodo === 'OPTIONS') {
            http_response_code(200);
            exit(); // Termina la ejecución para el preflight
        }

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
                exit();
        }
    }

    /**
     * Maneja las solicitudes GET.
     * Si se proporciona un ID, obtiene un rol específico; de lo contrario, obtiene todos los roles.
     * @param int|null $id_rol El ID del rol a obtener.
     */
    private function handleGetRequest(?int $id_rol){
        if ($id_rol) {
            $rol = $this->dao->obtenerPorId($id_rol); // Llama al DAO de Rol
            if ($rol) {
                // --- MODIFICACIÓN: Llamar a toArray() para serializar el objeto Rol ---
                echo json_encode($rol->toArray());
            } else {
                http_response_code(404); // No encontrado
                echo json_encode(["mensaje" => "Rol no encontrado"]); // Mensaje específico para Rol
            }
        } else {
            $roles = $this->dao->obtenerDatos(); // Llama al DAO de Rol 

            $rolesArray = array_map(function($rol_obj) {
                return $rol_obj->toArray();
            }, $roles);

            echo json_encode($rolesArray);
        }
        exit(); 
    }

    /**
     * Maneja las solicitudes POST para crear un nuevo rol.
     */
    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        // Validar que los datos necesarios estén presentes para Rol
        if (!isset($datos['nombre_rol'], $datos['descripcion'])) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "Datos incompletos para crear rol. Se requieren nombre_rol, descripcion."]);
            exit(); 
        }

        // Crea un objeto Rol con los datos recibidos
        $rol = new Rol(
            null, // id_rol es null para la inserción
            $datos['nombre_rol'],
            $datos['descripcion']
        );

        if ($this->dao->insertar($rol)) { // Llama al DAO de Rol
            http_response_code(201);
            echo json_encode(["mensaje" => "Rol creado exitosamente"]);
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["mensaje" => "Error al crear el rol"]);
        }
        exit(); 
    }

    /**
     * Maneja las solicitudes PUT para actualizar un rol existente.
     * @param int|null $id_rol El ID del rol a actualizar.
     */
    private function handlePutRequest(?int $id_rol){
        if (!$id_rol) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "ID de rol necesario para actualizar."]);
            exit(); 
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        // Validar que los datos necesarios estén presentes para la actualización de Rol
        if (!isset($datos['nombre_rol'], $datos['descripcion'])) {
             http_response_code(400); // Bad Request
             echo json_encode(["mensaje" => "Datos incompletos para actualizar rol. Se requieren nombre_rol, descripcion."]);
             exit();
        }

        // Crear un objeto Rol con el ID y los datos actualizados
        $rol = new Rol(
            $id_rol, // Usamos el ID de la URL
            $datos['nombre_rol'],
            $datos['descripcion']
        );

        if ($this->dao->actualizar($rol)) { // Llama al DAO de Rol
            http_response_code(200); // OK
            echo json_encode(["mensaje" => "Rol actualizado exitosamente"]);
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["mensaje" => "Error al actualizar el rol o rol no encontrado"]);
        }
        exit(); 
    }

    /**
     * Maneja las solicitudes DELETE para eliminar un rol.
     * @param int|null $id_rol El ID del rol a eliminar.
     */
    private function handleDeleteRequest(?int $id_rol){
        if (!$id_rol) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "ID de rol necesario para eliminar."]);
            exit(); 
        }

        if ($this->dao->eliminar($id_rol)) { // Llama al DAO de Rol
            http_response_code(200); // OK
            echo json_encode(["mensaje" => "Rol eliminado exitosamente"]);
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["mensaje" => "Error al eliminar el rol o rol no encontrado"]);
        }
        exit();
    }
}

// Instanciar y manejar la solicitud
$controlador = new RolApiController();
$controlador->manejarRequest();

?>