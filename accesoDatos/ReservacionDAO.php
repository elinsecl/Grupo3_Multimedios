<?php

require_once __DIR__ . '/../modelo/Reservacion.php';
require_once __DIR__ . '/../misc/Conexion.php';

class ReservacionDAO {

    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();

    }

    public function obtenerDatos() {
        $sql = "SELECT * FROM Grupo3_Reservacion";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        $reservaciones = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reservaciones[] = new Reservacion(
                $row['id_reservacion'],
                $row['cliente_id'],
                $row['mesa_id'],
                $row['fecha_reserva'],
                $row['hora_reserva'],
                $row['cantidad_personas'],
                $row['estado']
            );
        }
        return $reservaciones;
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM Grupo3_Reservacion WHERE id_reservacion = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Reservacion(
                $row['id_reservacion'],
                $row['cliente_id'],
                $row['mesa_id'],
                $row['fecha_reserva'],
                $row['hora_reserva'],
                $row['cantidad_personas'],
                $row['estado']
            );
        }
        return null;
    }

    public function insertar(Reservacion $res) {
        $sql = "INSERT INTO Grupo3_Reservacion (cliente_id, mesa_id, fecha_reserva, hora_reserva, cantidad_personas, estado)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $res->cliente_id,
            $res->mesa_id,
            $res->fecha_reserva,
            $res->hora_reserva,
            $res->cantidad_personas,
            $res->estado
        ]);
    }

    public function actualizar(Reservacion $res) {
        $sql = "UPDATE Grupo3_Reservacion
                SET cliente_id = ?, mesa_id = ?, fecha_reserva = ?, hora_reserva = ?, cantidad_personas = ?, estado = ?
                WHERE id_reservacion = ?";
        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute([
            $res->cliente_id,
            $res->mesa_id,
            $res->fecha_reserva,
            $res->hora_reserva,
            $res->cantidad_personas,
            $res->estado,
            $res->id_reservacion
        ]);
    }

    public function eliminar($id) {
        $sql = "DELETE FROM Grupo3_Reservacion WHERE id_reservacion = ?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$id]);
    }
}
