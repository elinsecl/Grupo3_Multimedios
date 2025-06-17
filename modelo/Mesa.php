<?php

class Mesa {
    public $id_mesa;
    public $numero_mesa;
    public $capacidad;
    public $ubicacion;
    public $estado;

    public function __construct($id_mesa, $numero_mesa, $capacidad, $ubicacion, $estado) {
        $this->id_mesa = $id_mesa;
        $this->numero_mesa = $numero_mesa;
        $this->capacidad = $capacidad;
        $this->ubicacion = $ubicacion;
        $this->estado = $estado;
    }
}
?>
