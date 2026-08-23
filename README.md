# Sistema Digital de Gestión de Citas y Turnos
### Hospital Municipal Plan 3000 — Santa Cruz de la Sierra, Bolivia

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![IEEE 830](https://img.shields.io/badge/IEEE-830--1998-blue?style=for-the-badge)

---

## 📌 Descripción del Proyecto

El **Sistema Digital de Gestión de Citas y Turnos** es una solución tecnológica desarrollada bajo el estándar **IEEE 830-1998** e implementada mediante metodología **Scrum** para el **Hospital Municipal Plan 3000** (Distrito Municipal 8, Santa Cruz de la Sierra). 

El sistema implementa un **modelo híbrido multicanal** (digital, presencial en ventanilla y telefónico vía Call Center) orientado a mitigar el impacto de suspensiones de servicios, reducir la incertidumbre del paciente y optimizar la asignación transparente de turnos médicos.

### 👥 Integrantes del Proyecto
* **Gabriel Alcon** — *Desarrollador Front-end / UI/UX*
* **Rudi Condo** — *Desarrollador Back-end / API*
* **Olver Alvarez** — *Desarrollador Full-stack / Base de Datos / Product Owner*
* **Mijail Galarza** — *Desarrollador Front-end / QA / Scrum Master*

---

## 🛠️ Especificaciones Técnicas y Requisitos del Sistema

### 💻 Requisitos de Hardware Mínimos
* **Procesador:** Dual Core 2.0 GHz o superior.
* **Memoria RAM:** 4 GB mínimo (8 GB recomendado).
* **Almacenamiento:** 2 GB de espacio libre en disco.

### ⚙️ Requisitos de Software
* **Sistema Operativo:** Windows 10/11, Linux (Ubuntu 20.04+), o macOS.
* **PHP:** Versión 8.2 o superior.
  * Extensiones PHP requeridas: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`.
* **Base de Datos:** MySQL 8.0+ o MariaDB 10.4+ (Servidor XAMPP / Laragon).
* **Gestor de Dependencias:** Composer 2.0+.
* **Navegador Web:** Google Chrome, Mozilla Firefox, Microsoft Edge o Safari (versiones actualizadas).

---

## 🚀 Guía de Despliegue e Instalación

### 1️⃣ Clonar o Descargar el Proyecto
Ubique el proyecto en su directorio local de trabajo:
```bash
cd c:\Users\kevin\Documents\proyecto-SI-II
```

### 2️⃣ Configurar las Variables de Entorno (`.env`)
Asegúrese de contar con el archivo `.env` en la raíz del proyecto. Si no existe, copie el archivo de ejemplo:
```bash
cp .env.example .env
```

Verifique la configuración de conexión a la base de datos en `.env`:
```env
APP_NAME="Hospital Plan 3000"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_LOCALE=es
APP_FALLBACK_LOCALE=es

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hospital_plan3000
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
```

### 3️⃣ Crear la Base de Datos en MySQL
Asegúrese de que el servidor MySQL/MariaDB esté activo (vía XAMPP o servicio local) y cree la base de datos:
```sql
CREATE DATABASE hospital_plan3000 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4️⃣ Instalar Dependencias del Proyecto
Ejecute la instalación de paquetes con Composer:
```bash
composer install
```

### 5️⃣ Generar la Clave de la Aplicación
```bash
php artisan key:generate
```

### 6️⃣ Ejecutar Migraciones y Población de Datos Iniciales (Seeders)
Este comando creará las 10 tablas del diccionario de datos y poblará las especialidades, médicos, agendas y cuentas de usuario:
```bash
php artisan migrate:fresh --seed
```

### 7️⃣ Iniciar el Servidor de Desarrollo
```bash
php artisan serve --host=127.0.0.1 --port=8000
```
Acceda desde su navegador web a: **`http://127.0.0.1:8000/login`**

---

## 🔑 Credenciales de Acceso para Pruebas (Demo)

El sistema cuenta con 4 roles preconfigurados con permisos específicos (**RBAC**):

| Rol | Usuario / CI | Contraseña | Funcionalidad Principal |
| :--- | :--- | :--- | :--- |
| **PACIENTE** | CI: `12345678` (o `juan.perez@mail.com`) | `paciente123` | Dashboard de Paciente, Solicitar Citas en 3 Pasos, Cancelar Citas, Ver Notificaciones. |
| **PERSONAL VENTANILLA** | `ventanilla@plan3000.gob.bo` | `ventanilla123` | Ventanilla de Atención Presencial, Registro de Pacientes Walk-in, Asignación de Citas. |
| **MÉDICO** | `jperez@plan3000.gob.bo` | `medico123` | Agenda Médica Personal, Atender Citas, Registrar Inasistencias de Pacientes. |
| **ADMINISTRADOR** | `admin@plan3000.gob.bo` | `admin123` | Panel de Administración, CRUD Usuarios y Roles, Auditoría y Módulo de Contingencia. |

---

## 🧪 Ejecución de Pruebas Automáticas

Para validar la integridad de la base de datos, flujo de citas y control de acceso:
```bash
php artisan test
```

---

## 📊 Estructura de la Base de Datos (10 Tablas)

1. `roles`: Definición de roles del sistema (`PACIENTE`, `MEDICO`, `CALL_CENTER`, `ADMIN`).
2. `usuarios`: Credenciales de acceso, contraseñas encriptadas con `bcrypt` y estado de la cuenta.
3. `pacientes`: Datos específicos de pacientes, Cédula de Identidad (`ci`) y contactos de emergencia.
4. `especialidades`: Catálogo de especialidades médicas y duración estándar del turno.
5. `medicos`: Perfil profesional de médicos, colegiatura y vinculación a especialidad.
6. `agendas`: Bloques de tiempo, cupos disponibles y estado de la disponibilidad (`DISPONIBLE`, `BLOQUEADO`, `COMPLETO`).
7. `citas`: Registro de solicitudes de citas, estado (`SOLICITADA`, `CONFIRMADA`, `ATENDIDA`, `CANCELADA`) y motivo.
8. `notificaciones`: Registro de envíos simulados vía WhatsApp, SMS y correo electrónico.
9. `auditorias`: Trazabilidad completa de acciones de usuarios e IP de origen (Cumplimiento RNF04).
10. `configuraciones`: Parámetros institucionales del hospital e interoperabilidad SUIS Bolivia.

---

## 🌐 Despliegue en Servidor de Producción (Apache / Nginx)

Para desplegar la aplicación en un servidor IIS, Nginx o Apache de producción:

1. Configurar la raíz de documentos (DocumentRoot) apuntando a la carpeta **`public/`** del proyecto.
2. Configurar la directiva `AllowOverride All` en Apache para habilitar la reescritura de URLs en `.htaccess`.
3. Establecer `APP_ENV=production` y `APP_DEBUG=false` en el archivo `.env`.
4. Optimizar cachés de Laravel:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
