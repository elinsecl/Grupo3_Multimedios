<?php

require_once __DIR__.'/../misc/Conexion.php';
require_once __DIR__.'/../modelo/Historialinventario.php';

class HistorialInventarioDAO {

    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function obtenerDatos(): array {
        $stmt = $this->pdo->query("SELECT * FROM Grupo3_Historial_Inventario");

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new HistorialInventario(
                $row['id_historial_inventario'],
                $row['id_inventario'],
                $row['ingrediente_id'],
                $row['cambio_stock'],
                $row['fecha'],
                $row['tipo_cambio']
            );
        }
        return $result;
    }

    public function obtenerPorId(int $id_historial_inventario): ?HistorialInventario {
        $stmt = $this->pdo->prepare("SELECT * FROM Grupo3_Historial_Inventario WHERE id_historial_inventario = ?");
        $stmt->execute([$id_historial_inventario]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new HistorialInventario(
                $row['id_historial_inventario'],
                $row['id_inventario'],
                $row['ingrediente_id'],
                $row['cambio_stock'],
                $row['fecha'],
                $row['tipo_cambio']
            );
        }
        return null;
    }

    public function obtenerPorIdIngrediente(int $ingrediente_id) :  array {

         $stmt = $this->pdo->query("SELECT * FROM Grupo3_Historial_Inventario Where ingrediente_id = ?");

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new HistorialInventario(
                $row['id_historial_inventario'],
                $row['id_inventario'],
                $row['ingrediente_id'],
                $row['cambio_stock'],
                $row['fecha'],
                $row['tipo_cambio']
            );
        }
        return $result;
    }    

    public function insertar(HistorialInventario $objeto): bool {
        $sql = "INSERT INTO Grupo3_Historial_Inventario (id_inventario, ingrediente_id, cambio_stock, fecha,tipo_cambio) VALUES ( ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->id_inventario,
            $objeto->ingrediente_id,
            $objeto->cambio_stock,
            $objeto->fecha,
            $objeto->tipo_cambio
        ]);
    }

    public function eliminar(int $id_historial_inventario): bool {
        $sql = "DELETE FROM Grupo3_Historial_Inventario WHERE id_historial_inventario = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_historial_inventario]);
    }
}
?>
