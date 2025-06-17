<?php

require_once __DIR__ . '/../modelo/Ingrediente.php';
require_once __DIR__ . '/../misc/Conexion.php';

class IngredienteDAO {

    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();

    }

    public function obtenerDatos() {
        $sql = "SELECT * FROM Grupo3_Ingrediente";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        $ingredientes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ingredientes[] = new Ingrediente(
                $row['id_ingrediente'],
                $row['nombre_ingrediente'],
                $row['descripcion'],
                $row['cantidad_stock'],
                $row['unidad']
            );
        }
        return $ingredientes;
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM Grupo3_Ingrediente WHERE id_ingrediente = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Ingrediente(
                $row['id_ingrediente'],
                $row['nombre_ingrediente'],
                $row['descripcion'],
                $row['cantidad_stock'],
                $row['unidad']
            );
        }
        return null;
    }

    public function insertar(Ingrediente $ing) {
        $sql = "INSERT INTO Grupo3_Ingrediente (nombre_ingrediente, descripcion, cantidad_stock, unidad)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $ing->nombre_ingrediente,
            $ing->descripcion,
            $ing->cantidad_stock,
            $ing->unidad
        ]);
    }

    public function actualizar(Ingrediente $ing) {
        $sql = "UPDATE Grupo3_Ingrediente
                SET nombre_ingrediente = ?, descripcion = ?, cantidad_stock = ?, unidad = ?
                WHERE id_ingrediente = ?";
        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $ing->nombre_ingrediente,
            $ing->descripcion,
            $ing->cantidad_stock,
            $ing->unidad,
            $ing->id_ingrediente
        ]);
    }

    public function eliminar($id) {
        $sql = "DELETE FROM Grupo3_Ingrediente WHERE id_ingrediente = ?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$id]);
    }
}
