<?php
require_once("DB.php");

class usuarios {
    protected $id;
    protected $usuario;
    protected $contraseña;
    protected $rol;

    public function setId($id) { $this->id = $id; }
    public function setUsuario($usuario) { $this->usuario = $usuario; }
    public function setContraseña($contraseña) { $this->contraseña = $contraseña; }
    public function setRol($rol) { $this->rol = $rol; }

    public function getId() { return $this->id; }
    public function getUsuario() { return $this->usuario; }
    public function getContraseña() { return $this->contraseña; }
    public function getRol() { return $this->rol; }

    public function guardar() {
        if ($this->existeUsuario()) {
            return false;
        }
        $con = DB::conectar();
        $hash = password_hash($this->contraseña, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios(usuario, contraseña, rol) VALUES(?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssi", $this->usuario, $hash, $this->rol);
        $stmt->execute();
        $stmt->close();
        return true;
    }

    public function login() {
        $con = DB::conectar();
        $sql = "SELECT * FROM usuarios WHERE usuario = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $this->usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            if (password_verify($this->contraseña, $fila['contraseña'])) {
                $_SESSION['usuario_id'] = $fila['id'];
                $_SESSION['usuario_nombre'] = $fila['usuario'];
                $_SESSION['usuario_rol'] = $fila['rol'];
                return true;
            }
        }
        return false;
    }

    private function existeUsuario() {
        $con = DB::conectar();
        $sql = "SELECT id FROM usuarios WHERE usuario = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $this->usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0;
    }

    public function obtenerTodos() {
        $con = DB::conectar();
        $sql = "SELECT id, usuario, rol FROM usuarios ORDER BY usuario ASC";
        return $con->query($sql);
    }

    public function actualizarRol() {
        $con = DB::conectar();
        $sql = "UPDATE usuarios SET rol = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ii", $this->rol, $this->id);
        $stmt->execute();
        $stmt->close();
    }
}
?>