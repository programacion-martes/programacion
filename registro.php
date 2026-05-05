<?php
session_start();
require_once("class/usuario.php");
require_once("class/TokenAntiCSRF.php");

if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}

$token = TokenAntiCSRF::generarToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Sistema de Ventas</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
    <h2>Registrar Usuario</h2>
    
    <form action="procesar.php" method="post">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <label>Usuario:</label><br>
        <input type="text" name="usuario" required><br><br>
        <label>Contraseña:</label><br>
        <input type="password" name="contraseña" required><br><br>
        <button type="submit" name="enviar">Registrar</button>
    </form>
    <br>
    <a href="index.php">¿Ya tienes cuenta? Inicia sesión aquí</a>
</body>
</html>