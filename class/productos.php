<?php
require_once("DB.php");

class producto {
    protected $id;
    protected $categoria_productoid;
    protected $producto;
    protected $stock;

    public function setId($id) {
        $this->id = $id;
    }
    
    public function setCategoria_productoid($categoria_productoid) {
        $this->categoria_productoid = $categoria_productoid;
    }
    
    public function setProducto($producto) {
        $this->producto = $producto;
    }

    public function setStock($stock) {
        $this->stock = $stock;
    }

    public function getId() {
        return $this->id;
    }
    
    public function getCategoria_productoid() {
        return $this->categoria_productoid;
    }
    
    public function getProducto() {
        return $this->producto;
    }

    public function getStock() {
        return $this->stock;
    }

    public function guardar() {
        $con = DB::conectar();
        $sql = "INSERT INTO productos(categoria_productoid, nombre_producto, stock) VALUES(?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("isi", $this->categoria_productoid, $this->producto, $this->stock);
        $stmt->execute();
        $stmt->close();
    }

    public static function listar() {
        $con = DB::conectar();
        $sql = "SELECT p.id, p.nombre_producto, p.stock, c.nombre_categoria 
                FROM productos p 
                JOIN categorias_productos c ON p.categoria_productoid = c.id";
        $resultado = $con->query($sql);
        return $resultado;
    }

    public function obtenerPorId() {
        $con = DB::conectar();
        $sql = "SELECT * FROM productos WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    public function actualizar() {
        $con = DB::conectar();
        $sql = "UPDATE productos SET categoria_productoid = ?, nombre_producto = ?, stock = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("isii", $this->categoria_productoid, $this->producto, $this->stock, $this->id);
        $stmt->execute();
        $stmt->close();
    }

    public function eliminar() {
        $con = DB::conectar();
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $stmt->close();
    }
}
?>