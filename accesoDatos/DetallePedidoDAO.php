<?php

require_once __DIR__ . '/../modelo/DetallePedido.php';
require_once __DIR__ . '/../misc/Conexion.php';

class DetallePedidoDAO {

    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    public function obtenerTodos() {
        $sql = "SELECT * FROM Grupo3_Detalle_Pedido";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_detalle) {
        $sql = "SELECT * FROM Grupo3_Detalle_Pedido WHERE id_detalle = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_detalle]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPorPedido($id_pedido) {
        $sql = "SELECT * FROM Grupo3_Detalle_Pedido WHERE id_pedido = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_pedido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertar(DetallePedido $detalle) {
        $sql = "INSERT INTO Grupo3_Detalle_Pedido (id_pedido, id_platillo, cantidad, subtotal) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            $detalle->id_pedido,
            $detalle->id_platillo,
            $detalle->cantidad,
            $detalle->subtotal
        ]);
    }

    public function actualizar(DetallePedido $detalle) {
        $sql = "UPDATE Grupo3_Detalle_Pedido SET id_pedido = ?, id_platillo = ?, cantidad = ?, subtotal = ? WHERE id_detalle = ?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            $detalle->id_pedido,
            $detalle->id_platillo,
            $detalle->cantidad,
            $detalle->subtotal,
            $detalle->id_detalle
        ]);
    }

    public function eliminar($id_detalle) {
        $sql = "DELETE FROM Grupo3_Detalle_Pedido WHERE id_detalle = ?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$id_detalle]);
    }
}
