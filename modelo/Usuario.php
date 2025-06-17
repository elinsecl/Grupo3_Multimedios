
<?php

class Usuario{
    public $id_usuario ;    
    public $nombre ;
    public $correo;
    public $password;
    public $tipo_usuario;
    public $id_rol;
    public $fecha_creacion;
    public $estado;
  

    public function __construct($id_usuario,$nombre,$correo,$password,$tipo_usuario,$id_rol,$fecha_creacion,$estado){
        $this->id_usuario = $id_usuario;
        $this->nombre = $nombre;
        $this->correo = $correo;
        $this->password = $password;
        $this->tipo_usuario = $tipo_usuario;
        $this->id_rol = $id_rol;
        $this->fecha_creacion = $fecha_creacion;
        $this->estado = $estado;
    }

}

?>