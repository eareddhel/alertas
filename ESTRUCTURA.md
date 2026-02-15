# Estructura del Proyecto - Sistema de Alertas

```
alertas/
├── index.php                          # página principal con ejemplos de alertas
├── config-panel.php                   # panel de configuración del sistema
├── alertas-config.php                 # archivo de configuración editable (generado automáticamente)
│
├── php/
│   └── AlertRepository.php            # clase para gestionar alertas
│
├── templates/
│   └── alerts.php                     # template HTML de las alertas
│
├── assets/
│   ├── js/
│   │   ├── alerts.js                  # motor principal del sistema de alertas
│   │   └── config-panel.js            # lógica del panel de configuración
│   └── css/
│       ├── alerts.css                 # estilos base del sistema de alertas
│       └── (config-panel.css)         # estilos del panel (si es necesario)
│
└── examples/
    └── layout_footer_example.php      # ejemplo de integración en footer
```

## Archivos Clave

### Archivos de Configuración (Editables)
- **`alertas-config.php`** - Configuración principal. Este archivo se genera automáticamente desde el panel de configuración. Contiene:
  - Posición de las alertas (9 opciones)
  - Dimensiones (ancho máximo, padding, tamaño de fuente, radio de borde)
  - Duración de alertas por tipo
  - Colores personalizados por tipo de alerta

### Archivos del Sistema (No editar)
- **`config-panel.php`** - Panel web para configurar el sistema
- **`php/AlertRepository.php`** - Clase PHP del sistema
- **`templates/alerts.php`** - Template HTML
- **`assets/js/alerts.js`** - Motor de alertas (JavaScript)
- **`assets/js/config-panel.js`** - Lógica del panel de configuración
- **`assets/css/alerts.css`** - Estilos base

## Portabilidad

El proyecto es 100% portable:
- ✅ No usa rutas absolutas
- ✅ Rutas relativas a partir de la carpeta raíz
- ✅ Configuración centralizada en `alertas-config.php`
- ✅ Se puede mover a cualquier carpeta sin cambios

## Cómo Usar

1. **Configurar alertas**: Accede a `config-panel.php` en el navegador
2. **Personalizar**: Ajusta posición, colores, tamaños, duración
3. **Guardar**: Los cambios se guardan en `alertas-config.php`
4. **Descargar**: Descarga el archivo `alertas-config.php` generado
5. **Implementar**: Copia el archivo descargado en tu proyecto

## Integración en Proyectos

Para usar el sistema de alertas en tu proyecto:

```php
<?php
require_once 'ruta/a/php/AlertRepository.php';

// Cargar configuración
$config = require 'ruta/a/alertas-config.php';

// Inicializar sistema
$alerts = new AlertRepository();
?>

<!-- Cargar estilos -->
<link rel="stylesheet" href="ruta/a/assets/css/alerts.css">

<!-- Cargar script -->
<script src="ruta/a/assets/js/alerts.js"></script>

<!-- El container es inyectado automáticamente -->
```

## Ventajas de Esta Estructura

✅ **Separación de conceptos**: HTML, CSS, JS, PHP en lugares específicos
✅ **Mantenimiento**: Fácil encontrar y actualizar cada componente
✅ **Portabilidad**: Se mueve intacto a cualquier proyecto
✅ **Escalabilidad**: Preparado para futuras mejoras
✅ **Documentación**: Código comentado y automático
