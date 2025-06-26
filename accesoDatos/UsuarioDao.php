<?php


require_once __DIR__.'/../misc/Conexion.php';
require_once __DIR__.'/../modelo/Usuario.php'; 

/**
 * Clase de Acceso a Datos (DAO) para la entidad Usuario.
 * Proporciona métodos para interactuar con la tabla 'Grupo3_Usuario' en la base de datos.
 */
class UsuarioDao {

    private $pdo; // Objeto PDO para la conexión a la base de datos

    /**
     * Constructor de la clase UsuarioDao.
     * Establece la conexión a la base de datos usando la clase Conexion.
     */
    public function __construct(){
        $this->pdo = Conexion::conectar();
    }

    /**
     * Obtiene todos los registros de usuarios de la base de datos.
     *
     * @return array Un array de objetos Usuario. Puede estar vacío si no hay usuarios.
     */
    public function obtenerDatos(): array {
        $stmt = $this->pdo->query("SELECT id_usuario, nombre, correo, password, id_rol, fecha_creacion, estado FROM Grupo3_Usuario"); 
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            // Crea un objeto Usuario por cada fila obtenida
            $result[] = new Usuario(
                $row['id_usuario'],
                $row['nombre'],
                $row['correo'],
                $row['password'],
                $row['id_rol'],
                $row['fecha_creacion'],
                $row['estado']
            );
        }
        return $result;
    }

    /**
     * Obtiene un usuario por su ID.
     *
     * @param int $id_usuario El ID del usuario a buscar.
     * @return Usuario|null Un objeto Usuario si se encuentra, o null si no.
     */
    public function obtenerPorId(int $id_usuario): ?Usuario {
        $stmt = $this->pdo->prepare("SELECT id_usuario, nombre, correo, password, id_rol, fecha_creacion, estado FROM Grupo3_Usuario WHERE id_usuario = ?;");
        $stmt->execute([$id_usuario]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            // Retorna un objeto Usuario con los datos obtenidos
            return new Usuario(
                $row['id_usuario'],
                $row['nombre'],
                $row['correo'],
                $row['password'],
                $row['id_rol'],
                $row['fecha_creacion'],
                $row['estado']
            );
        }
        return null; // Retorna null si no se encuentra el usuario
    }
 
    /**
     * Inserta un nuevo usuario en la base de datos.
     * Se asume que la contraseña ya viene hasheada desde el controlador.
     *
     * @param Usuario $objeto El objeto Usuario a insertar.
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    public function insertar(Usuario $objeto): bool {
        try {
            $sql = "INSERT INTO Grupo3_Usuario (nombre, correo, password, id_rol, fecha_creacion, estado) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $objeto->nombre,
                $objeto->correo,
                $objeto->password, 
                $objeto->id_rol,
                $objeto->fecha_creacion,
                $objeto->estado
            ]);
        } catch (PDOException $e) {
            error_log("Error al insertar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualiza un usuario existente en la base de datos.
     * La contraseña se actualiza solo si se proporciona una nueva (ya hasheada desde el controlador).
     *
     * @param Usuario $objeto El objeto Usuario con los datos actualizados.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizar(Usuario $objeto): bool {
        try {
            $sql = "UPDATE Grupo3_Usuario SET nombre = ?, correo = ?, id_rol = ?, fecha_creacion = ?, estado = ?";
            $params = [
                $objeto->nombre,
                $objeto->correo,
                $objeto->id_rol,
                $objeto->fecha_creacion,
                $objeto->estado
            ];

            // Si se proporciona una nueva contraseña (no vacía), la incluimos en la actualización
            // Se asume que $objeto->password ya viene hasheada desde el controlador si es nueva.
            if ($objeto->password !== null && $objeto->password !== '') {
                $sql .= ", password = ?";
                $params[] = $objeto->password; // Agrega la nueva contraseña hasheada
            }

            $sql .= " WHERE id_usuario = ?";
            $params[] = $objeto->id_usuario; // Agrega el ID del usuario al final de los parámetros

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params); // Ejecuta con todos los parámetros
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica si un correo electrónico ya existe en la base de datos,
     * excluyendo opcionalmente a un usuario específico (para actualizaciones).
     *
     * @param string $correo El correo electrónico a verificar.
     * @param int|null $id_usuario El ID del usuario a excluir de la comprobación (si se está editando a sí mismo).
     * @return bool True si el correo ya existe (para otro usuario si se excluye), false en caso contrario.
     */
    public function correoExiste(string $correo, ?int $id_usuario = null): bool {
        try {
            $sql = "SELECT COUNT(*) FROM Grupo3_Usuario WHERE correo = ?";
            $params = [$correo];

            // Si se proporciona un ID para excluir, se agrega a la consulta y a los parámetros
            if ($id_usuario !== null) {
                $sql .= " AND id_usuario != ?";
                $params[] = $id_usuario;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params); // Ejecuta con los parámetros
            $count = $stmt->fetchColumn(); // Obtiene el valor de la primera columna (COUNT(*))

            return $count > 0; // Si count es mayor que 0, el correo ya existe
        } catch (PDOException $e) {
            error_log("Error al verificar si el correo existe: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina un usuario de la base de datos por su ID.
     *
     * @param int $id_usuario El ID del usuario a eliminar.
     * @return bool True si la eliminación fue exitosa (se afectó al menos una fila), false en caso contrario.
     */
    public function eliminar(int $id_usuario): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM Grupo3_Usuario WHERE id_usuario = :id");
            $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            // Si 1 fila fue afectada, significa que se eliminó un usuario.
            // Si 0 filas fueron afectadas, significa que no se encontró ningún usuario con esa ID.
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            // Registra el error para fines de depuración
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }
}

?>
