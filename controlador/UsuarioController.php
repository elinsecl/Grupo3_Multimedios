<?php

require_once __DIR__.'/../accessoDatos/ClientesDAO.php';
require_once __DIR__.'/../models/Clientes.php'; // Asegúrate de incluir la clase Clientes

class ClientesController{

    private $dao;

    public function __construct(){
        $this->dao = new ClientesDAO();
    }

    /**
     * Obtiene todos los clientes.
     * @return array Un array de objetos Clientes.
     */
    public function obtenerDatos(): array {
        return $this->dao->obtenerDatos();
    }

    /**
     * Obtiene un cliente por su ID.
     * @param int $id El ID del cliente a buscar.
     * @return Clientes|null Un objeto Clientes si se encuentra, o null.
     */
    public function obtenerPorId(int $id): ?Clientes {
        return $this->dao->obtenerPorId($id);
    }

    /**
     * Inserta un nuevo cliente.
     * @param Clientes $objeto El objeto Clientes a insertar.
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    public function insertar(Clientes $objeto): bool {
        return $this->dao->insertar($objeto);
    }

    ---

    ## Método para Actualizar (PUT)

    /**
     * Actualiza un cliente existente.
     * @param Clientes $objeto El objeto Clientes con los datos actualizados.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizar(Clientes $objeto): bool {
        return $this->dao->actualizar($objeto);
    }
   

    ## Método para Eliminar (DELETE)

    
    /**
     * Elimina un cliente por su ID.
     * @param int $id El ID del cliente a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function eliminar(int $id): bool {
        return $this->dao->eliminar($id);
    }


}

## Manejo de Solicitudes HTTP (GET, POST, PUT, DELETE)

// Este bloque de código iría en un archivo de entrada (por ejemplo, index.php o clientes.php)
// que maneja las solicitudes HTTP.

$controlador = new ClientesController();

// Establecer el encabezado JSON para las respuestas de la API
header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Manejar solicitud GET: Obtener todos o uno por ID
        $id = $_GET['id'] ?? null; // Obtener ID de la URL (e.g., ?id=1)
        if ($id) {
            $cliente = $controlador->obtenerPorId((int)$id);
            if ($cliente) {
                echo json_encode($cliente);
            } else {
                http_response_code(404); // No encontrado
                echo json_encode(["mensaje" => "Cliente no encontrado"]);
            }
        } else {
            $clientes = $controlador->obtenerDatos();
            echo json_encode($clientes);
        }
        break;

    case 'POST':
        // Manejar solicitud POST: Insertar un nuevo cliente
        // Los datos para POST generalmente vienen del cuerpo de la solicitud (form-urlencoded o JSON)
        $nombre = $_POST['nombre'] ?? null;
        $apellidos = $_POST['apellidos'] ?? null;
        $telefono = $_POST['telefono'] ?? null;

        // Si esperas JSON en el cuerpo para POST (como en APIs RESTful), usarías:
        // $datos = json_decode(file_get_contents("php://input"), true);
        // $nombre = $datos['nombre'] ?? null;
        // $apellidos = $datos['apellidos'] ?? null;
        // $telefono = $datos['telefono'] ?? null;


        if ($nombre && $apellidos && $telefono) {
            $cliente = new Clientes(null, $nombre, $apellidos, $telefono);
            if ($controlador->insertar($cliente)) {
                http_response_code(201); // Creado
                echo json_encode(["mensaje" => "Cliente agregado exitosamente"]);
            } else {
                http_response_code(500); // Error interno del servidor
                echo json_encode(["mensaje" => "Error al agregar el cliente"]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "Datos incompletos para agregar cliente"]);
        }
        break;

    case 'PUT':
        // Manejar solicitud PUT: Actualizar un cliente existente
        // Para PUT, los datos suelen venir del cuerpo de la solicitud como JSON
        $datos = json_decode(file_get_contents("php://input"), true);
        $id = $datos['id'] ?? null; // El ID debe venir en el cuerpo del JSON para PUT
        $nombre = $datos['nombre'] ?? null;
        $apellidos = $datos['apellidos'] ?? null;
        $telefono = $datos['telefono'] ?? null;

        if ($id && $nombre && $apellidos && $telefono) {
            $cliente = new Clientes((int)$id, $nombre, $apellidos, $telefono);
            if ($controlador->actualizar($cliente)) {
                http_response_code(200); // OK
                echo json_encode(["mensaje" => "Cliente actualizado exitosamente"]);
            } else {
                http_response_code(500); // Error interno del servidor
                echo json_encode(["mensaje" => "Error al actualizar el cliente o cliente no encontrado"]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "Datos incompletos o ID de cliente faltante para actualizar"]);
        }
        break;

    case 'DELETE':
        // Manejar solicitud DELETE: Eliminar un cliente
        // El ID para DELETE generalmente se pasa en la URL o en el cuerpo del JSON
        $id = $_GET['id'] ?? null; // Intentar obtener ID de la URL

        if ($id) {
            if ($controlador->eliminar((int)$id)) {
                http_response_code(200); // OK
                echo json_encode(["mensaje" => "Cliente eliminado exitosamente"]);
            } else {
                http_response_code(500); // Error interno del servidor
                echo json_encode(["mensaje" => "Error al eliminar el cliente o cliente no encontrado"]);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(["mensaje" => "ID de cliente necesario para eliminar"]);
        }
        break;

    default:
        // Método no permitido
        http_response_code(405); // Method Not Allowed
        echo json_encode(["mensaje" => "Método HTTP no permitido"]);
        break;
}

?>