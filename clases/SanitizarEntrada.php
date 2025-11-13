<?php
class Sanitizador {
    
    public static function limpiarCadena($cadena) {
        return trim(strip_tags($cadena));
    }

    // Métodos que usa RegistroUsuario
    public static function texto($texto) {
        return trim(strip_tags($texto));
    }

    public static function email($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }
}
?>