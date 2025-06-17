<?php  

    class HistorialInventario {
          
    
    public $id_historial_pedido;
    public $pedido_id;
    public $fecha_entrega;
    public $estado_entrega;
 
    public function __construct($id_historial_pedido, $pedido_id, $fecha_entrega, $estado_entrega) {
        $this->id_historial_pedido = $id_historial_pedido;
        $this->pedido_id = $pedido_id;
        $this->fecha_entrega = $fecha_entrega;
        $this->estado_entrega = $estado_entrega;
    }

    }   

?>