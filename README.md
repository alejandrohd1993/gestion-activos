# 🧰 Sistema de Gestión de Activos y Mantenimientos

⚡ Persistencia Digital 

Sistema web desarrollado por **Persistencia Digital** para la gestión integral de **generadores eléctricos, activos, mantenimientos, servicios, gastos y reportes**.  
Permite controlar de manera precisa el estado operativo de los equipos, programar mantenimientos, registrar usos (horómetro) y administrar gastos asociados.

Aplicación web desarrollada en **Laravel + Filament PHP + MySQL** para la gestión integral de activos, generadores, mantenimientos, servicios, gastos y reportes.  
Permite a las empresas controlar de manera eficiente el estado, uso y costos operativos de sus equipos.

---

## 🚀 Características principales

- **Gestión de Activos**  
  Control detallado de equipos, generadores y componentes asociados.

- **Módulo de Mantenimientos**  
  Programación, ejecución y seguimiento de mantenimientos preventivos y correctivos.  
  Compatible con alertas por horómetro y fechas de vencimiento.

- **Módulo de Servicios**  
  Creación de servicios asociados a clientes y activos, con seguimiento del estado (`Pendiente`, `En progreso`, `Completado`).

- **Registro de Usos (Horómetros)**  
  Control de horas de operación de generadores con validación de formato `HH:MM:SS`.

- **Gestión de Gastos**  
  Registro de gastos generales y específicos, asociados tanto a mantenimientos como a servicios.  
  Permite relacionar gastos a un activo o generador específico.

- **Reportes y Estadísticas**  
  Visualización de reportes por servicio, activo o periodo.  
  Compatible con exportación de datos y gráficos en Filament.

- **Roles y Permisos (Filament Shield)**  
  Control de acceso mediante roles:
  - 🧑‍💼 **Administrador:** Acceso completo al sistema.  
  - 🧰 **Operador:** Acceso a servicios, mantenimientos y reportes operativos.  
  - 💰 **Contabilidad:** Acceso a módulos de gastos, facturación y reportes financieros.

- **Notificaciones y Calendario**  
  Alertas automáticas de mantenimientos programados y vencimientos.

---

## 🏗️ Tecnologías utilizadas

| Componente | Tecnología |
|-------------|-------------|
| **Backend** | Laravel 11 |
| **Panel administrativo** | Filament PHP v4 |
| **Base de datos** | MySQL 8 |
| **Frontend** | Tailwind CSS |
| **Control de acceso** | Filament Shield |
| **ORM** | Eloquent |
| **Notificaciones** | Laravel Notifications |
| **Autenticación** | Laravel Breeze / Filament Auth |
| **Despliegue** | Docker / Laravel Forge *(opcional)* |

---

## ⚙️ Instalación y configuración

### 1️⃣ Clonar el repositorio
```bash
git clone https://github.com/alejandrohd1993/gestion-activos
cd gestion-activos
