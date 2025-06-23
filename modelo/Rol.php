<?php
class Rol {
    public $id_rol;
    public $nombre_rol;
    public $descripcion;

    public function __construct($id_rol, $nombre_rol, $descripcion) {
        $this->id_rol = $id_rol;
        $this->nombre_rol = $nombre_rol;
        $this->descripcion = $descripcion;
    }

    /**
     * Convierte el objeto Rol a un array asociativo.
     * Este método es crucial para que json_encode() funcione correctamente.
     * @return array
     */
    public function toArray() {
        return [
            'id_rol' => $this->id_rol,
            'nombre_rol' => $this->nombre_rol,
            'descripcion' => $this->descripcion
        ];
    }
}
?>