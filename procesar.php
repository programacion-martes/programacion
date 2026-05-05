<?php
session_start();
require_once("class/usuario.php");
require_once("class/TokenAntiCSRF.php");
require_once("class/productos.php");
require_once("class/categorias_productos.php");
require_once("class/precios.php");
require_once("class/cliente.php");

// REGISTRAR USUARIO
if (isset($_POST["enviar"])) {
    if (!TokenAntiCSRF::validarToken($_POST['token'])) {
        echo 'Token inválido.<br><a href="index.php">Volver</a>';
        exit();
    }
    $usuario = new usuarios();
    $usuario->setUsuario($_POST["usuario"]);
    $usuario->setContraseña($_POST["contraseña"]);
    if ($usuario->guardar()) {
        TokenAntiCSRF::generarToken();
        echo 'Usuario registrado correctamente.<br><a href="index.php">Ir al Login</a>';
    } else {
        echo 'El usuario ya existe.<br><a href="index.php">Volver</a>';
    }
}

// GUARDAR VENTA
if (isset($_POST["guardar_venta"])) {
    $cliente = new cliente();
    $cliente->setDocumento($_POST["documento"]);
    $cliente->setNumeroDocumento($_POST["numerodocumento"]);
    $cliente->setNombre($_POST["nombre"]);
    $cliente->setApellido($_POST["apellido"]);
    $cliente->setTelefono($_POST["telefono"]);
    $cliente->setDireccion($_POST["direccion"]);
    
    if ($cliente->existeCedula()) {
        $cliente->guardar(); // Actualizar datos si ya existe
        $con = DB::conectar();
        $sql = "SELECT id FROM clientes WHERE numerodocumento = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $_POST["numerodocumento"]);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $cli = $resultado->fetch_assoc();
        $cliente_id = $cli['id'];
        $stmt->close();
    } else {
        $cliente->guardar();
        $cliente_id = DB::conectar()->insert_id;
    }
    
    $con = DB::conectar();
    $sql = "INSERT INTO ventas(fecha, clienteid) VALUES(CURDATE(), ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $venta_id = $con->insert_id;
    $stmt->close();
    
    if (isset($_POST["cantidad"])) {
        foreach ($_POST["cantidad"] as $producto_id => $cantidad) {
            if ($cantidad > 0) {
                $sql = "SELECT id FROM precios WHERE productoid = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("i", $producto_id);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $precio = $resultado->fetch_assoc();
                $stmt->close();
                
                if ($precio) {
                    for ($i = 0; $i < $cantidad; $i++) {
                        $sql = "INSERT INTO detalles_ventas(ventaid, precioid) VALUES(?, ?)";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param("ii", $venta_id, $precio['id']);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            }
        }
    }
    
    header("Location: dashboard.php");
    exit();
}

// GUARDAR PRODUCTO
if (isset($_POST["guardar_producto"])) {
    $con = DB::conectar();
    $sql = "SELECT id FROM categorias_productos WHERE nombre_categoria = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $_POST["categoria"]);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $categoria_id = $fila['id'];
    } else {
        $nueva_cat = new categoria_productos();
        $nueva_cat->setNombre_categoria($_POST["categoria"]);
        $nueva_cat->guardar();
        $categoria_id = $con->insert_id;
    }
    $stmt->close();
    
    $producto = new producto();
    $producto->setCategoria_productoid($categoria_id);
    $producto->setProducto($_POST["nombre_producto"]);
    $producto->guardar();
    $producto_id = $con->insert_id;
    
    $precio = new precios();
    $precio->setProductoid($producto_id);
    $precio->setPrecio($_POST["precio"]);
    $precio->setIva($_POST["iva"]);
    $precio->guardar();
    
    header("Location: dashboard.php");
    exit();
}

// ACTUALIZAR PRODUCTO
if (isset($_POST["actualizar_producto"])) {
    $con = DB::conectar();
    $sql = "SELECT id FROM categorias_productos WHERE nombre_categoria = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $_POST["categoria"]);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $categoria_id = $fila['id'];
    } else {
        $nueva_cat = new categoria_productos();
        $nueva_cat->setNombre_categoria($_POST["categoria"]);
        $nueva_cat->guardar();
        $categoria_id = $con->insert_id;
    }
    $stmt->close();
    
    $producto = new producto();
    $producto->setId($_POST["id"]);
    $producto->setCategoria_productoid($categoria_id);
    $producto->setProducto($_POST["nombre_producto"]);
    $producto->actualizar();
    
    $precio_obj = new precios();
    $precio_obj->setProductoid($_POST["id"]);
    $precio_existente = $precio_obj->obtenerPorProductoId();
    if ($precio_existente) {
        $precio_obj->setId($precio_existente['id']);
        $precio_obj->setPrecio($_POST["precio"]);
        $precio_obj->setIva($_POST["iva"]);
        $precio_obj->actualizar();
    } else {
        $precio_obj->setPrecio($_POST["precio"]);
        $precio_obj->setIva($_POST["iva"]);
        $precio_obj->guardar();
    }
    
    header("Location: dashboard.php");
    exit();
}

// ELIMINAR PRODUCTO
if (isset($_GET["eliminar"])) {
    $producto = new producto();
    $producto->setId($_GET["eliminar"]);
    $producto->eliminar();
    header("Location: dashboard.php");
    exit();
}
?>