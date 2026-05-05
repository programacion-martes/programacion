<?php
session_start();
require_once("class/usuario.php");
require_once("class/TokenAntiCSRF.php");
require_once("class/productos.php");
require_once("class/categorias_productos.php");
require_once("class/precios.php");
require_once("class/cliente.php");

if (isset($_POST["enviar"])) {
    if (!TokenAntiCSRF::validarToken($_POST['token'])) {
        echo '<!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"><title>Error</title><link rel="stylesheet" href="assets/css/app.css"></head>
        <body><div class="mensaje-page"><div class="mensaje-card error-card">
        <h2>Token inválido</h2><a href="index.php">Volver</a>
        </div></div></body></html>';
        exit();
    }
$usuario = new usuarios();
$usuario->setUsuario($_POST["usuario"]);
$usuario->setContraseña($_POST["contraseña"]);
$usuario->setRol(isset($_POST["rol"]) ? $_POST["rol"] : 0);
    if ($usuario->guardar()) {
        TokenAntiCSRF::generarToken();
        echo '<!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"><title>Registro Exitoso</title><link rel="stylesheet" href="assets/css/app.css"></head>
        <body><div class="mensaje-page"><div class="mensaje-card">
        <h2>Usuario registrado correctamente</h2><a href="index.php">Ir al Login</a>
        </div></div></body></html>';
    } else {
        echo '<!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"><title>Error</title><link rel="stylesheet" href="assets/css/app.css"></head>
        <body><div class="mensaje-page"><div class="mensaje-card error-card">
        <h2>El usuario ya existe</h2><a href="index.php">Volver</a>
        </div></div></body></html>';
    }
    exit();
}

if (isset($_POST['guardar_venta'])) {
    $con = DB::conectar();
    
    $cliente_id = $_POST['cliente_id'];
    
    $sql = "INSERT INTO ventas (clienteid, fecha) VALUES (?, NOW())";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $venta_id = $stmt->insert_id;
    $stmt->close();
    
    if (isset($_POST['cantidad'])) {
        foreach ($_POST['cantidad'] as $producto_id => $cantidad) {
            if ($cantidad > 0) {
                $sql_precio = "SELECT id FROM precios WHERE productoid = ?";
                $stmt_precio = $con->prepare($sql_precio);
                $stmt_precio->bind_param("i", $producto_id);
                $stmt_precio->execute();
                $resultado = $stmt_precio->get_result();
                $precio = $resultado->fetch_assoc();
                $stmt_precio->close();
                
                for ($i = 0; $i < $cantidad; $i++) {
                    $sql_det = "INSERT INTO detalles_ventas (ventaid, precioid) VALUES (?, ?)";
                    $stmt_det = $con->prepare($sql_det);
                    $stmt_det->bind_param("ii", $venta_id, $precio['id']);
                    $stmt_det->execute();
                    $stmt_det->close();
                }
                
                $sql_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
                $stmt_stock = $con->prepare($sql_stock);
                $stmt_stock->bind_param("ii", $cantidad, $producto_id);
                $stmt_stock->execute();
                $stmt_stock->close();
            }
        }
    }
    
    
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST["guardar_producto"])) {
    $con = DB::conectar();
    
    if (!empty($_POST["categoria_id"])) {
        $categoria_id = $_POST["categoria_id"];
    }
    
    $producto = new producto();
    $producto->setCategoria_productoid($categoria_id);
    $producto->setProducto($_POST["nombre_producto"]);
    $producto->setStock($_POST["stock"]);
    $producto->guardar();
    $producto_id = $con->insert_id;
    
    $precio = new precios();
    $precio->setProductoid($producto_id);
    $precio->setPrecio($_POST["precio"]);
    $precio->setIva($_POST["iva"]);
    $precio->guardar();
    
    header("Location: lista_productos.php");
    exit();
}

if (isset($_POST["actualizar_producto"])) {
    $con = DB::conectar();
    
    if (!empty($_POST["categoria_id"])) {
        $categoria_id = $_POST["categoria_id"];
    }
    
    $producto = new producto();
    $producto->setId($_POST["id"]);
    $producto->setCategoria_productoid($categoria_id);
    $producto->setProducto($_POST["nombre_producto"]);
    $producto->setStock($_POST["stock"]);
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
    
    header("Location: lista_productos.php");
    exit();
}

