<?php

class Cliente {
    public $id_cliente;
    public $nombre;
    public $telefono;
    public $correo;
    public $direccion;
    public $fecha_registro;
    public $estado;

    public function __construct($id_cliente, $nombre, $telefono, $correo, $direccion, $fecha_registro, $estado) {
        $this->id_cliente = $id_cliente;
        $this->nombre = $nombre;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->direccion = $direccion;
        $this->fecha_registro = $fecha_registro;
        $this->estado = $estado;
    }
}
?>
