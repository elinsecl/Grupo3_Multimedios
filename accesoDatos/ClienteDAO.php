<?php

require_once __DIR__.'/../misc/Conexion.php';
require_once __DIR__.'/../modelo/Cliente.php';

class ClienteDao {

    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function obtenerDatos(): array {
        $stmt = $this->pdo->query("SELECT * FROM Grupo3_Cliente");

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Cliente(
                $row['id_cliente'],
                $row['nombre'],
                $row['telefono'],
                $row['correo'],
                $row['direccion'],
                $row['fecha_registro'],
                $row['estado']
            );
        }
        return $result;
    }

    public function obtenerPorId(int $id_cliente): ?Cliente {
        $stmt = $this->pdo->prepare("SELECT * FROM Grupo3_Cliente WHERE id_cliente = ?");
        $stmt->execute([$id_cliente]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Cliente(
                $row['id_cliente'],
                $row['nombre'],
                $row['telefono'],
                $row['correo'],
                $row['direccion'],
                $row['fecha_registro'],
                $row['estado']
            );
        }
        return null;
    }

    public function insertar(Cliente $objeto): bool {
        $sql = "INSERT INTO Grupo3_Cliente (nombre, telefono, correo, direccion, fecha_registro, estado) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->nombre,
            $objeto->telefono,
            $objeto->correo,
            $objeto->direccion,
            $objeto->fecha_registro,
            $objeto->estado
        ]);
    }

    public function actualizar(Cliente $objeto): bool {
        $sql = "UPDATE Grupo3_Cliente SET nombre = ?, telefono = ?, correo = ?, direccion = ?, fecha_registro = ?, estado = ? WHERE id_cliente = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->nombre,
            $objeto->telefono,
            $objeto->correo,
            $objeto->direccion,
            $objeto->fecha_registro,
            $objeto->estado,
            $objeto->id_cliente
        ]);
    }

    public function eliminar(int $id_cliente): bool {
        $sql = "DELETE FROM Grupo3_Cliente WHERE id_cliente = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_cliente]);
    }
}
?>
