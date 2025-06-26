
<?php

// En tu archivo: Grupo3_Multimedios/modelo/Usuario.php

class Usuario {
    public $id_usuario;
    public $nombre;
    public $correo;
    public $password; // Cuidado con exponer esto
    public $id_rol;
    public $fecha_creacion;
    public $estado;

    public function __construct(
        $id_usuario = null, 
        $nombre = null, 
        $correo = null, 
        $password = null,  
        $id_rol = null, 
        $fecha_creacion = null, 
        $estado = null
    ) {
        $this->id_usuario = $id_usuario;
        $this->nombre = $nombre;
        $this->correo = $correo;
        $this->password = $password;
        $this->id_rol = $id_rol;
        $this->fecha_creacion = $fecha_creacion;
        $this->estado = $estado;
    }

    public function toArray() {
        return [
            'id_usuario' => $this->id_usuario,
            'nombre' => $this->nombre,
            'correo' => $this->correo,
            'password' => $this->password, 
            'id_rol' => $this->id_rol,
            'fecha_creacion' => $this->fecha_creacion,
            'estado' => $this->estado
        ];
    }

    public function toPublicArray() {
        return [
            'id_usuario' => $this->id_usuario,
            'nombre' => $this->nombre,
            'correo' => $this->correo,
            //no password para la segurity
            'id_rol' => $this->id_rol,
            'fecha_creacion' => $this->fecha_creacion,
            'estado' => $this->estado
        ];
    }
}

?>