<?php
session_start();
require 'vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

// Verificar que el usuario esté logueado
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== "SI") {
    header("Location: login.php");
    exit();
}

// Crear conexión MySQLi directamente
$servername = "localhost";
$username = "app_web_utp";
$password = "ClaveSegura2025!";
$dbname = "company_info";

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['Usuario'];

$mensaje = "";
$qr_url = "";
$secret = "";
$tiene_2fa = false;

// Procesar activación/desactivación de 2FA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['activar'])) {
        // Activar 2FA
        $g = new GoogleAuthenticator();
        $secret = $g->generateSecret();
        
        // Guardar en la base de datos
        $sql = "UPDATE usuarios SET secret_2fa = ? WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("si", $secret, $usuario_id);
            
            if ($stmt->execute()) {
                $mensaje = "<div class='mensaje-exito'>2FA activado correctamente. Escanea el código QR con Google Authenticator.</div>";
                $tiene_2fa = true;
                
                // GENERAR QR
                $issuer = "SistemaUTP"; 
                $accountName = $usuario_nombre;
                
                // Método 1: Usar GoogleQrUrl directamente
                $qrContent = GoogleQrUrl::generate($accountName, $secret, $issuer);
                $qr_url = $qrContent;
                
            } else {
                $mensaje = "<div class='mensaje-error'> Error al activar 2FA: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } else {
            $mensaje = "<div class='mensaje-error'> Error preparando la consulta: " . $mysqli->error . "</div>";
        }
        
    } elseif (isset($_POST['desactivar'])) {
        // Desactivar 2FA
        $sql = "UPDATE usuarios SET secret_2fa = NULL WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $usuario_id);
            
            if ($stmt->execute()) {
                $mensaje = "<div class='mensaje-exito'>2FA desactivado correctamente.</div>";
                $tiene_2fa = false;
            } else {
                $mensaje = "<div class='mensaje-error'>Error al desactivar 2FA: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } else {
            $mensaje = "<div class='mensaje-error'> Error preparando la consulta: " . $mysqli->error . "</div>";
        }
    }
}

