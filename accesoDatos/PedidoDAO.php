<?php

require_once __DIR__.'/../misc/Conexion.php';
require_once __DIR__.'/../modelo/Pedido.php';

class PedidoDAO {

    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function obtenerDatos(): array {
        $stmt = $this->pdo->query("SELECT * FROM Grupo3_Pedido");
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Pedido(
                $row['id_pedido'],
                $row['cliente_id'],
                $row['mesa_id'],
                $row['fecha_pedido'],
                $row['hora_pedido'],
                $row['total'],
                $row['estado'],
                $row['metodo_pago']
            );
        }
        return $result;
    }

    public function obtenerPorId(int $id_pedido): ?Pedido {
        $stmt = $this->pdo->prepare("SELECT * FROM Grupo3_Pedido WHERE id_pedido = ?");
        $stmt->execute([$id_pedido]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Pedido(
                $row['id_pedido'],
                $row['cliente_id'],
                $row['mesa_id'],
                $row['fecha_pedido'],
                $row['hora_pedido'],
                $row['total'],
                $row['estado'],
                $row['metodo_pago']
            );
        }
        return null;
    }

    public function insertar(Pedido $objeto): bool {
        $sql = "INSERT INTO Grupo3_Pedido (cliente_id, mesa_id, fecha_pedido, hora_pedido, total, estado, metodo_pago) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->cliente_id,
            $objeto->mesa_id,
            $objeto->fecha_pedido,
            $objeto->hora_pedido,
            $objeto->total,
            $objeto->estado,
            $objeto->metodo_pago
        ]);
        
    }

    public function insertar2(Pedido $objeto): int|false {
        $sql = "INSERT INTO Grupo3_Pedido (cliente_id, mesa_id, fecha_pedido, hora_pedido, total, estado, metodo_pago) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $resultado = $stmt->execute([
            $objeto->cliente_id,
            $objeto->mesa_id,
            $objeto->fecha_pedido,
            $objeto->hora_pedido,
            $objeto->total,
            $objeto->estado,
            $objeto->metodo_pago
        ]);

        if ($resultado) {
            return $this->pdo->lastInsertId();
        }

        return true;
    }

    public function actualizar(Pedido $objeto): bool {
        $sql = "UPDATE Grupo3_Pedido SET cliente_id = ?, mesa_id = ?, fecha_pedido = ?, hora_pedido = ?, total = ?, estado = ?, metodo_pago = ? WHERE id_pedido = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->cliente_id,
            $objeto->mesa_id,
            $objeto->fecha_pedido,
            $objeto->hora_pedido,
            $objeto->total,
            $objeto->estado,
            $objeto->metodo_pago,
            $objeto->id_pedido
        ]);
    }

    public function eliminar(int $id_pedido): bool {
        $sql = "DELETE FROM Grupo3_Pedido WHERE id_pedido = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_pedido]);
    }
}
