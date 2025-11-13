<?php
require_once 'Sanitizador.php';

class RegistroUsuario {
    private $db;
    private $datos;
    private $errores = [];

    public function __construct($db, $datos) {
        $this->db = $db;
        $this->datos = $datos;
    }

    // MÉTODO 1: Validar campos requeridos (responsabilidad única)
    private function validarCamposRequeridos() {
        $campos = ['nombre', 'apellido', 'usuario', 'correo', 'contraseña'];
        foreach ($campos as $campo) {
            if (empty(trim($this->datos[$campo]))) {
                $this->errores[] = "El campo " . ucfirst($campo) . " es obligatorio";
            }
        }
    }

    // MÉTODO 2: Validar formato email (responsabilidad única)
    private function validarFormatoEmail() {
        $email = $this->datos['correo'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errores[] = "El formato del correo electrónico no es válido";
        }
    }

    // MÉTODO 3: Validar longitud usuario (responsabilidad única)
    private function validarLongitudUsuario() {
        $usuario = $this->datos['usuario'];
        if (strlen($usuario) < 4) {
            $this->errores[] = "El nombre de usuario debe tener al menos 4 caracteres";
        }
    }

    // MÉTODO 4: Validar caracteres usuario (responsabilidad única)
    private function validarCaracteresUsuario() {
        $usuario = $this->datos['usuario'];
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $usuario)) {
            $this->errores[] = "El nombre de usuario solo puede contener letras, números y guiones bajos";
        }
    }

    // MÉTODO 5: Validar fortaleza contraseña (responsabilidad única)
    private function validarFortalezaContraseña() {
        $contraseña = $this->datos['contraseña'];
        if (strlen($contraseña) < 6) {
            $this->errores[] = "La contraseña debe tener al menos 6 caracteres";
        }
    }

    // MÉTODO 6: Validar duplicados en BD (responsabilidad única)
    private function validarDuplicados() {
    $usuario = $this->datos['usuario'];
    $correo = $this->datos['correo'];

    try {
        // Verificar si usuario existe - USANDO PDO
        $sql_usuario = "SELECT id FROM usuarios WHERE Usuario = :usuario";
        $stmt_usuario = $this->db->getConexion()->prepare($sql_usuario);
        $stmt_usuario->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt_usuario->execute();
        
        if ($stmt_usuario->rowCount() > 0) {
            $this->errores[] = "El nombre de usuario ya está en uso";
        }

        // Verificar si correo existe - USANDO PDO
        $sql_correo = "SELECT id FROM usuarios WHERE Correo = :correo";
        $stmt_correo = $this->db->getConexion()->prepare($sql_correo);
        $stmt_correo->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt_correo->execute();
        
        if ($stmt_correo->rowCount() > 0) {
            $this->errores[] = "El correo electrónico ya está registrado";
        }

    } catch (PDOException $e) {
        $this->errores[] = "Error al verificar duplicados: " . $e->getMessage();
    }
}

    // MÉTODO PRINCIPAL: Coordinar todas las validaciones
    public function validar() {
        $this->validarCamposRequeridos();
        $this->validarFormatoEmail();
        $this->validarLongitudUsuario();
        $this->validarCaracteresUsuario();
        $this->validarFortalezaContraseña();
        
        // Solo validar duplicados si no hay otros errores
        if (empty($this->errores)) {
            $this->validarDuplicados();
        }
        
        return empty($this->errores);
    }

    // MÉTODO: Sanitizar datos antes del registro
    private function sanitizarDatos() {
        $this->datos['nombre'] = Sanitizador::texto($this->datos['nombre']);
        $this->datos['apellido'] = Sanitizador::texto($this->datos['apellido']);
        $this->datos['usuario'] = Sanitizador::texto($this->datos['usuario']);
        $this->datos['correo'] = Sanitizador::email($this->datos['correo']);
    }

    // MÉTODO: Registrar usuario en la base de datos
    public function registrar() {
        // Sanitizar datos primero
        $this->sanitizarDatos();

        // Generar hash de la contraseña
        $contraseña_hash = password_hash($this->datos['contraseña'], PASSWORD_DEFAULT);

        // Preparar datos para inserción
        $data = array(
            'Nombre' => $this->datos['nombre'],
            'Apellido' => $this->datos['apellido'],
            'Usuario' => $this->datos['usuario'],
            'Correo' => $this->datos['correo'],
            'HashMagic' => $contraseña_hash
        );

        // Insertar usando la clase mod_db existente
        return $this->db->insertSeguro("usuarios", $data);
    }

    // GETTER para errores
    public function getErrores() {
        return $this->errores;
    }

    // GETTER para ID del usuario insertado
    public function getIdInsertado() {
        return $this->db->insert_id();
    }
}
?>