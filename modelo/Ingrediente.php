<?php

class Ingrediente {
    public $id_ingrediente;
    public $nombre_ingrediente;
    public $descripcion;
    public $cantidad_stock;
    public $unidad;

    public function __construct($id_ingrediente, $nombre_ingrediente, $descripcion, $cantidad_stock, $unidad) {
        $this->id_ingrediente = $id_ingrediente;
        $this->nombre_ingrediente = $nombre_ingrediente;
        $this->descripcion = $descripcion;
        $this->cantidad_stock = $cantidad_stock;
        $this->unidad = $unidad;
    }
}
?>
