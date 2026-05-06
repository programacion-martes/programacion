<?php
require_once("DB.php");

class iva {
    protected $id;
    protected $porcentaje;

    public function setId($id) {
        $this->id = $id;
    }
    
    public function setPorcentaje($porcentaje) {
        $this->porcentaje = $porcentaje;
    }

    public function getId() {
        return $this->id;
    }
    
    public function getPorcentaje() {
        return $this->porcentaje;
    }

    public static function obtenerActual() {
        $con = DB::conectar();
        $sql = "SELECT * FROM iva ORDER BY id DESC LIMIT 1";
        $resultado = $con->query($sql);
        return $resultado->fetch_assoc();
    }

    public function obtenerPorId() {
        $con = DB::conectar();
        $sql = "SELECT * FROM iva WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    public function guardar() {
        $con = DB::conectar();
        $sql = "INSERT INTO iva(porcentaje) VALUES(?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("d", $this->porcentaje);
        $stmt->execute();
        $stmt->close();
        return $con->insert_id;
    }

    public function actualizar() {
        $con = DB::conectar();
        $sql = "UPDATE iva SET porcentaje = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("di", $this->porcentaje, $this->id);
        $stmt->execute();
        $stmt->close();
    }
}
?>