<?php

require_once __DIR__.'/../misc/Conexion.php';
require_once __DIR__.'/../modelo/Rol.php';

class RolDAO {

    private $pdo;

    public function __construct(){
        $this->pdo = Conexion::conectar();
    }

    /**
     * Obtiene todos los registros de roles de la base de datos.
     *
     * @return array Un array de objetos Rol.
     */
    public function obtenerDatos(): array {
        $stmt = $this->pdo->query("SELECT * FROM Grupo3_Rol"); 

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            
            $result[] = new Rol(
                $row['id_rol'],
                $row['nombre_rol'],
                $row['descripcion']
            );
        }
        return $result;
    }

    ## Operación de Lectura (Read): `obtenerPorId()`

    /**
     * Obtiene un rol por su ID.
     *
     * @param int $id_rol El ID del rol a buscar.
     * @return Rol|null Un objeto Rol si se encuentra, o null si no.
     */
    public function obtenerPorId(int $id_rol): ?Rol {
        $stmt = $this->pdo->prepare("SELECT * FROM Grupo3_Rol WHERE id_rol = ?;");
        $stmt->execute([$id_rol]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Rol(
                $row['id_rol'],
                $row['nombre_rol'],
                $row['descripcion']
            );
        }
        return null; // Retorna null si no se encuentra el rol
    }
 
    ## Operación de Creación (Create): `insertar()`

    /**
     * Inserta un nuevo rol en la base de datos.
     *
     * @param Rol $objeto El objeto Rol a insertar.
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    public function insertar(Rol $objeto): bool {
        $sql = "INSERT INTO Grupo3_Rol (nombre_rol, descripcion) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->nombre_rol,
            $objeto->descripcion
        ]);
    }
    

    ## Operación de Actualización (Update): `actualizar()`

    /**
     * Actualiza un rol existente en la base de datos.
     *
     * @param Rol $objeto El objeto Rol con los datos actualizados.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizar(Rol $objeto): bool {
        $sql = "UPDATE Grupo3_Rol SET nombre_rol = ?, descripcion = ? WHERE id_rol = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->nombre_rol,
            $objeto->descripcion,
            $objeto->id_rol // El ID para identificar el registro a actualizar
        ]);
    }
 
    ## Operación de Eliminación (Delete): `eliminar()`

    /**
     * Elimina un rol de la base de datos por su ID.
     *
     * @param int $id_rol El ID del rol a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
   public function eliminar(int $id_rol): bool {
        try {
            $sql = "DELETE FROM Grupo3_Rol WHERE id_rol = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_rol]);

            // Si devuelve 0, significa que no se encontró un rol con esa ID y, por lo tanto, no se eliminó.
            // Si devuelve 1 (o más, aunque para una eliminación por ID debería ser 1), significa éxito.
            if ($stmt->rowCount() > 0) {
                return true; // Se eliminó el rol
            } else {
                return false; // No se encontró el rol con esa ID o no se afectaron filas
            }
        } catch (PDOException $e) {
            error_log("Error al eliminar rol con ID " . $id_rol . ": " . $e->getMessage());
            return false; 
        }
    }

}

