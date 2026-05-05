<?php
session_start();
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
    <h2>Registrar Usuario</h2>

    <form action="procesar.php" method="post">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        
        <label>Usuario:</label><br>
        <input type="text" name="usuario" id="usuario" required><br><br>
        
        <label>Contraseña:</label><br>
        <input type="password" name="contraseña" id="contraseña" required><br><br>
        
        <button type="submit" name="enviar">Registrar</button>
    </form>
    
    <br>
    <a href="login.php">Ya tengo cuenta</a>
</body>
</html>