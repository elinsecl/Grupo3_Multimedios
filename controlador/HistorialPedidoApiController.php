<?php

require_once __DIR__.'/../accesoDatos/HistorialPedidoDAO.php';
require_once __DIR__.'/../modelo/HistorialPedido.php'; 

class HistorialPedidoApiController {

    private $dao;

    public function __construct(){
        $this->dao = new HistorialPedidoDAO();
    }

}

?>