<?php

require_once __DIR__.'/../misc/Conexion.php';
require_once __DIR__.'/../modelo/Usuario.php'; 

class UsuarioDao {

    private $pdo;

    public function __construct(){
        $this->pdo = Conexion::conectar();
    }


    /**
     * Obtiene todos los registros de usuarios de la base de datos.
     *
     * @return array Un array de objetos Usuario.
     */
    public function obtenerDatos(): array {
        $stmt = $this->pdo->query("SELECT * FROM Grupo3_Usuario"); 

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            
            $result[] = new Usuario(
                $row['id_usuario'],
                $row['nombre'],
                $row['correo'],
                $row['password'],
                $row['tipo_usuario'],
                $row['id_rol'],
                $row['fecha_creacion'],
                $row['estado']
            );
        }
        return $result;
    }

    ## Operación de Lectura (Read): `obtenerPorId()`

    /**
     * Obtiene un usuario por su ID.
     *
     * @param int $id_usuario El ID del usuario a buscar.
     * @return Usuario|null Un objeto Usuario si se encuentra, o null si no.
     */
    public function obtenerPorId(int $id_usuario): ?Usuario {
        $stmt = $this->pdo->prepare("SELECT * FROM Grupo3_Usuario WHERE id_usuario = ?;");
        $stmt->execute([$id_usuario]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Usuario(
                $row['id_usuario'],
                $row['nombre'],
                $row['correo'],
                $row['password'],
                $row['tipo_usuario'],
                $row['id_rol'],
                $row['fecha_creacion'],
                $row['estado']
            );
        }
        return null; // Retorna null si no se encuentra el usuario
    }
  
    ## Operación de Creación (Create): `insertar()`

    /**
     * Inserta un nuevo usuario en la base de datos.
     *
     * @param Usuario $objeto El objeto Usuario a insertar.
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    public function insertar(Usuario $objeto): bool {
        $sql = "INSERT INTO Grupo3_Usuario (nombre, correo, password, tipo_usuario, id_rol, fecha_creacion, estado) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->nombre,
            $objeto->correo,
            $objeto->password,
            $objeto->tipo_usuario,
            $objeto->id_rol,
            $objeto->fecha_creacion,
            $objeto->estado
        ]);
    }
   

    ## Operación de Actualización (Update): `actualizar()`

    /**
     * Actualiza un usuario existente en la base de datos.
     *
     * @param Usuario $objeto El objeto Usuario con los datos actualizados.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizar(Usuario $objeto): bool {
        $sql = "UPDATE Grupo3_Usuario SET nombre = ?, correo = ?, password = ?, tipo_usuario = ?, id_rol = ?, fecha_creacion = ?, estado = ? WHERE id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->nombre,
            $objeto->correo,
            $objeto->password,
            $objeto->tipo_usuario,
            $objeto->id_rol,
            $objeto->fecha_creacion,
            $objeto->estado,
            $objeto->id_usuario // El ID para identificar el registro a actualizar
        ]);
    }
 
    ## Operación de Eliminación (Delete): `eliminar()`

    /**
     * Elimina un usuario de la base de datos por su ID.
     *
     * @param int $id_usuario El ID del usuario a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function eliminar(int $id_usuario): bool {
        $sql = "DELETE FROM Grupo3_Usuario WHERE id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_usuario]);
    }

}

?>