// Obtener estado actual del 2FA
$sql = "SELECT secret_2fa FROM usuarios WHERE id = ?";
$stmt = $mysqli->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $usuario_data = $result->fetch_assoc();
        $tiene_2fa = !empty($usuario_data['secret_2fa']);
        $secret = $usuario_data['secret_2fa'] ?? '';
        
        // Generar QR si está activo
        if ($tiene_2fa && !empty($secret)) {
            $issuer = "SistemaUTP";
            $accountName = $usuario_nombre;
            $qrContent = GoogleQrUrl::generate($accountName, $secret, $issuer);
            $qr_url = $qrContent;
        }
    }
    $stmt->close();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar 2FA</title>
    <link rel="stylesheet" href="Estilos/Techmania.css" type="text/css" />
    <style>
        /* CONTENEDOR PRINCIPAL */
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        /* ENCABEZADO */
        .page-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .page-header h2 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .page-header .user-info {
            color: #6c757d;
            font-size: 16px;
        }
        
        /* SECCIÓN QR */
        .qr-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
            border: 1px solid #e9ecef;
        }
        
        .qr-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }
        
        .qr-image {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .qr-image img {
            max-width: 250px;
            height: auto;
        }
        
        /* SECRETO */
        .secret-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
        }
        
        .secret-code {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #e17055;
            background: white;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            text-align: center;
            letter-spacing: 1px;
        }
        
        /* INSTRUCCIONES */
        .instructions {
            background: #e8f4fd;
            border: 1px solid #b8daff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        
        .instructions h4 {
            color: #004085;
            margin-bottom: 15px;
        }
        
        .instructions ol {
            padding-left: 20px;
            margin: 0;
        }
        
        .instructions li {
            margin-bottom: 8px;
            line-height: 1.5;
        }
        
        /* BOTONES */
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 25px 0;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-activar {
            background: #28a745;
            color: white;
        }
        
        .btn-activar:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .btn-desactivar {
            background: #dc3545;
            color: white;
        }
        
        .btn-desactivar:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .btn-volver {
            background: #6c757d;
            color: white;
        }
        
        .btn-volver:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        /* MENSAJES */
        .mensaje-exito {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .mensaje-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        
        /* ESTADO INACTIVO */
        .inactive-state {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            margin: 20px 0;
        }
        
        .inactive-state h3 {
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .container {
                margin: 15px;
                padding: 20px;
            }
            
            .btn-container {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* DEBUG (oculto por defecto) */
        .debug-info {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-size: 12px;
            display: none;
            text-align: left;
        }
    </style>
</head>
<body>
<div id="wrap">
    <div id="header"></div>
    
    <div class="container">
        <!-- ENCABEZADO -->
        <div class="page-header">
            <h2> Configurar Autenticación de Dos Factores</h2>
            <div class="user-info">Usuario: <strong><?php echo htmlspecialchars($usuario_nombre); ?></strong></div>
        </div>
        
        <!-- MENSAJES -->
        <?php echo $mensaje; ?>
        
        <!-- INFORMACIÓN DEBUG -->
        <div class="debug-info" id="debugInfo">
            <strong>Información de Debug:</strong><br>
            Secret: <?php echo htmlspecialchars($secret); ?><br>
            Longitud URL QR: <?php echo strlen($qr_url); ?><br>
            2FA Activado: <?php echo $tiene_2fa ? 'Sí' : 'No'; ?>
        </div>
        
        <?php if ($tiene_2fa && !empty($secret)): ?>
            <!-- ESTADO: 2FA ACTIVADO -->
            <div class="qr-section">
                <h3 style="color: #28a745; margin-bottom: 20px;">✅ Autenticación de Dos Factores Activada</h3>
                
                <div class="qr-container">
                    <!-- CÓDIGO QR -->
                    <?php if (!empty($qr_url)): ?>
                        <div class="qr-image">
                            <img src="<?php echo $qr_url; ?>" alt="Código QR para Google Authenticator">
                        </div>
                        <p style="color: #6c757d; font-size: 14px;">Escanea este código QR con Google Authenticator</p>
                    <?php else: ?>
                        <div class="mensaje-error">
                             Error generando el código QR
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- SECRETO MANUAL -->
                <div class="secret-section">
                    <h4 style="margin-bottom: 15px; color: #856404;">📝 Configuración Manual</h4>
                    <p style="margin-bottom: 10px;">Si no puedes escanear el QR, ingresa este código manualmente:</p>
                    <div class="secret-code"><?php echo $secret; ?></div>
                </div>
                
                <!-- INSTRUCCIONES -->
                <div class="instructions">
                    <h4>📋 Instrucciones para Configuración Manual</h4>
                    <ol>
                        <li><strong>Abre Google Authenticator</strong> en tu dispositivo móvil</li>
                        <li><strong>Toca el botón "+"</strong> para agregar una nueva cuenta</li>
                        <li><strong>Selecciona "Ingresar una clave de configuración"</strong></li>
                        <li><strong>Ingresa la siguiente información:</strong>
                            <ul style="margin-top: 8px; padding-left: 20px;">
                                <li><strong>Cuenta:</strong> <?php echo htmlspecialchars($usuario_nombre); ?></li>
                                <li><strong>Clave secreta:</strong> <?php echo $secret; ?></li>
                            </ul>
                        </li>
                        <li><strong>Asegúrate</strong> de que el tipo sea "Basado en el tiempo"</li>
                    </ol>
                </div>
            </div>
            
            <!-- BOTÓN DESACTIVAR -->
            <div class="btn-container">
                <form method="POST" style="width: 100%;">
                    <button type="submit" name="desactivar" class="btn btn-desactivar">
                        Desactivar Autenticación de Dos Factores
                    </button>
                </form>
            </div>
            
        <?php else: ?>
            <!-- ESTADO: 2FA NO ACTIVADO -->
            <div class="inactive-state">
                <h3> Autenticación de Dos Factores No Activada</h3>
                <p style="color: #6c757d; margin-bottom: 20px; line-height: 1.6;">
                    La autenticación de dos factores añade una capa adicional de seguridad a tu cuenta, 
                    requiriendo tanto tu contraseña como un código temporal para acceder.
                </p>
                
                <!-- INFORMACIÓN SOBRE 2FA -->
                <div class="instructions">
                    <h4>¿Por qué activar la autenticación de dos factores?</h4>
                    <ul style="padding-left: 20px;">
                        <li><strong>Protección adicional:</strong> Tu contraseña + código temporal</li>
                        <li><strong>Códigos dinámicos:</strong> Cambian cada 30 segundos</li>
                        <li><strong>Protección contra robos:</strong> Seguridad incluso si roban tu contraseña</li>
                        <li><strong>Aplicación requerida:</strong> Google Authenticator en tu teléfono</li>
                    </ul>
                </div>
                
                <!-- BOTÓN ACTIVAR -->
                <div class="btn-container">
                    <form method="POST" style="width: 100%;">
                        <button type="submit" name="activar" class="btn btn-activar">
                            Activar Autenticación de Dos Factores
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- BOTÓN VOLVER -->
        <div class="btn-container">
            <a href="formularios/PanelControl.php" class="btn btn-volver">
                ← Volver al Panel de Control
            </a>
        </div>
        
        <!-- BOTÓN DEBUG (solo para desarrollo) -->
        <div style="text-align: center; margin-top: 15px;">
            <button onclick="document.getElementById('debugInfo').style.display='block'" 
                    style="font-size: 10px; padding: 5px 10px; background: #ffc107; border: none; border-radius: 4px; cursor: pointer;">
                🔍 Mostrar Info Debug
            </button>
        </div>
    </div>
    
    <?php include("comunes/footer.php"); ?>
</div>
</body>
</html>