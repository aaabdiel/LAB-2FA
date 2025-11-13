<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "app_web_utp";
$password = "ClaveSegura2025!";
$dbname = "company_info";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Incluir clases necesarias
include("clases/mysql.inc.php");
include("clases/RegistroUsuario.php");

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Crear instancia de mod_db
    $db = new mod_db();
    
    // Crear instancia de RegistroUsuario
    $registro = new RegistroUsuario($db, $_POST);
    
    // Validar los datos
    if ($registro->validar()) {
        // Intentar registrar el usuario
        if ($registro->registrar()) {
            // Obtener el ID del usuario registrado
            $usuario_id = $registro->getIdInsertado();
            $usuario = $_POST['usuario'];
            
            // Iniciar sesión automáticamente
            session_start();
            $_SESSION['autenticado'] = "SI";
            $_SESSION['Usuario'] = $usuario;
            $_SESSION['usuario_id'] = $usuario_id;
            $_SESSION['autenticado_2fa'] = false;
            
            // Redirigir a activación 2FA
            header("Location: activar_2fa.php?nuevo_registro=1");
            exit();
        } else {
            // Error al insertar en la base de datos
            $error_message = "Error al guardar los datos en la base de datos";
            if ($conn->errno == 1062) {
                $error_message = "El usuario o correo electrónico ya existen";
            }
            header("Location: FormularioRegistro.php?error=" . urlencode($error_message));
            exit();
        }
    } else {
        // Si hay errores de validación, redirigir con mensajes
        $errores = $registro->getErrores();
        header("Location: FormularioRegistro.php?error=" . urlencode(implode(", ", $errores)));
        exit();
    }
} else {
    // Si no es POST, redirigir al formulario
    header("Location: FormularioRegistro.php");
    exit();
}

$conn->close();
?>