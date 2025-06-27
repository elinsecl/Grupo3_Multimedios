<?php

require_once __DIR__.'/../misc/Conexion.php';
require_once __DIR__.'/../modelo/Pedido.php';

class PedidoDao {
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
                $row['id_usuario'],
                $row['fecha_pedido'],
                $row['estado']
            );
        }
        return $result;
    }

    public function obtenerPorId(int $id_pedido): ?Pedido {
        $stmt = $this->pdo->prepare("SELECT * FROM Grupo3_Pedido WHERE id_pedido = ?");
        $stmt->execute([$id_pedido]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Pedido(
            $row['id_pedido'],
            $row['id_usuario'],
            $row['fecha_pedido'],
            $row['estado']
        ) : null;
    }

    public function insertar(Pedido $pedido): bool {
        $sql = "INSERT INTO Grupo3_Pedido (id_usuario, fecha_pedido, estado) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $pedido->id_usuario,
            $pedido->fecha_pedido,
            $pedido->estado
        ]);
    }

     public function insertar2(Pedido $pedido): int|false {
        $sql = "INSERT INTO Grupo3_Pedido (id_usuario, fecha_pedido, estado) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $resultado = $stmt->execute([
            $pedido->id_usuario,
            (!empty($pedido->fecha_pedido) && trim($pedido->fecha_pedido) !== '') ? $pedido->fecha_pedido  : date('Y-m-d H:i:s'),
            $pedido->estado
        ]);

        if ($resultado) {
            return (int)$this->pdo->lastInsertId();
        }

        return true;
    }
    
    public function actualizar(Pedido $pedido): bool {
        $sql = "UPDATE Grupo3_Pedido SET id_usuario = ?, fecha_pedido = ?, estado = ? WHERE id_pedido = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $pedido->id_usuario,
            $pedido->fecha_pedido,
            $pedido->estado,
            $pedido->id_pedido
        ]);
    }

    public function eliminar(int $id_pedido): bool {
        $stmt = $this->pdo->prepare("DELETE FROM Grupo3_Pedido WHERE id_pedido = ?");
        $stmt->execute([$id_pedido]);
        return $stmt->rowCount() > 0;
    }
}

