<?php

class Pedido {
    public $id_pedido;
    public $id_usuario;
    public $fecha_pedido;
    public $estado;

    public function __construct($id_pedido = null, $id_usuario = null, $fecha_pedido = null, $estado = null) {
        $this->id_pedido = $id_pedido;
        $this->id_usuario = $id_usuario;
        $this->fecha_pedido = $fecha_pedido;
        $this->estado = $estado;
    }

    public function toArray() {
        return [
            'id_pedido' => $this->id_pedido,
            'id_usuario' => $this->id_usuario,
            'fecha_pedido' => $this->fecha_pedido,
            'estado' => $this->estado
        ];
    }

    public function toPublicArray() {
        return $this->toArray(); // No hay datos sensibles
    }
}

