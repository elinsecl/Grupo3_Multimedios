<?php

require_once __DIR__.'/../misc/Conexion.php';
require_once __DIR__.'/../modelo/HistorialPedido.php';

class HistorialPedidoDAO {

    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function obtenerDatos(): array {
        $stmt = $this->pdo->query("SELECT * FROM Grupo3_Historial_Pedido");

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new HistorialPedido(
                $row['id_historial_pedido'],
                $row['pedido_id'],
                $row['fecha_entrega'],
                $row['estado_entrega']
            );
        }
        return $result;
    }

    public function obtenerPorId(int $pedido_id): ?HistorialPedido {
        $stmt = $this->pdo->prepare("SELECT * FROM Grupo3_Historial_Pedido WHERE pedido_id = ?");
        $stmt->execute([$pedido_id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new HistorialPedido(
                $row['id_historial_pedido'],
                $row['pedido_id'],
                $row['fecha_entrega'],
                $row['estado_entrega']
            );
        }
        return null;
    }

    public function insertar(HistorialPedido $objeto): bool {
        $sql = "INSERT INTO Grupo3_Historial_Pedido (id_historial_pedido, pedido_id, fecha_entrega, estado_entrega) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->id_historial_pedido,
            $objeto->pedido_id,
            $objeto->fecha_entrega,
            $objeto->estado_entrega
        ]);
    }

    public function eliminar(int $id_historial_pedido): bool {
        $sql = "DELETE FROM Grupo3_Historial_Pedido WHERE pedido_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_historial_pedido]);
    }
}
?>
