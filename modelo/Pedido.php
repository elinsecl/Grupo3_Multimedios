<?php

class Pedido {
    public $id_pedido;
    public $cliente_id;
    public $mesa_id;
    public $fecha_pedido;
    public $hora_pedido;
    public $total;
    public $estado;
    public $metodo_pago;

    public function __construct($id_pedido, $cliente_id, $mesa_id, $fecha_pedido, $hora_pedido, $total, $estado, $metodo_pago) {
        $this->id_pedido = $id_pedido;
        $this->cliente_id = $cliente_id;
        $this->mesa_id = $mesa_id;
        $this->fecha_pedido = $fecha_pedido;
        $this->hora_pedido = $hora_pedido;
        $this->total = $total;
        $this->estado = $estado;
        $this->metodo_pago = $metodo_pago;
    }
}
