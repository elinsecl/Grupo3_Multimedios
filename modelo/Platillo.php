<?php

class Platillo {
    public $id_platillo;
    public $nombre_platillo;
    public $descripcion;
    public $precio;
    public $id_categoria;


    public function __construct(
        $id_platillo = null, 
        $nombre_platillo = null, 
        $descripcion = null, 
        $precio = null, 
        $id_categoria = null, 
    ) {
        $this->id_platillo = $id_platillo;
        $this->nombre_platillo = $nombre_platillo;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->id_categoria = $id_categoria;
    }

    public function toArray() {
        return [
            'id_platillo' => $this->id_platillo,
            'nombre_platillo' => $this->nombre_platillo,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'id_categoria' => $this->id_categoria,
        ];
    }

    public function toPublicArray() {
        return [
            'id_platillo' => $this->id_platillo,
            'nombre_platillo' => $this->nombre_platillo,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'id_categoria' => $this->id_categoria,
            // Todos los campos son públicos en este caso
            // Si hubiera campos sensibles, los omitiríamos aquí
        ];
    }
}