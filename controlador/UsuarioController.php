<?php


require_once __DIR__.'/../accesoDatos/UsuarioDAO.php'; 
require_once __DIR__.'/../modelo/Usuario.php'; 

class UsuarioController {

    private $dao;

    public function __construct(){
        // Cambiado de new UsuarioDao() a new UsuarioDAO() para una convención de nombres consistente
        $this->dao = new UsuarioDAO(); 
    }

    /**
     * Obtiene todos los Usuarios.
     * @return array Un array de objetos Usuario.
     */
    public function obtenerDatos(): array {
        return $this->dao->obtenerDatos();
    }

    /**
     * Obtiene un Usuario por su ID.
     * @param int $id El ID del Usuario a buscar.
     * @return Usuario|null Un objeto Usuario si se encuentra, o null.
     */
    public function obtenerPorId(int $id): ?Usuario {
        return $this->dao->obtenerPorId($id);
    }

    /**
     * Inserta un nuevo Usuario.
     * @param Usuario $objeto El objeto Usuario a insertar.
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    public function insertar(Usuario $objeto): bool {
        return $this->dao->insertar($objeto);
    }

    /**
     * Actualiza un usuario existente.
     * @param Usuario $objeto El objeto Usuario con los datos actualizados.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizar(Usuario $objeto): bool {
        return $this->dao->actualizar($objeto);
    }
    
    /**
     * Elimina un Usuario por su ID.
     * @param int $id El ID del Usuario a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function eliminar(int $id): bool {
        return $this->dao->eliminar($id);
    }
}

## Manejo de Solicitudes HTTP (GET, POST, PUT, DELETE)

// Este bloque de código iría en un archivo de entrada
// que maneja las solicitudes HTTP.

$controlador = new UsuarioController();

// Establecer el encabezado JSON para las respuestas de la API
header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Manejar solicitud GET: Obtener todos o uno por ID
        $id = $_GET['id'] ?? null; 
        if ($id) {
            $usuario = $controlador->obtenerPorId((int)$id);
            if ($usuario) {
                echo json_encode($usuario);
            } else {
                http_response_code(404); // No encontrado
                echo json_encode(["mensaje" => "Usuario no encontrado"]);
            }
        } else {
            $usuarios = $controlador->obtenerDatos();
            echo json_encode($usuarios);
        }
        break;

    case 'POST':
        // Manejar solicitud POST: Insertar un nuevo Usuario
        // Obtener datos del cuerpo de la solicitud
        $input = json_decode(file_get_contents("php://input"), true);

        // Los parámetros para el constructor de Usuario son:
        // $id_usuario (null para inserción), $nombre, $correo, $password, $tipo_usuario, $id_rol, $fecha_creacion, $estado
        $id_usuario = null; 
        $nombre = $input['nombre'] ?? null;
        $correo = $input['correo'] ?? null;
        $password = $input['password'] ?? null;
        $tipo_usuario = $input['tipo_usuario'] ?? null;
        $id_rol = $input['id_rol'] ?? null;
        $fecha_creacion = $input['fecha_creacion'] ?? date('Y-m-d H:i:s'); 
        $estado = $input['estado'] ?? null; 

        // Validar que los campos esenciales para un nuevo usuario estén presentes
        if ($nombre && $correo && $password && $tipo_usuario && $id_rol) {
            // Instanciar el objeto Usuario con los datos recibidos.
            // Los parámetros se pasan en el orden exacto del constructor de tu clase Usuario.
            $usuario = new Usuario(
                $id_usuario,
                $nombre,
                $correo,
                $password,
                $tipo_usuario,
                (int)$id_rol, // Asegurar que id_rol sea int
                $fecha_creacion,
                $estado
            ); 

            if ($controlador->insertar($usuario)) {
                http_response_code(201); // Creado
                echo json_encode(["mensaje" => "Usuario agregado exitosamente"]);
            } else {
                http_response_code(500); // Error interno del servidor
                echo json_encode(["mensaje" => "Error al agregar el usuario"]);
            }
        } else {
            http_response_code(400); // Solicitud incorrecta
            echo json_encode(["mensaje" => "Datos incompletos para agregar usuario. Se requieren nombre, correo, password, tipo_usuario, id_rol."]);
        }
        break;

    case 'PUT':
        // Manejar solicitud PUT: Actualizar un usuario existente
        // Los datos para las solicitudes PUT suelen provenir del cuerpo JSON
        $datos = json_decode(file_get_contents("php://input"), true);
        
        // El ID debe venir en el cuerpo JSON para PUT
        $id_usuario = $datos['id_usuario'] ?? null; 

    
        $nombre = $datos['nombre'] ?? null;
        $correo = $datos['correo'] ?? null;
        $password = $datos['password'] ?? null; 
        $tipo_usuario = $datos['tipo_usuario'] ?? null;
        $id_rol = $datos['id_rol'] ?? null;
        $fecha_creacion = $datos['fecha_creacion'] ?? null; 
        $estado = $datos['estado'] ?? null; 

        // Validar que los campos esenciales para actualizar un usuario estén presentes
        if ($id_usuario && $nombre && $correo && $password && $tipo_usuario && $id_rol && $fecha_creacion !== null && $estado !== null) {
            // Instanciar el objeto Usuario con los datos actualizados.
            // Los parámetros se pasan en el orden exacto del constructor de tu clase Usuario.
            $usuario = new Usuario(
                (int)$id_usuario, // Asegurar que id_usuario sea int
                $nombre,
                $correo,
                $password,
                $tipo_usuario,
                (int)$id_rol, // Asegurar que id_rol sea int
                $fecha_creacion,
                (int)$estado // Asegurar que estado sea int
            ); 

            if ($controlador->actualizar($usuario)) {
                http_response_code(200); // OK
                echo json_encode(["mensaje" => "Usuario actualizado exitosamente"]);
            } else {
                http_response_code(500); // Error interno del servidor
                echo json_encode(["mensaje" => "Error al actualizar el usuario o usuario no encontrado"]);
            }
        } else {
            http_response_code(400); // Solicitud incorrecta
            echo json_encode(["mensaje" => "Datos incompletos o ID de usuario faltante para actualizar. Se requieren id_usuario, nombre, correo, password, tipo_usuario, id_rol, fecha_creacion, estado."]);
        }
        break;

    case 'DELETE':
        // Manejar solicitud DELETE: Eliminar un Usuario
        // El ID para DELETE se pasa típicamente en la URL o a veces en el cuerpo JSON
        $id = $_GET['id'] ?? null; // Intentar obtener el ID del parámetro de consulta de la URL

        // Si no se encuentra en la URL, intentar obtenerlo del cuerpo JSON 
        if (!$id) {
            $datos = json_decode(file_get_contents("php://input"), true);
            $id = $datos['id_usuario'] ?? null; // Usar 'id_usuario' si es lo que esperas en el JSON
        }

        if ($id) {
            if ($controlador->eliminar((int)$id)) {
                http_response_code(200); // OK
                echo json_encode(["mensaje" => "Usuario eliminado exitosamente"]);
            } else {
                http_response_code(500); // Error interno del servidor
                echo json_encode(["mensaje" => "Error al eliminar el usuario o usuario no encontrado"]);
            }
        } else {
            http_response_code(400); // Solicitud incorrecta
            echo json_encode(["mensaje" => "ID de usuario necesario para eliminar"]);
        }
        break;

    default:
        // Método no permitido
        http_response_code(405); 
        echo json_encode(["mensaje" => "Método HTTP no permitido"]);
        break;
}

?>