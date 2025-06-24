<?php

require_once __DIR__.'/../misc/Conexion.php';
require_once __DIR__.'/../modelo/Platillo.php';

class PlatilloDAO {

    private $pdo;

    public function __construct(){
        $this->pdo = Conexion::conectar();
    }

    /**
     * Obtiene todos los registros de platillos de la base de datos.
     *
     * @return array Un array de objetos Platillo.
     */
    public function obtenerDatos(): array {
        $stmt = $this->pdo->query("SELECT * FROM Grupo3_Platillo");

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Platillo(
                $row['id_platillo'],
                $row['nombre_platillo'],
                $row['descripcion'],
                $row['precio'],
                $row['id_categoria'],
                $row['estado']
            );
        }
        return $result;
    }

    /**
     * Obtiene un platillo por su ID.
     *
     * @param int $id_platillo El ID del platillo a buscar.
     * @return Platillo|null Un objeto Platillo si se encuentra, o null si no.
     */
    public function obtenerPorId(int $id_platillo): ?Platillo {
        $stmt = $this->pdo->prepare("SELECT * FROM Grupo3_Platillo WHERE id_platillo = ?;");
        $stmt->execute([$id_platillo]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Platillo(
                $row['id_platillo'],
                $row['nombre_platillo'],
                $row['descripcion'],
                $row['precio'],
                $row['id_categoria'],
                $row['estado']
            );
        }
        return null; // Retorna null si no se encuentra el platillo
    }

    /**
     * Inserta un nuevo platillo en la base de datos.
     *
     * @param Platillo $objeto El objeto Platillo a insertar.
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    public function insertar(Platillo $objeto): bool {
        $sql = "INSERT INTO Grupo3_Platillo (nombre_platillo, descripcion, precio, id_categoria, estado) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->nombre_platillo,
            $objeto->descripcion,
            $objeto->precio,
            $objeto->id_categoria,
            $objeto->estado
        ]);
    }

    /**
     * Actualiza un platillo existente en la base de datos.
     *
     * @param Platillo $objeto El objeto Platillo con los datos actualizados.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizar(Platillo $objeto): bool {
        $sql = "UPDATE Grupo3_Platillo SET nombre_platillo = ?, descripcion = ?, precio = ?, id_categoria = ?, estado = ? WHERE id_platillo = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->nombre_platillo,
            $objeto->descripcion,
            $objeto->precio,
            $objeto->id_categoria,
            $objeto->estado,
            $objeto->id_platillo // El ID para identificar el registro a actualizar
        ]);
    }

    /**
     * Elimina un platillo de la base de datos por su ID.
     *
     * @param int $id_platillo El ID del platillo a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function eliminar(int $id_platillo): bool {
        try {
            // Preparar la sentencia para eliminar el platillo
            $stmt = $this->pdo->prepare("DELETE FROM Grupo3_Platillo WHERE id_platillo = :id");
            $stmt->bindParam(':id', $id_platillo, PDO::PARAM_INT);
            $stmt->execute();

            // Verificar si alguna fila fue afectada
            if ($stmt->rowCount() > 0) {
                return true; // Platillo eliminado exitosamente
            } else {
                return false; // No se encontró el platillo con ese ID
            }
        } catch (PDOException $e) {
            // En caso de error, se registra en el log
            error_log("Error al eliminar platillo: " . $e->getMessage());
            return false; // Retorna false si hubo un error
        }
    }
}
?>