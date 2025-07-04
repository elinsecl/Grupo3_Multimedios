<?php

require_once __DIR__.'/../misc/Conexion.php';
require_once __DIR__.'/../modelo/Categoria.php';

class CategoriaDAO {

    private $pdo;

    public function __construct(){
        $this->pdo = Conexion::conectar();
    }

    /**
     * Obtiene todos los registros de categorías de la base de datos.
     *
     * @return array Un array de objetos Categoria.
     */
    public function obtenerDatos(): array {
        $stmt = $this->pdo->query("SELECT * FROM Grupo3_Categoria");

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Categoria(
                $row['id_categoria'],
                $row['nombre_categoria'],
                $row['descripcion']
            );
        }
        return $result;
    }

    /**
     * Obtiene una categoría por su ID.
     *
     * @param int $id_categoria El ID de la categoría a buscar.
     * @return Categoria|null Un objeto Categoria si se encuentra, o null si no.
     */
    public function obtenerPorId(int $id_categoria): ?Categoria {
        $stmt = $this->pdo->prepare("SELECT * FROM Grupo3_Categoria WHERE id_categoria = ?;");
        $stmt->execute([$id_categoria]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Categoria(
                $row['id_categoria'],
                $row['nombre_categoria'],
                $row['descripcion']
            );
        }
        return null;
    }

    /**
     * Obtiene una categoría por su nombre.
     *
     * @param string $nombre El nombre de la categoría a buscar.
     * @return Categoria|null Un objeto Categoria si se encuentra, o null si no.
     */
    public function obtenerPorNombre(string $nombre): ?Categoria {
        $stmt = $this->pdo->prepare("SELECT * FROM Grupo3_Categoria WHERE nombre_categoria = ?;");
        $stmt->execute([$nombre]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Categoria(
                $row['id_categoria'],
                $row['nombre_categoria'],
                $row['descripcion']
            );
        }
        return null;
    }

    /**
     * Verifica si existe una categoría con el nombre especificado.
     *
     * @param string $nombre El nombre de la categoría a verificar.
     * @return bool True si existe, false si no.
     */
    public function existeCategoriaConNombre(string $nombre): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Grupo3_Categoria WHERE nombre_categoria = ?");
        $stmt->execute([$nombre]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verifica si una categoría tiene platillos asociados.
     *
     * @param int $id_categoria El ID de la categoría a verificar.
     * @return bool True si tiene platillos asociados, false si no.
     */
    public function tienePlatillosAsociados(int $id_categoria): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Grupo3_Platillo WHERE id_categoria = ?");
        $stmt->execute([$id_categoria]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Inserta una nueva categoría en la base de datos.
     *
     * @param Categoria $objeto El objeto Categoria a insertar.
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    public function insertar(Categoria $objeto): bool {
        $sql = "INSERT INTO Grupo3_Categoria (nombre_categoria, descripcion) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->nombre_categoria,
            $objeto->descripcion
        ]);
    }

    /**
     * Actualiza una categoría existente en la base de datos.
     *
     * @param Categoria $objeto El objeto Categoria con los datos actualizados.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizar(Categoria $objeto): bool {
        $sql = "UPDATE Grupo3_Categoria SET nombre_categoria = ?, descripcion = ? WHERE id_categoria = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->nombre_categoria,
            $objeto->descripcion,
            $objeto->id_categoria
        ]);
    }

    /**
     * Elimina una categoría de la base de datos por su ID.
     *
     * @param int $id_categoria El ID de la categoría a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function eliminar(int $id_categoria): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM Grupo3_Categoria WHERE id_categoria = :id");
            $stmt->bindParam(':id', $id_categoria, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error al eliminar categoría: " . $e->getMessage());
            return false;
        }
    }
}
?>