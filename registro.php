<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header("Location: index.php");
    exit();
}

require_once("class/usuario.php");
require_once("class/TokenAntiCSRF.php");

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
    
    <form action="procesar.php" method="post">
        
        <h2>Registrar Usuario</h2>
        
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        
        <label>Usuario:</label>
        <input type="text" name="usuario" required>
        
        <label>Contraseña:</label>
        <input type="password" name="contraseña" required>
        
        <label>Rol:</label>
        <select name="rol">
            <option value="1">Admin</option>
            <option value="0" selected>Vendedor</option>
        </select>
        
        <button type="submit" name="enviar">Registrar</button>
        
        <a href="index.php">¿Ya tienes cuenta? Inicia sesión aquí</a>
    </form>

    <script src="assets/js/validaciones.js"></script>
</body>
</html>