if (isset($_GET["eliminar"])) {
    $producto = new producto();
    $producto->setId($_GET["eliminar"]);
    $producto->eliminar();
    header("Location: lista_productos.php");
    exit();
}

if (isset($_POST['guardar_categoria'])) {
    $cat = new categoria_productos();
    $cat->setNombre_categoria($_POST['nombre_categoria']);
    $cat->guardar();
    header("Location: categorias.php");
    exit();
}

if (isset($_POST['actualizar_categoria'])) {
    $cat = new categoria_productos();
    $cat->setId($_POST['id']);
    $cat->setNombre_categoria($_POST['nombre_categoria']);
    $cat->actualizar();
    header("Location: categorias.php");
    exit();
}

if (isset($_GET['eliminar_categoria'])) {
    $cat = new categoria_productos();
    $cat->setId($_GET['eliminar_categoria']);
    $cat->eliminar();
    header("Location: categorias.php");
    exit();
}

if (isset($_POST['guardar_cliente'])) {
    $cliente = new cliente();
    $cliente->setDocumento($_POST['documento']);
    $cliente->setNumeroDocumento($_POST['numerodocumento']);
    $cliente->setNombre($_POST['nombre']);
    $cliente->setApellido($_POST['apellido']);
    $cliente->setTelefono($_POST['telefono']);
    $cliente->setDireccion($_POST['direccion']);

    if ($cliente->guardar()) {
        $cedula = $_POST['numerodocumento'];
        header("Location: registrar_venta.php?cedula=" . $cedula);
        exit();
    } else {
        echo '<!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"><title>Error</title><link rel="stylesheet" href="assets/css/app.css"></head>
        <body><div class="mensaje-page"><div class="mensaje-card error-card">
        <h2>La cédula ya existe</h2><a href="agregar_cliente.php">Volver</a>
        </div></div></body></html>';
        exit();
    }
}

if (isset($_POST['actualizar_cliente'])) {
    $cliente = new cliente();
    $cliente->setId($_POST['id']);
    $cliente->setDocumento($_POST['documento']);
    $cliente->setNumeroDocumento($_POST['numerodocumento']);
    $cliente->setNombre($_POST['nombre']);
    $cliente->setApellido($_POST['apellido']);
    $cliente->setTelefono($_POST['telefono']);
    $cliente->setDireccion($_POST['direccion']);
    $cliente->actualizar();

    header("Location: clientes.php");
    exit();
}

if (isset($_GET['eliminar_cliente'])) {
    $cliente = new cliente();
    $cliente->setId($_GET['eliminar_cliente']);
    $cliente->eliminar();

    header("Location: clientes.php");
    exit();
}

if (isset($_GET['eliminar_detalle'])) {
    $con = DB::conectar();
    
    $sql = "SELECT pr.productoid FROM detalles_ventas dv 
            JOIN precios pr ON dv.precioid = pr.id 
            WHERE dv.id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $_GET['eliminar_detalle']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $detalle = $resultado->fetch_assoc();
    $stmt->close();
    
    if ($detalle) {
        $sql_stock = "UPDATE productos SET stock = stock + 1 WHERE id = ?";
        $stmt_stock = $con->prepare($sql_stock);
        $stmt_stock->bind_param("i", $detalle['productoid']);
        $stmt_stock->execute();
        $stmt_stock->close();
        
        $sql_del = "DELETE FROM detalles_ventas WHERE id = ? LIMIT 1";
        $stmt_del = $con->prepare($sql_del);
        $stmt_del->bind_param("i", $_GET['eliminar_detalle']);
        $stmt_del->execute();
        $stmt_del->close();
    }
    
    header("Location: dashboard.php");
    exit();
}


if (isset($_GET['cambiar_rol'])) {
    $usuario = new usuarios();
    $usuario->setId($_GET['cambiar_rol']);
    $usuario->setRol($_GET['rol']);
    $usuario->actualizarRol();
    header("Location: usuarios.php");
    exit();
}

header("Location: dashboard.php");
exit();
?>