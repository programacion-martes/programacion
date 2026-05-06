<?php
require_once("DB.php");

class precios {
    protected $id;
    protected $productoid;
    protected $precio;

    public function setId($id) {
        $this->id = $id;
    }
    
    public function setProductoid($productoid) {
        $this->productoid = $productoid;
    }
    
    public function setPrecio($precio) {
        $this->precio = $precio;
    }

    public function getId() {
        return $this->id;
    }
    
    public function getProductoid() {
        return $this->productoid;
    }
    
    public function getPrecio() {
        return $this->precio;
    }

    public function guardar() {
        $con = DB::conectar();
        $sql = "INSERT INTO precios(productoid, precio) VALUES(?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("id", $this->productoid, $this->precio);
        $stmt->execute();
        $stmt->close();
    }

    public function obtenerPorProductoId() {
        $con = DB::conectar();
        $sql = "SELECT * FROM precios WHERE productoid = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $this->productoid);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    public function actualizar() {
        $con = DB::conectar();
        $sql = "UPDATE precios SET precio = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("di", $this->precio, $this->id);
        $stmt->execute();
        $stmt->close();
    }
}
?>