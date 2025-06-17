<?php

require_once __DIR__.'/../misc/Conexion.php';
require_once __DIR__.'/../modelo/Mesa.php';

class MesaDao {

    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function obtenerDatos(): array {
        $stmt = $this->pdo->query("SELECT * FROM Grupo3_Mesa");

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Mesa(
                $row['id_mesa'],
                $row['numero_mesa'],
                $row['capacidad'],
                $row['ubicacion'],
                $row['estado']
            );
        }
        return $result;
    }

    public function obtenerPorId(int $id_mesa): ?Mesa {
        $stmt = $this->pdo->prepare("SELECT * FROM Grupo3_Mesa WHERE id_mesa = ?");
        $stmt->execute([$id_mesa]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Mesa(
                $row['id_mesa'],
                $row['numero_mesa'],
                $row['capacidad'],
                $row['ubicacion'],
                $row['estado']
            );
        }
        return null;
    }

    public function insertar(Mesa $objeto): bool {
        $sql = "INSERT INTO Grupo3_Mesa (numero_mesa, capacidad, ubicacion, estado) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->numero_mesa,
            $objeto->capacidad,
            $objeto->ubicacion,
            $objeto->estado
        ]);
    }

    public function actualizar(Mesa $objeto): bool {
        $sql = "UPDATE Grupo3_Mesa SET numero_mesa = ?, capacidad = ?, ubicacion = ?, estado = ? WHERE id_mesa = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $objeto->numero_mesa,
            $objeto->capacidad,
            $objeto->ubicacion,
            $objeto->estado,
            $objeto->id_mesa
        ]);
    }

    public function eliminar(int $id_mesa): bool {
        $sql = "DELETE FROM Grupo3_Mesa WHERE id_mesa = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id_mesa]);
    }
}
?>
