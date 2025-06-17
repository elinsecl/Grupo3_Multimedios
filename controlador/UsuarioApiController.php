<?php

require_once __DIR__.'/../accesoDatos/UsuarioDAO.php'; 
require_once __DIR__.'/../modelo/Usuario.php'; 

class UsuarioApiController {

    private $dao;

    public function __construct(){
        $this->dao = new UsuarioDAO();
    }

    /**
     * Maneja las solicitudes HTTP (GET, POST, PUT, DELETE) para el recurso Usuario.
     */
    public function manejarRequest(){
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_usuario'] ?? null; // Obtiene el ID si está presente en la URL (para GET, PUT, DELETE)

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
     * Si se proporciona un ID, obtiene un usuario específico; de lo contrario, obtiene todos los usuarios.
     * @param int|null $id_usuario El ID del usuario a obtener.
     */
    private function handleGetRequest(?int $id_usuario){
        if ($id_usuario) {
            $usuario = $this->dao->obtenerPorId($id_usuario);
            if ($usuario) {
                echo json_encode($usuario);
            } else {
                http_response_code(404); // No encontrado
                echo json_encode(["mensaje" => "Usuario no encontrado"]);
            }
        } else {
            $usuarios = $this->dao->obtenerDatos();
            echo json_encode($usuarios);
        }
    }

    /**
     * Maneja las solicitudes POST para crear un nuevo usuario.
     */
    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        // Validar que los datos necesarios estén presentes
        if (!isset($datos['nombre'], $datos['correo'], $datos['password'], $datos['tipo_usuario'], $datos['id_rol'], $datos['fecha_creacion'], $datos['estado'])) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "Datos incompletos para crear usuario"]);
            return;
        }

        
        $usuario = new Usuario(
            null, // id_usuario es null para la inserción
            $datos['nombre'],
            $datos['correo'],
            $datos['password'], 
            $datos['tipo_usuario'],
            $datos['id_rol'],
            $datos['fecha_creacion'],
            $datos['estado']
        );

        if ($this->dao->insertar($usuario)) {
            http_response_code(201); // Creado
            echo json_encode(["mensaje" => "Usuario creado exitosamente"]);
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["mensaje" => "Error al crear el usuario"]);
        }
    }

    /**
     * Maneja las solicitudes PUT para actualizar un usuario existente.
     * @param int|null $id_usuario El ID del usuario a actualizar.
     */
    private function handlePutRequest(?int $id_usuario){
        if (!$id_usuario) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "ID de usuario necesario para actualizar"]);
            return;
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        // Validar que los datos necesarios estén presentes para la actualización
        if (!isset($datos['nombre'], $datos['correo'], $datos['password'], $datos['tipo_usuario'], $datos['id_rol'], $datos['fecha_creacion'], $datos['estado'])) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "Datos incompletos para actualizar usuario"]);
            return;
        }

        // Crear un objeto Usuario con el ID y los datos actualizados
        $usuario = new Usuario(
            $id_usuario,
            $datos['nombre'],
            $datos['correo'],
            $datos['password'], 
            $datos['tipo_usuario'],
            $datos['id_rol'],
            $datos['fecha_creacion'],
            $datos['estado']
        );

        if ($this->dao->actualizar($usuario)) {
            http_response_code(200); // OK
            echo json_encode(["mensaje" => "Usuario actualizado exitosamente"]);
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["mensaje" => "Error al actualizar el usuario o usuario no encontrado"]);
        }
    }

    /**
     * Maneja las solicitudes DELETE para eliminar un usuario.
     * @param int|null $id_usuario El ID del usuario a eliminar.
     */
    private function handleDeleteRequest(?int $id_usuario){
        if (!$id_usuario) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "ID de usuario necesario para eliminar"]);
            return;
        }

        if ($this->dao->eliminar($id_usuario)) {
            http_response_code(200); // OK
            echo json_encode(["mensaje" => "Usuario eliminado exitosamente"]);
        } else {
            http_response_code(500); // Error interno del servidor
            echo json_encode(["mensaje" => "Error al eliminar el usuario o usuario no encontrado"]);
        }
    }
}

?>