# Sistema Digital de Gestión de Citas y Turnos
### Hospital Municipal Plan 3000 — Santa Cruz de la Sierra, Bolivia

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
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

## 🐳 Opción 1: Despliegue con Docker (Recomendado - 2 Comandos)

Esta opción permite levantar todo el entorno (Laravel 12, PHP 8.2, Node.js/Vite y MySQL 8.0) sin necesidad de instalar PHP ni MySQL manualmente en su máquina.

### 📋 Requisitos Previos para Docker
1. Tener instalado **Docker Desktop** en Windows/Mac o **Docker Engine + Docker Compose** en Linux.
2. **IMPORTANTE:** Abrir e iniciar **Docker Desktop** antes de ejecutar los comandos.
   > ⚠️ **Nota en Windows:** Asegúrese de que Docker Desktop esté en estado **"Engine running"** (icono de la ballena verde en la bandeja del sistema). Si Docker Desktop está cerrado, obtendrá el error `failed to connect to the docker API`.

---

### 🚀 Pasos para Iniciar con Docker (2 Comandos)

#### **1️⃣ Paso 1: Construir y levantar los contenedores**
Abra una terminal en la raíz del proyecto y ejecute:
```bash
docker compose up -d --build
```
> *Este comando descarga la imagen de MySQL 8.0, construye la imagen de PHP 8.2 con Node.js, prepara el `.env`, compila los assets de Vite, espera la conexión a la base de datos y ejecuta las migraciones automáticamente.*

#### **2️⃣ Paso 2: Cargar datos iniciales (Seeders)**
```bash
docker compose exec app php artisan db:seed
```
> *Inserta los usuarios de prueba, especialidades, médicos y configuraciones en la base de datos MySQL del contenedor.*

---

### 🌐 Acceso a la Aplicación
* **Aplicación Web:** [http://localhost:8000](http://localhost:8000)
* **Base de Datos MySQL:** `localhost:3307` *(Usuario: `root`, Contraseña: `root`, BD: `hospital_plan3000`)*

### 🛠️ Comandos Útiles de Docker
* **Ver logs en tiempo real:** `docker compose logs -f`
* **Detener los contenedores:** `docker compose down`
* **Reiniciar el proyecto:** `docker compose restart`
* **Entrar a la consola del contenedor:** `docker compose exec app bash`

---

## 💻 Opción 2: Despliegue Local Tradicional (PHP + XAMPP)

Si prefiere ejecutar el proyecto directamente en su sistema operativo sin Docker:

### ⚙️ Requisitos de Software
* **PHP:** Versión 8.2 o superior (`pdo_mysql`, `mbstring`, `gd`, `zip`, `bcmath`, `curl`).
* **Base de Datos:** MySQL 8.0+ o MariaDB (Servidor XAMPP / Laragon en puerto 3306).
* **Gestores:** Composer 2.0+ y Node.js 18+.

### 🚀 Pasos de Instalación Manual

1. **Configurar el archivo `.env`:**
   ```bash
   cp .env.example .env
   ```
   Configurar credenciales de MySQL local (`DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=hospital_plan3000`, `DB_USERNAME=root`).

2. **Crear la Base de Datos:**
   Iniciar XAMPP (MySQL) y crear la base de datos `hospital_plan3000`:
   ```sql
   CREATE DATABASE hospital_plan3000 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Instalar dependencias y compilar:**
   ```bash
   composer install
   npm install
   npm run build
   ```

4. **Generar clave y migrar base de datos:**
   ```bash
   php artisan key:generate
   php artisan migrate:fresh --seed
   ```

5. **Iniciar el servidor:**
   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```
   Acceda a: **`http://127.0.0.1:8000`**

---

## 🔑 Credenciales de Acceso para Pruebas (Demo)

El sistema cuenta con 4 roles preconfigurados con permisos específicos (**RBAC**):

| Rol | Usuario / CI / Email | Contraseña | Funcionalidad Principal |
| :--- | :--- | :--- | :--- |
| **PACIENTE** | CI: `12345678` (o `juan.perez@mail.com`) | `paciente123` | Dashboard de Paciente, Solicitar Citas en 3 Pasos, Cancelar Citas, Ver Notificaciones. |
| **PERSONAL VENTANILLA** | `ventanilla@plan3000.gob.bo` | `ventanilla123` | Ventanilla de Atención Presencial, Registro de Pacientes Walk-in, Asignación de Citas. |
| **MÉDICO** | `jperez@plan3000.gob.bo` | `medico123` | Agenda Médica Personal, Atender Citas, Registrar Inasistencias de Pacientes. |
| **ADMINISTRADOR** | `admin@plan3000.gob.bo` | `admin123` | Panel de Administración, CRUD Usuarios y Roles, Auditoría y Módulo de Contingencia. |

---

## 🧪 Ejecución de Pruebas Automáticas

Para validar la integridad de la base de datos, flujo de citas y control de acceso:
* **Con Docker:**
  ```bash
  docker compose exec app php artisan test
  ```
* **En local:**
  ```bash
  php artisan test
  ```

---

## 📊 Estructura de la Base de Datos

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
11. `expedientes_medicos`: Historial y fichas clínicas de pacientes.
12. `consultas`: Registro de atenciones y consultas médicas.
13. `consultas_especialidades`: Formularios especializados por rama clínica (Cardiología, Pediatría, etc.).
14. `pagos`: Registro de comprobantes y pagos de ventanilla / caja.
15. `consultorios`: Gestión física de consultorios.
16. `documentos`: Gestión documental y archivos clínicos adjuntos.
