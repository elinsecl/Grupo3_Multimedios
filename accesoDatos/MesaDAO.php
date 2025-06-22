<?php

require_once __DIR__.'/../misc/Conexion.php';
require_once __DIR__.'/../modelo/Mesa.php';

class MesaDao {

    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function obtenerDatos(): array {
        try {
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
        } catch (PDOException $e) {
            error_log("Error al obtener datos de mesas: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPorId(int $id_mesa): ?Mesa {
        try {
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
        } catch (PDOException $e) {
            error_log("Error al obtener mesa por ID: " . $e->getMessage());
            return null;
        }
    }

    public function insertar(Mesa $objeto): bool {
        try {
            $sql = "INSERT INTO Grupo3_Mesa (numero_mesa, capacidad, ubicacion, estado) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $objeto->numero_mesa,
                $objeto->capacidad,
                $objeto->ubicacion,
                $objeto->estado
            ]);
        } catch (PDOException $e) {
            error_log("Error al insertar mesa: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar(Mesa $objeto): bool {
        try {
            $sql = "UPDATE Grupo3_Mesa SET numero_mesa = ?, capacidad = ?, ubicacion = ?, estado = ? WHERE id_mesa = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $objeto->numero_mesa,
                $objeto->capacidad,
                $objeto->ubicacion,
                $objeto->estado,
                $objeto->id_mesa
            ]);
        } catch (PDOException $e) {
            error_log("Error al actualizar mesa: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar(int $id_mesa): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM Grupo3_Mesa WHERE id_mesa = :id");
            $stmt->bindParam(':id', $id_mesa, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error al eliminar mesa: " . $e->getMessage());
            return false;
        }
    }
}
?>
