# 🍬 Sistema POS Karamelos

**Sistema de Punto de Venta (POS) para la gestión integral de "Karamelos".**

Este proyecto es una API RESTful desarrollada en **Laravel 12** diseñada para administrar las operaciones diarias de una tienda de dulces. Permite la gestión de productos, control de ventas, manejo de lotes y realización de cortes de caja, facilitando la generación de reportes y el control administrativo.

---

## 🚀 Características Principales

El sistema cuenta con roles de usuario (Administrador y Usuario General) y ofrece las siguientes funcionalidades:

### 🔐 Autenticación y Seguridad
- **Registro e Inicio de Sesión**: Autenticación segura vía API Tokens (Sanctum/JWT).
- **Gestión de Sesiones**: Login, Logout y obtención de perfil de usuario.
- **Seguridad**: Rutas protegidas por middleware y validación de roles (`admin`).

### 📦 Gestión de Productos
- **Catálogo**: Listado público de productos disponibles.
- **Administración**: CRUD completo (Crear, Leer, Actualizar, Eliminar) de productos (Solo Admin).
- **Control de Stock**: (Implícito en la lógica de negocio).

### 💰 Ventas
- **Registro de Ventas**: Creación de nuevas transacciones de venta.
- **Historial**: Consulta de ventas realizadas por fecha.
- **Detalle**: Visualización detallada de cada venta.

### 📊 Lotes y Cortes de Caja
- **Cortes (Cuts)**: Funcionalidad para realizar cortes de caja, esencial para el cierre de turnos o del día.
- **Lotes de Venta**: Agrupación y gestión de lotes para reportes organizados.
- **Reportes Admin**: Visualización de todos los cortes y lotes (Solo Admin).

---

## 🛠️ Tecnologías Utilizadas

- **Framework Backend**: [Laravel 12](https://laravel.com)
- **Lenguaje**: PHP 8.2+
- **Base de Datos**: MySQL / MariaDB (o SQLite para pruebas)
- **Autenticación**: Laravel Sanctum / JWT Auth
- **Documentación API**: L5-Swagger (OpenAPI)

---

## 📋 Requisitos Previos

Asegúrate de tener instalado lo siguiente en tu entorno local:

- [PHP](https://www.php.net/downloads) >= 8.2
- [Composer](https://getcomposer.org/)
- [XAMPP](https://www.apachefriends.org/es/index.html) (o cualquier servidor MySQL)
- [Node.js](https://nodejs.org/) & NPM (Opcional, para assets frontend si aplica)

---

## ⚡ Instalación y Configuración

Sigue estos pasos para levantar el proyecto en tu entorno local:

1. **Clonar el repositorio**
   ```bash
   git clone <URL_DEL_REPOSITORIO>
   cd sistema_karamelos
   ```

2. **Instalar dependencias de PHP**
   ```bash
   composer install
   ```

3. **Configurar el entorno**
   Copia el archivo de ejemplo y configura tus credenciales de base de datos.
   ```bash
   cp .env.example .env
   ```
   *Abre el archivo `.env` y ajusta `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` según tu configuración local.*

4. **Generar clave de aplicación**
   ```bash
   php artisan key:generate
   ```

5. **Ejecutar migraciones (Base de Datos)**
   Crea las tablas necesarias en tu base de datos.
   ```bash
   php artisan migrate
   ```

6. **JWT Secret (Si aplica)**
   Si el sistema usa JWT, genera el secreto:
   ```bash
   php artisan jwt:secret
   ```

7. **Iniciar el servidor local**
   ```bash
   php artisan serve
   ```
   El sistema estará disponible en `http://localhost:8000`.

---

## 📖 Documentación de la API

El proyecto incluye documentación generada con Swagger. Una vez iniciado el servidor, puedes visitar:

```
http://localhost:8000/api/documentation
```
*(Asegúrate de generar la documentación si no está visible ejecutando: `php artisan l5-swagger:generate`)*

---

## 🛣️ Endpoints Principales

Aquí tienes un resumen rápido de las rutas disponibles en `api.php`:

| Método | Endpoint | Descripción | Acceso |
|--------|----------|-------------|--------|
| POST | `/api/login` | Iniciar sesión | Público |
| POST | `/api/register` | Registrar usuario | Público |
| GET | `/api/products` | Ver productos | Público |
| POST | `/api/sales` | Registrar venta | Auth |
| POST | `/api/cuts` | Realizar corte | Auth |
| GET | `/api/lots` | Ver lotes | Admin |
| GET | `/api/cuts/{lot_id}` | Ver cortes de un lote | Admin |

---

## 📄 Licencia

Este proyecto es software de código abierto licenciado bajo la [MIT license](https://opensource.org/licenses/MIT).
