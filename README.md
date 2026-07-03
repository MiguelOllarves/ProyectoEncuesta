# Documentación Completa del Proyecto Encuesta

## 1. Resumen del proyecto
Este proyecto es una aplicación de encuestas con un panel administrativo de inteligencia operacional. Está desarrollado en PHP nativo, JavaScript y Bootstrap en el frontend, con una API REST simple que consume datos de una base de datos PostgreSQL.

## 2. Estructura del repositorio
- `public/`
  - `index.html`: formulario de captura de reportes de cuadrantes.
  - `login.html`: pantalla de acceso al dashboard.
  - `dashboard.html`: dashboard administrativo moderno con métricas y gráficos.
  - `assets/`: contiene recursos e scripts de frontend.
- `api/`
  - `auth/`
    - `login.php`: autenticación de admin.
    - `check.php`: verificación de sesión activa.
  - `config/`
    - `db.php`: función de conexión PDO a PostgreSQL.
  - `encuestas/`
    - `guardar.php`: endpoint para almacenar reportes.
    - `estadisticas.php`: endpoint para obtener métricas y datos del dashboard.
- `setup.php`: script de inicializac
ssdsdsads