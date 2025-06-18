<?php

class Platillo {
    public $id_platillo;
    public $nombre_platillo;
    public $descripcion;
    public $precio;
    public $id_categoria;
    public $estado;
    public $imagen_url;

    public function __construct($id_platillo, $nombre_platillo, $descripcion, $precio, $id_categoria, $estado, $imagen_url) {
        $this->id_platillo = $id_platillo;
        $this->nombre_platillo = $nombre_platillo;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->id_categoria = $id_categoria;
        $this->estado = $estado;
        $this->imagen_url = $imagen_url;
    }
}

?>
