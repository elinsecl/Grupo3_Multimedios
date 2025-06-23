<?php

class Platillo {
    public $id_platillo;
    public $nombre_platillo;
    public $descripcion;
    public $precio;
    public $id_categoria;
    public $estado;
    public $imagen_url;

    public function __construct(
        $id_platillo = null, 
        $nombre_platillo = null, 
        $descripcion = null, 
        $precio = null, 
        $id_categoria = null, 
        $estado = null, 
        $imagen_url = null
    ) {
        $this->id_platillo = $id_platillo;
        $this->nombre_platillo = $nombre_platillo;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->id_categoria = $id_categoria;
        $this->estado = $estado;
        $this->imagen_url = $imagen_url;
    }

    public function toArray() {
        return [
            'id_platillo' => $this->id_platillo,
            'nombre_platillo' => $this->nombre_platillo,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'id_categoria' => $this->id_categoria,
            'estado' => $this->estado,
            'imagen_url' => $this->imagen_url
        ];
    }

    public function toPublicArray() {
        return [
            'id_platillo' => $this->id_platillo,
            'nombre_platillo' => $this->nombre_platillo,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'id_categoria' => $this->id_categoria,
            'estado' => $this->estado,
            'imagen_url' => $this->imagen_url
            // Todos los campos son públicos en este caso
            // Si hubiera campos sensibles, los omitiríamos aquí
        ];
    }
}