<?php
session_start();
require_once("class/usuario.php");

if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['login'])) {
    $usuario = new usuarios();
    $usuario->setUsuario($_POST['usuario']);
    $usuario->setContraseña($_POST['contraseña']);

    if ($usuario->login()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Ventas</title>
     <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <h2>Iniciar Sesión</h2>

    <form method="POST" action="">
        <label>Usuario:</label><br>
        <input type="text" name="usuario" required><br><br>
        
        <label>Contraseña:</label><br>
        <input type="password" name="contraseña" required><br><br>
        
        <button type="submit" name="login">Ingresar</button>
    </form>
    
    <br>
    <a href="index.php">Registrarse</a>
</body>
</html>