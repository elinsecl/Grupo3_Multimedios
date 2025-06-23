<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");


require_once __DIR__.'/../accesoDatos/UsuarioDAO.php';
require_once __DIR__.'/../modelo/Usuario.php';

class UsuarioApiController {

    private $dao;

    public function __construct(){
        $this->dao = new UsuarioDAO();
    }

    public function manejarRequest(){
        $metodo = $_SERVER['REQUEST_METHOD'];
        $id = $_GET['id_usuario'] ?? null;

        // Manejo de la solicitud OPTIONS para el "preflight" de CORS
        if ($metodo === 'OPTIONS') {
            http_response_code(200);
            exit(); // Termina aquí para el preflight
        }

        // Establece el tipo de contenido como JSON para las respuestas de datos
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
                http_response_code(405); // Método no permitido
                echo json_encode(["mensaje" => "Método no permitido"]);
                exit(); // Termina después de enviar la respuesta
        }
    }

    private function handleGetRequest(?int $id_usuario){
        if ($id_usuario) {
            $usuario = $this->dao->obtenerPorId($id_usuario);
            if ($usuario) {
                echo json_encode($usuario->toPublicArray());
            } else {
                http_response_code(404);
                echo json_encode(["mensaje" => "Usuario no encontrado"]);
            }
        } else {
            // Este bloque maneja la obtención de TODOS los usuarios
            $usuarios = $this->dao->obtenerDatos();

            // Mapear cada objeto Usuario a su representación de array público para JSON
            $usuariosArray = array_map(function($user) {
                // Asegúrate de que toPublicArray() existe en Usuario.php y devuelve un array.
                return $user->toPublicArray();
            }, $usuarios);

            // Aseguramos que siempre sea un array JSON, incluso si está vacío
            echo json_encode($usuariosArray);
        }
        exit(); // ¡IMPORTANTE! Termina el script aquí para evitar cualquier salida adicional.
    }

    private function handlePostRequest(){
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['nombre'], $datos['correo'], $datos['password'], $datos['tipo_usuario'], $datos['estado'])) {
            http_response_code(400);
            echo json_encode(["mensaje" => "Datos incompletos para crear usuario. Se requieren nombre, correo, password, tipo_usuario, estado."]);
            exit();
        }

        $id_rol = isset($datos['id_rol']) && $datos['id_rol'] !== '' ? (int)$datos['id_rol'] : null;
        $fecha_creacion = $datos['fecha_creacion'] ?? date('Y-m-d H:i:s');

        $usuario = new Usuario(
            null,
            $datos['nombre'],
            $datos['correo'],
            $datos['password'],
            $datos['tipo_usuario'],
            $id_rol,
            $fecha_creacion,
            $datos['estado']
        );

        if ($this->dao->insertar($usuario)) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Usuario creado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al crear el usuario"]);
        }
        exit(); // Termina después de enviar la respuesta
    }

    private function handlePutRequest(?int $id_usuario){
        if (!$id_usuario) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de usuario necesario para actualizar en la URL."]);
            exit();
        }

        $datos = json_decode(file_get_contents("php://input"), true);

        if (!isset($datos['nombre'], $datos['correo'], $datos['tipo_usuario'], $datos['estado'])) {
             http_response_code(400);
             echo json_encode(["mensaje" => "Datos incompletos para actualizar usuario. Se requieren nombre, correo, tipo_usuario, estado."]);
             exit();
        }

        $usuarioExistente = $this->dao->obtenerPorId($id_usuario);
        if (!$usuarioExistente) {
            http_response_code(404);
            echo json_encode(["mensaje" => "Usuario a actualizar no encontrado."]);
            exit();
        }

        $id_rol = isset($datos['id_rol']) && $datos['id_rol'] !== '' ? (int)$datos['id_rol'] : null;
        $password = isset($datos['password']) && $datos['password'] !== '' ? $datos['password'] : $usuarioExistente->password;
        $fecha_creacion = $datos['fecha_creacion'] ?? $usuarioExistente->fecha_creacion;

        $usuario = new Usuario(
            $id_usuario,
            $datos['nombre'],
            $datos['correo'],
            $password,
            $datos['tipo_usuario'],
            $id_rol,
            $fecha_creacion,
            $datos['estado']
        );

        if ($this->dao->actualizar($usuario)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Usuario actualizado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al actualizar el usuario o usuario no encontrado"]);
        }
        exit(); // Termina después de enviar la respuesta
    }

    private function handleDeleteRequest(?int $id_usuario){
        if (!$id_usuario) {
            http_response_code(400);
            echo json_encode(["mensaje" => "ID de usuario necesario para eliminar en la URL."]);
            exit();
        }

        if ($this->dao->eliminar($id_usuario)) {
            http_response_code(200);
            echo json_encode(["mensaje" => "Usuario eliminado exitosamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error al eliminar el usuario o usuario no encontrado"]);
        }
        exit(); // Termina después de enviar la respuesta
    }
}

// Instanciar y manejar la solicitud
$controlador = new UsuarioApiController();
$controlador->manejarRequest();

?>