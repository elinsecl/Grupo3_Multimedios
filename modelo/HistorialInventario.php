<?php  

    class HistorialInventario {
          
    public $id_historial_inventario;
    public $id_inventario;
    public $ingrediente_id;
    public $cambio_stock;
    public $fecha;
    public $tipo_cambio;
 
    public function __construct($id_historial_inventario, $id_inventario, $ingrediente_id, $cambio_stock, $fecha, $tipo_cambio) {
        $this->id_historial_inventario = $id_historial_inventario;
        $this->id_inventario = $id_inventario;
        $this->ingrediente_id = $ingrediente_id;
        $this->cambio_stock = $cambio_stock;
        $this->fecha = $fecha;
        $this->tipo_cambio = $tipo_cambio;
    }

    }   

?>