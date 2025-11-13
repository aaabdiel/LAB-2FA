# **Laboratorio - Sistema de Autenticación con 2FA en PHP**

## **Contenido**
- [Requisitos Previos](#requisitos-previos)
- [Introducción](#introducción)
- [Características del Sistema](#características-del-sistema)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Configuración e Instalación](#configuración-e-instalación)
- [Flujo de Autenticación](#flujo-de-autenticación)
- [Base de Datos](#base-de-datos)
- [Capturas del Sistema](#capturas-del-sistema)
- [Tecnologías Implementadas](#tecnologías-implementadas)
- [Dificultades y Soluciones](#dificultades-y-soluciones)
- [Referencias Bibliográficas](#referencias-bibliográficas)
- [Información del Desarrollador](#información-del-desarrollador)

<p align="center">
  <img src="https://raw.githubusercontent.com/github/explore/80688e429a7d4ef2fca1e82350fe8e3517d3494d/topics/php/php.png" alt="PHP" width="80">
  <img src="https://raw.githubusercontent.com/github/explore/80688e429a7d4ef2fca1e82350fe8e3517d3494d/topics/mysql/mysql.png" alt="MySQL" width="80">
  <img src="https://raw.githubusercontent.com/github/explore/80688e429a7d4ef2fca1e82350fe8e3517d3494d/topics/html/html.png" alt="HTML" width="80">
  <img src="https://raw.githubusercontent.com/github/explore/80688e429a7d4ef2fca1e82350fe8e3517d3494d/topics/css/css.png" alt="CSS" width="80">
  <img src="https://raw.githubusercontent.com/github/explore/80688e429a7d4ef2fca1e82350fe8e3517d3494d/topics/javascript/javascript.png" alt="JavaScript" width="80">
</p>

## **Requisitos Previos**

### **Stack Tecnológico Requerido**
- ![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php&logoColor=white) **PHP** versión 7.4 o superior
- ![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white) **MySQL** versión 5.7 o superior
- ![Apache](https://img.shields.io/badge/Apache-2.4+-D22128?style=flat&logo=apache&logoColor=white) **Servidor web** Apache
- ![Composer](https://img.shields.io/badge/Composer-2.0+-885630?style=flat&logo=composer&logoColor=white) **Composer** para gestión de dependencias
- ![Google Authenticator](https://img.shields.io/badge/Google_Authenticator-Required-4285F4?style=flat&logo=google&logoColor=white) **App móvil** Google Authenticator

### **Entorno de Desarrollo**
- ![XAMPP](https://img.shields.io/badge/XAMPP-Recommended-FB7A24?style=flat&logo=xampp&logoColor=white) **XAMPP** o **WAMP** (recomendado)
- ![VS Code](https://img.shields.io/badge/VS_Code-Editor-007ACC?style=flat&logo=visual-studio-code&logoColor=white) **Visual Studio Code** o editor preferido
- ![phpMyAdmin](https://img.shields.io/badge/phpMyAdmin-Required-6C78AF?style=flat&logo=phpmyadmin&logoColor=white) **phpMyAdmin** para gestión de BD

## **Introducción**

Este proyecto implementa un **sistema completo de autenticación segura** con **Autenticación de Dos Factores (2FA)** utilizando tecnologías web estándar. El sistema combina múltiples capas de seguridad para proteger contra amenazas comunes como inyección SQL, ataques XSS, CSRF y robo de credenciales.

### **Arquitectura de Seguridad Implementada:**
- **Autenticación de Dos Factores** con Google Authenticator
- **Hashing Seguro** de contraseñas con bcrypt
- **Protección CSRF** con tokens únicos
- **Prevención de SQL Injection** mediante consultas preparadas
- **Sanitización de Entradas** contra XSS
- **Auditoría Completa** de intentos de acceso

### **Objetivos del Laboratorio**
- Implementar un sistema de registro seguro con validaciones multi-nivel
- Integrar autenticación 2FA mediante el estándar TOTP (RFC 6238)
- Aplicar principios de seguridad OWASP en todas las capas
- Demostrar el principio de privilegios mínimos en base de datos
- Crear un flujo de autenticación robusto y usable

## **Características del Sistema**

### **Módulos Principales**
1. **Registro de Usuarios** - Formulario seguro con validaciones
2. **Login Tradicional** - Autenticación usuario/contraseña
3. **Autenticación 2FA** - Integración con Google Authenticator
4. **Protección de Sesiones** - Mecanismos anti-hijacking
5. **Auditoría de Accesos** - Registro de intentos de login

### **Características de Seguridad**
- **Contraseñas hasheadas** con bcrypt (coste 13)
- **Tokens CSRF** en todos los formularios
- **Consultas preparadas** para prevenir SQL injection
- **Sanitización de datos** contra XSS
- **Timeout automático** de sesiones
- **Regeneración de IDs** de sesión
- **Privilegios mínimos** en base de datos

## **Estructura del Proyecto**

```
sistema_autenticacion/
├──  clases/
│   ├── objLoginAdmin.php       # Clase principal de autenticación
│   ├── RegistroUsuario.php     # Clase para registro de usuarios
│   ├── Sanitizador.php         # Clase de sanitización de datos
│   └── mysql.inc.php           # Clase de conexión a BD
├──  comunes/
│   ├── bloque_Seguridad.php    # Protección de sesiones
│   ├── loginfunciones.php      # Funciones auxiliares
│   └── footer.php              # Pie de página
├──  formularios/
│   ├── PanelControl.php        # Panel principal
│   └── TableroMenu.php         # Menú de navegación
├──  vendor/                  # Dependencias de Composer
├──  activar_2fa.php          # Activación de 2FA
├──  Autenticar.php           # Verificación de códigos 2FA
├──  FormularioRegistro.php   # Formulario de registro
├️ ProcesarRegistro.php      # Procesamiento de registros
├️  login.php                 # Página de login
├️  login_form.php           # Formulario de login
├️  index.php                # Procesamiento principal
├️  salir.php                # Cierre de sesión
└️  composer.json            # Configuración de dependencias
```

## **Configuración e Instalación**

### **1. Clonar o Descargar el Proyecto**
```bash
# Clonar el repositorio 
git clone [https://github.com/aaabdiel/LAB-2FA]

# O descargar los archivos directamente
```

### **2. Configurar Base de Datos**
```sql
-- Ejecutar en MySQL via phpMyAdmin
CREATE DATABASE company_info;

-- Crear usuario con privilegios mínimos
CREATE USER 'app_web_user'@'localhost' IDENTIFIED BY 'PasswordSeguro2025!';
GRANT SELECT, INSERT, UPDATE ON company_info.usuarios TO 'app_web_user'@'localhost';
GRANT SELECT, INSERT ON company_info.intentos_login TO 'app_web_user'@'localhost';
FLUSH PRIVILEGES;
```

### **3. Instalar Dependencias**
```bash
# Instalar Google Authenticator via Composer
composer require sonata-project/google-authenticator
```

### **4. Configurar Archivos**
**Actualizar `mysql.inc.php` o `setting.inc.php`:**
```php
$sql_user = "app_web_user";
$sql_pass = "PasswordSeguro2025!";
$sql_name = "company_info";
```

## **Flujo de Autenticación**

### **Flujo Completo del Usuario**

1. **Registro Inicial**
   - Usuario accede a `FormularioRegistro.php`
   - Completa datos personales y credenciales
   - Sistema valida y almacena usuario con hash seguro

2. **Activación de 2FA**
   - Redirección automática a `activar_2fa.php`
   - Generación de código QR único
   - Usuario escanea QR con Google Authenticator

3. **Primer Login**
   - Acceso a `login.php` con credenciales
   - Verificación primaria exitosa
   - Redirección a verificación 2FA

4. **Verificación 2FA**
   - Ingreso de código de 6 dígitos en `Autenticar.php`
   - Validación contra secreto almacenado
   - Acceso concedido al panel principal

### **Flujo Técnico Detallado**
```
Usuario → FormularioRegistro → Validación → Hash Bcrypt → BD
     → Activación 2FA → Generación Secreto → QR → Google Auth
     → Login → Verificación Credenciales → Redirección 2FA
     → Autenticar → Validación TOTP → Panel Control
```

## **Base de Datos**

### **Esquema Principal**

```sql
-- Tabla de usuarios
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Apellido` varchar(100) NOT NULL,
  `Usuario` varchar(50) NOT NULL UNIQUE,
  `Correo` varchar(100) NOT NULL UNIQUE,
  `HashMagic` varchar(255) NOT NULL,
  `secret_2fa` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de auditoría
CREATE TABLE `intentos_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `ipRemoto` varchar(45) NOT NULL,
  `deteccionAnomalia` tinyint(1) NOT NULL,
  `fecha_intento` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### **Configuración de Privilegios**
```sql
-- Verificar privilegios concedidos
SHOW GRANTS FOR 'app_web_user'@'localhost';
```

## **Tecnologías Implementadas**

### **Backend**
- **PHP 7.4+** - Lenguaje de programación del lado del servidor
- **MySQL** - Sistema de gestión de base de datos
- **Composer** - Gestor de dependencias PHP
- **Google Authenticator** - Librería para 2FA

### **Frontend**
- **HTML5** - Estructura semántica
- **CSS3** - Estilos y diseño responsive
- **JavaScript** - Validaciones del lado del cliente
- **jQuery** - Manipulación del DOM y AJAX

### **Seguridad**
- **Bcrypt** - Algoritmo de hashing de contraseñas
- **TOTP** - Estándar para contraseñas de un solo uso
- **PDO** - Extensión para acceso seguro a BD
- **CSRF Tokens** - Protección contra cross-site request forgery

## **Dificultades y Soluciones**

### **Problema 1: Generación de Códigos QR**
**Descripción**: Inicialmente los códigos QR no se generaban correctamente en algunos servidores.

**Solución**: Implementación de múltiples métodos de generación, incluyendo el uso directo del protocolo `otpauth://` y servicios externos como fallback.

### **Problema 2: Configuración de Composer**
**Descripción**: Dificultades con la autocarga de clases en diferentes entornos.

**Solución**: Verificación de la versión de PHP y regeneración del autocargador mediante `composer dump-autoload`.

### **Problema 3: Privilegios de Base de Datos**
**Descripción**: Configuración incorrecta de permisos para el usuario de aplicación.

**Solución**: Implementación del principio de privilegios mínimos y verificación mediante `SHOW GRANTS`.

## **Referencias Bibliográficas**

1. **OWASP Foundation**. (2025). *OWASP Application Security Verification Standard*. Recuperado de: https://owasp.org/www-project-application-security-verification-standard/
2. **PHP Documentation**. (2025). *PHP: Password Hashing*. Recuperado de: https://www.php.net/manual/en/book.password.php
3. **Google Authenticator**. (2025). *TOTP: Time-Based One-Time Password Algorithm*. RFC 6238
4. **Composer Documentation**. (2025). *Dependency Manager for PHP*. Recuperado de: https://getcomposer.org/doc/
5. **Sonata Project**. (2025). *Google Authenticator PHP Library*. Recuperado de: https://github.com/sonata-project/GoogleAuthenticator

## **Información del Desarrollador**

---

**Este sistema de autenticación ha sido desarrollado como parte del curso de Ingeniería Web en la Universidad Tecnológica de Panamá:**

- **Nombre**: Nathaly Bonilla Mcklean
- **Correo**:
    - **Institucional**: nathaly.bonilla1@utp.ac.pa 
    - **Github**: githubmcklean@gmail.com
    - **Profesional**: nbmcklean@gmail.com

- **Nombre**: Abdiel Abrego
- **Correo**:
    - **Institucional**: abdiel.abrego1@utp.ac.pa 


- **Curso**: Ingeniería Web
- ** Instructora**: Ing. Irina Fong
- **Período Académico**: II Semestre 2025
- **Fecha de Entrega**: 12 de noviembre de 2025

---

<p align="center">
  <strong>Universidad Tecnológica de Panamá</strong><br>
  Facultad de Ingeniería de Sistemas Computacionales<br>
  Ingeniería de Software<br>
  II Semestre 2025
</p>

<div align="center">
  
  ** Sistema de Autenticación Segura con 2FA** · **Desarrollado con PHP y MySQL**
  
</div>