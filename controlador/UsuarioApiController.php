<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require_once __DIR__.'/../accesoDatos/UsuarioDAO.php';
require_once __DIR__.'/../modelo/Usuario.php';

/**
 * Clase controladora para manejar las solicitudes API relacionadas con los usuarios.
 */
class UsuarioApiController {

    private $dao; // Instancia de UsuarioDAO para interactuar con la base de datos

    /**
     * Constructor de la clase. Inicializa la instancia de UsuarioDAO.
     */
    public function __construct(){
        $this->dao = new UsuarioDAO();
    }

    /**
     * Maneja la solicitud HTTP entrante, dirigiéndola al método adecuado
     * basado en el método de la solicitud (GET, POST, PUT, DELETE, OPTIONS).
     */
    public function manejarRequest(){
        $metodo = $_SERVER['REQUEST_METHOD']; 
        $id = $_GET['id_usuario'] ?? null; 

        // Manejo de la solicitud OPTIONS para el "preflight" de CORS.
        // El navegador envía una solicitud OPTIONS antes de una solicitud "real" (POST, PUT, DELETE)
        // para verificar si la solicitud es segura de enviar.
        if ($metodo === 'OPTIONS') {
            http_response_code(200); // Responde con 200 OK para el preflight
            exit(); // Termina la ejecución aquí
        }

        // Establece el tipo de contenido de la respuesta como JSON
        header('Content-Type: application/json');

        // Lógica para dirigir la solicitud según el método HTTP
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
                // Si el método no es reconocido, devuelve un error 405 (Método no permitido)
                http_response_code(405);
                echo json_encode(["mensaje" => "Método no permitido"]);
                exit();
        }
    }

    /**
     * Maneja las solicitudes GET para obtener uno o todos los usuarios.
     *
     * @param int|null $id_usuario El ID del usuario a obtener, o null para obtener todos.
     */
    private function handleGetRequest(?int $id_usuario){
        if ($id_usuario) {
            // Si se proporciona un ID, intenta obtener un usuario específico
            $usuario = $this->dao->obtenerPorId($id_usuario);
            if ($usuario) {
                echo json_encode($usuario->toPublicArray()); // Devuelve el usuario como JSON
            } else {
                // Si el usuario no se encuentra, devuelve un error 404
                http_response_code(404);
                echo json_encode(["mensaje" => "Usuario no encontrado"]);
            }
        } else {
            // Si no se proporciona ID, obtiene todos los usuarios
            $usuarios = $this->dao->obtenerDatos();

            // Mapea cada objeto Usuario a su representación de array público para JSON
            $usuariosArray = array_map(function($user) {
                return $user->toPublicArray();
            }, $usuarios);

            // Asegura que siempre se devuelva un array JSON, incluso si está vacío
            echo json_encode($usuariosArray);
        }
        exit();
    }

    /**
     * Maneja las solicitudes POST para crear un nuevo usuario.
     */
    private function handlePostRequest(){
        // Decodifica los datos JSON del cuerpo de la solicitud
        $datos = json_decode(file_get_contents("php://input"), true);

        // Valida que los datos esenciales estén presentes
        if (!isset($datos['nombre'], $datos['correo'], $datos['password'], $datos['id_rol'], $datos['estado'])) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "Datos incompletos para crear usuario. Se requieren nombre, correo, password, id_rol, estado."]);
            exit();
        }

       
        if ($this->dao->correoExiste($datos['correo'])) {
            http_response_code(409); 
            echo json_encode(["message" => "El correo electrónico '{$datos['correo']}' ya está en uso."]);
            exit();
        }

        // Asigna el ID del rol, manejando si está vacío
        $id_rol = isset($datos['id_rol']) && $datos['id_rol'] !== '' ? (int)$datos['id_rol'] : null;
        // Asigna la fecha de creación, usando la actual si no se proporciona
        $fecha_creacion = $datos['fecha_creacion'] ?? date('Y-m-d H:i:s');

        
        $password = password($datos['password']);

        // Crea un nuevo objeto Usuario con los datos proporcionados
        $usuario = new Usuario(
            null, // ID es null para un nuevo usuario 
            $datos['nombre'],
            $datos['correo'],
            $password, 
            $id_rol,
            $fecha_creacion,
            $datos['estado']
        );

        // Intenta insertar el usuario en la base de datos
        if ($this->dao->insertar($usuario)) {
            http_response_code(201); // Created
            echo json_encode(["mensaje" => "Usuario creado exitosamente"]);
        } else {
            // Si hay un error al insertar (ej. problema de base de datos)
            http_response_code(500); // Internal Server Error
            echo json_encode(["mensaje" => "Error al crear el usuario. Intenta de nuevo más tarde."]);
        }
        exit();
    }

    /**
     * Maneja las solicitudes PUT para actualizar un usuario existente.
     *
     * @param int|null $id_usuario El ID del usuario a actualizar.
     */
    private function handlePutRequest(?int $id_usuario){
        // Valida que se haya proporcionado un ID de usuario en la URL
        if (!$id_usuario) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "ID de usuario necesario para actualizar en la URL."]);
            exit();
        }

        // Decodifica los datos JSON del cuerpo de la solicitud
        $datos = json_decode(file_get_contents("php://input"), true);

        // Valida que los datos esenciales para la actualización estén presentes
        if (!isset($datos['nombre'], $datos['correo'], $datos['id_rol'], $datos['estado'])) {
             http_response_code(400); // Bad Request
             echo json_encode(["mensaje" => "Datos incompletos para actualizar usuario. Se requieren nombre, correo, id_rol, estado."]);
             exit();
        }

        // Obtiene el usuario existente de la base de datos para comparar datos y obtener la contraseña actual
        $usuarioExistente = $this->dao->obtenerPorId($id_usuario);
        if (!$usuarioExistente) {
            http_response_code(404); // Not Found
            echo json_encode(["mensaje" => "Usuario a actualizar no encontrado."]);
            exit();
        }

    
        if ($datos['correo'] !== $usuarioExistente->correo && $this->dao->correoExiste($datos['correo'], $id_usuario)) {
            http_response_code(409); // Conflict
            echo json_encode(["message" => "El correo electrónico '{$datos['correo']}' ya está en uso por otro usuario."]);
            exit();
        }

        // Asigna el ID del rol, manejando si está vacío
        $id_rol = isset($datos['id_rol']) && $datos['id_rol'] !== '' ? (int)$datos['id_rol'] : null;
        
        // Manejo de la contraseña
        $password_to_save = $usuarioExistente->password; 
        if (isset($datos['password']) && $datos['password'] !== '') {
            $password_to_save = password_hash($datos['password']);
        }

        // Asigna la fecha de creación, usando la existente si no se proporciona una nueva
        $fecha_creacion = $datos['fecha_creacion'] ?? $usuarioExistente->fecha_creacion;

        // Crea un objeto Usuario con los datos actualizados
        $usuario = new Usuario(
            $id_usuario,
            $datos['nombre'],
            $datos['correo'],
            $password_to_save,
            $id_rol,
            $fecha_creacion,
            $datos['estado']
        );

        // Intenta actualizar el usuario en la base de datos
        if ($this->dao->actualizar($usuario)) {
            http_response_code(200); // OK
            echo json_encode(["mensaje" => "Usuario actualizado exitosamente"]);
        } else {
            // Si hay un error al actualizar
            http_response_code(500); // Internal Server Error
            echo json_encode(["mensaje" => "Error al actualizar el usuario. Intenta de nuevo más tarde."]);
        }
        exit();
    }

    /**
     * Maneja las solicitudes DELETE para eliminar un usuario.
     *
     * @param int|null $id_usuario El ID del usuario a eliminar.
     */
    private function handleDeleteRequest(?int $id_usuario){
        // Valida que se haya proporcionado un ID de usuario en la URL
        if (!$id_usuario) {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "ID de usuario necesario para eliminar en la URL."]);
            exit();
        }

        // Intenta eliminar el usuario de la base de datos
        if ($this->dao->eliminar($id_usuario)) {
            http_response_code(200); // OK
            echo json_encode(["mensaje" => "Usuario eliminado exitosamente"]);
        } else {
            // Si hay un error al eliminar
            http_response_code(500); // Internal Server Error
            echo json_encode(["mensaje" => "Error al eliminar el usuario o usuario no encontrado"]);
        }
        exit();
    }
}

// Instanciar el controlador y manejar la solicitud
$controlador = new UsuarioApiController();
$controlador->manejarRequest();

?>