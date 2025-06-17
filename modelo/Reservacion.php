<?php

class Reservacion {
    public $id_reservacion;
    public $cliente_id;
    public $mesa_id;
    public $fecha_reserva;
    public $hora_reserva;
    public $cantidad_personas;
    public $estado;

    public function __construct($id_reservacion, $cliente_id, $mesa_id, $fecha_reserva, $hora_reserva, $cantidad_personas, $estado) {
        $this->id_reservacion = $id_reservacion;
        $this->cliente_id = $cliente_id;
        $this->mesa_id = $mesa_id;
        $this->fecha_reserva = $fecha_reserva;
        $this->hora_reserva = $hora_reserva;
        $this->cantidad_personas = $cantidad_personas;
        $this->estado = $estado;
    }
}
?>
