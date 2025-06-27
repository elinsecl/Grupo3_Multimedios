<?php

require_once __DIR__.'/../misc/Conexion.php';
require_once __DIR__.'/../modelo/Cliente.php';

class ClienteDAO {

    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function obtenerDatos(): array {
        try {
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
        } catch (PDOException $e) {
            error_log("Error al obtener datos de clientes: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPorId(int $id_cliente): ?Cliente {
        try {
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
        } catch (PDOException $e) {
            error_log("Error al obtener cliente por ID: " . $e->getMessage());
            return null;
        }
    }

    public function insertar(Cliente $objeto): bool {
        try {
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
        } catch (PDOException $e) {
            error_log("Error al insertar cliente: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar(Cliente $objeto): bool {
        try {
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
        } catch (PDOException $e) {
            error_log("Error al actualizar cliente: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar(int $id_cliente): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM Grupo3_Cliente WHERE id_cliente = :id");
            $stmt->bindParam(':id', $id_cliente, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error al eliminar cliente: " . $e->getMessage());
            return false;
        }
    }
}
?>
