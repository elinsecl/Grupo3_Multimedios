<?php

require_once __DIR__.'/../accesoDatos/HistorialInventarioDAO.php';
require_once __DIR__.'/../modelo/HistorialInventario.php'; 

class HistorialInventarioApiController {

    private $dao;

    public function __construct(){
        $this->dao = new HistorialInventarioDAO();
    }

}

?>