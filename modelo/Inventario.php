<?php
class Inventario {
    public $id_inventario;
    public $ingrediente_id;
    public $cantidad_stock;
    public $fecha_entrada;
    public $proveedor_id;

    public function __construct($id_inventario, $ingrediente_id, $cantidad_stock, $fecha_entrada, $proveedor_id) {
        $this->id_inventario = $id_inventario;
        $this->ingrediente_id = $ingrediente_id;
        $this->cantidad_stock = $cantidad_stock;
        $this->fecha_entrada = $fecha_entrada;
        $this->proveedor_id = $proveedor_id;
    }
}
?>
