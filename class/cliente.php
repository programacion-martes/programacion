<?php
require_once("DB.php");

class cliente {
    protected $id;
    protected $documento;
    protected $numeroDocumento;
    protected $nombre;
    protected $apellido;
    protected $telefono;
    protected $direccion;

    public function setId($id) { $this->id = $id; }
    public function setDocumento($documento) { $this->documento = $documento; }
    public function setNumeroDocumento($numeroDocumento) { $this->numeroDocumento = $numeroDocumento; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setApellido($apellido) { $this->apellido = $apellido; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }
    public function setDireccion($direccion) { $this->direccion = $direccion; }

    public function getId() { return $this->id; }
    public function getDocumento() { return $this->documento; }
    public function getNumeroDocumento() { return $this->numeroDocumento; }
    public function getNombre() { return $this->nombre; }
    public function getApellido() { return $this->apellido; }
    public function getTelefono() { return $this->telefono; }
    public function getDireccion() { return $this->direccion; }

    public function existeCedula() {
        $con = DB::conectar();
        $sql = "SELECT id FROM clientes WHERE numerodocumento = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $this->numeroDocumento);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $existe = $resultado->num_rows > 0;
        $stmt->close();
        return $existe;
    }

    public function guardar() {
        if ($this->existeCedula()) {
            return false;
        }
        
        $con = DB::conectar();
        $sql = "INSERT INTO clientes(documento, numerodocumento, nombre, apellido, telefono, direccion) 
                VALUES(?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssss", 
            $this->documento, 
            $this->numeroDocumento, 
            $this->nombre, 
            $this->apellido, 
            $this->telefono, 
            $this->direccion
        );
        $stmt->execute();
        $stmt->close();
        return true;
    }

    public function obtenerPorId() {
        $con = DB::conectar();
        $sql = "SELECT * FROM clientes WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
}
?>