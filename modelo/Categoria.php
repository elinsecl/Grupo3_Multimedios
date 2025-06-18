<?php

class Categoria {
    public $id_categoria;
    public $nombre_categoria;
    public $descripcion;

    public function __construct($id_categoria, $nombre_categoria, $descripcion) {
        $this->id_categoria = $id_categoria;
        $this->nombre_categoria = $nombre_categoria;
        $this->descripcion = $descripcion;
    }
}

?>
