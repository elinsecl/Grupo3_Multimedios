<?php
require_once __DIR__.'/../modelo/Inventario.php';
require_once __DIR__.'/../misc/Conexion.php';

class InventarioDAO {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function obtenerDatos(): array {
        $stmt = $this->pdo->query("SELECT * FROM Grupo3_Inventario");
        $result = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Inventario(
                $row['id_inventario'],
                $row['ingrediente_id'],
                $row['cantidad_stock'],
                $row['fecha_entrada'],
                $row['proveedor_id']
            );
        }
        return $result;
    }

    public function obtenerPorId(int $id): ?Inventario {
        $stmt = $this->pdo->prepare("SELECT * FROM Grupo3_Inventario WHERE id_inventario = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Inventario(
                $row['id_inventario'],
                $row['ingrediente_id'],
                $row['cantidad_stock'],
                $row['fecha_entrada'],
                $row['proveedor_id']
            );
        }
        return null;
    }

    public function insertar(Inventario $obj): bool {
        $sql = "INSERT INTO Grupo3_Inventario (ingrediente_id, cantidad_stock, fecha_entrada, proveedor_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $obj->ingrediente_id,
            $obj->cantidad_stock,
            $obj->fecha_entrada ?? date('Y-m-d H:i:s'),
            $obj->proveedor_id
        ]);
    }

    public function actualizar(Inventario $obj): bool {
        $sql = "UPDATE Grupo3_Inventario SET ingrediente_id = ?, cantidad_stock = ?, fecha_entrada = ?, proveedor_id = ? WHERE id_inventario = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $obj->ingrediente_id,
            $obj->cantidad_stock,
            $obj->fecha_entrada,
            $obj->proveedor_id,
            $obj->id_inventario
        ]);
    }

    public function eliminar(int $id): bool {
        $sql = "DELETE FROM Grupo3_Inventario WHERE id_inventario = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
