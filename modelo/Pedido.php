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

    /**
     * Constructor del Pedido.
     */
    public function __construct(
        $id_pedido = null,
        $cliente_id = null,
        $mesa_id = null,
        $fecha_pedido = null,
        $hora_pedido = null,
        $total = null,
        $estado = null,
        $metodo_pago = null
    ) {
        $this->id_pedido = $id_pedido;
        $this->cliente_id = $cliente_id;
        $this->mesa_id = $mesa_id;
        $this->fecha_pedido = $fecha_pedido;
        $this->hora_pedido = $hora_pedido;
        $this->total = $total;
        $this->estado = $estado;
        $this->metodo_pago = $metodo_pago;
    }

    /**
     * Retorna un arreglo con todos los datos del pedido.
     */
    public function toArray() {
        return [
            'id_pedido'    => $this->id_pedido,
            'cliente_id'   => $this->cliente_id,
            'mesa_id'      => $this->mesa_id,
            'fecha_pedido' => $this->fecha_pedido,
            'hora_pedido'  => $this->hora_pedido,
            'total'        => $this->total,
            'estado'       => $this->estado,
            'metodo_pago'  => $this->metodo_pago
        ];
    }

    /**
     * Retorna un arreglo con los datos públicos del pedido.
     */
    public function toPublicArray() {
        return $this->toArray(); // Por ahora no hay diferencia entre privado y público
    }
}
