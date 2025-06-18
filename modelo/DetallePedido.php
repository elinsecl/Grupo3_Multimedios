<?php
class DetallePedido {
    public $id_detalle;
    public $id_pedido;
    public $id_platillo;
    public $cantidad;
    public $subtotal;

    public function __construct($id_detalle, $id_pedido, $id_platillo, $cantidad, $subtotal) {
        $this->id_detalle = $id_detalle;
        $this->id_pedido = $id_pedido;
        $this->id_platillo = $id_platillo;
        $this->cantidad = $cantidad;
        $this->subtotal = $subtotal;
    }
}
