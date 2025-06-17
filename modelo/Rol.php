<?php

class Rol{
    public $id_rol ;    
    public $nombre ;
    public $descripcion;
  
  

    public function __construct($id_rol,$nombre,$descripcion){
        $this->id_rol = $id_rol;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;

    }

}

?>