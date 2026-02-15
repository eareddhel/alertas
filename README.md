# Repositorio de Alertas

Recurso reutilizable de alertas para proyectos PHP con frontend Bootstrap.

Objetivo: copiar la carpeta `alertas` a cualquier proyecto y tener alertas funcionando sin reconstruir el sistema.

## Panel de Configuración 🎨

**Nuevo**: ahora incluye un **panel profesional con 7 pestañas** (`config-panel.php`) para personalizar:

### Pestañas disponibles

1. **Posición** (9 opciones)
2. **Tamaños** (ancho, padding, fuente, redondeado)
3. **Colores** (fondo, borde, texto, icono por tipo)
4. **Duración** (cierre automático por tipo)
5. **Entrada** - Dirección y velocidad de animación (NUEVO)
6. **Acciones** (guardar, restaurar, descargar)
7. **Previsualización** (demo en vivo)

### Acceso al panel

Desde el archivo `index.php`, verás un botón destacado **"Panel de configuración"** o accede directamente a:

```
http://localhost/alertas/config-panel.php
```

### Persistencia de Configuración

La configuración se guarda en el archivo auto-generado `alertas-config.php` y persiste entre sesiones. Puedes:

1. Usar **"Guardar configuración"** para aplicar cambios inmediatamente
2. Usar **"Descargar configuración"** para obtener `alertas-config.php` editable
3. Usar **"Restaurar valores por defecto"** desde alerta de confirmación

El archivo es un retorno PHP puro, completamente editable.

## Estructura

- `php/AlertRepository.php`: recolecta alertas desde query string y sesión.
- `templates/alerts.php`: renderiza contenedor e inyecta assets.
- `assets/alerts.js`: motor de toasts y API global `AlertSystem`.
- `assets/alerts.css`: estilos de alertas.
- `examples/layout_footer_example.php`: integración mínima.
- `alertas-config.php`: archivo auto-generado con configuración persistente (editable).
- `config-panel.php`: panel profesional con 7 pestañas.
- `assets/js/config-panel.js`: lógica del panel (estilos dinámicos, tab switching).
- `index.php`: página de ejemplos con acceso al panel.

## Integración rápida

1. Cargar el repositorio y construir alertas desde request:

```php
<?php
session_start();
require_once __DIR__ . '/../alertas/php/AlertRepository.php';

$alertRepository = new AlertRepository();
$alerts = $alertRepository->collect($_GET, $_SESSION);
```

2. Incluir la plantilla en tu layout principal:

```php
<?php
$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = rtrim(dirname($scriptName), '/');
$basePath = $basePath === '.' ? '' : $basePath;
$alertsCssPath = ($basePath !== '' ? $basePath : '') . '/assets/alerts.css';
$alertsJsPath = ($basePath !== '' ? $basePath : '') . '/assets/alerts.js';
include __DIR__ . '/../alertas/templates/alerts.php';
```

3. Mostrar alertas desde JavaScript cuando lo necesites:

```javascript
AlertSystem.notify('Guardado correctamente', 'success', 'Listo');
AlertSystem.notify('No se pudo guardar', 'danger', 'Error');
```

## Guía completa (paso a paso)

### 1) Copiar carpeta

Copia la carpeta `alertas` dentro de tu proyecto. Ejemplo:

```
/mi-proyecto
  /alertas
  /public
  /app
```

### 2) Preparar tu controlador o página

En el archivo PHP que procesa la petición:

- inicia sesión,
- carga `AlertRepository`,
- recolecta alertas (`query string` + `flash`),
- y deja `$alerts` disponible para el layout.

```php
<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../alertas/php/AlertRepository.php';

$alertRepository = new AlertRepository();
$alerts = $alertRepository->collect($_GET, $_SESSION);
```

### 3) Incluir la plantilla en tu layout (footer)

Incluye `templates/alerts.php` cerca de `</body>` para que el contenedor y scripts se carguen al final.

```php
<?php
// Variables esperadas por templates/alerts.php:
// - $alerts
// - $alertsCssPath
// - $alertsJsPath

$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = rtrim(dirname($scriptName), '/');
$basePath = $basePath === '.' ? '' : $basePath;

$alertsCssPath = ($basePath !== '' ? $basePath : '') . '/assets/alerts.css';
$alertsJsPath = ($basePath !== '' ? $basePath : '') . '/assets/alerts.js';

include __DIR__ . '/../alertas/templates/alerts.php';
```

Nota: si esas rutas no coinciden con tu estructura final, la plantilla tiene fallback automático por URL para intentar resolverlas.

### 4) Lanzar alertas manuales desde JavaScript

Formato simple (mensaje, tipo, titulo):
```javascript
AlertSystem.notify('Cambios guardados', 'success', 'Listo');
AlertSystem.notify('Error en la solicitud', 'danger', 'Oops');
```

Formato objeto (mas opciones):
```javascript
AlertSystem.show({
    type: 'success',
    title: 'Actualizado',
    message: 'Perfil actualizado correctamente',
    duration: 3000,
    dismissible: true
});
```

Las animaciones configuradas se aplican automáticamente.

### 5) Alertas flash (backend -> redirect -> frontend)

Usa `push()` antes de redirigir. En la nueva carga, `collect()` las muestra y las limpia de sesión.

```php
$alertRepository->push($_SESSION, [
    'type' => 'success',
    'title' => 'Perfil',
    'message' => 'Cambios guardados correctamente'
]);

header('Location: /perfil');
exit;
```

## Animaciones de entrada (NUEVO) ⭐

### Direcciones disponibles

- **Automático**: ajusta según posición (recomendado)
- **Desde arriba**: para arrays en top
- **Desde abajo**: para arrays en bottom
- **Desde izquierda**: para arrays a la izq
- **Desde derecha**: para arrays a la der
- **Desde el centro**: efecto pop-in con rebote sutil

### Velocidades

- **Lento**: 450ms (premium feel)
- **Normal**: 300ms (recomendado)
- **Rápido**: 200ms (discreto)

### Ejemplos rápidos

Alertas discretas (abajo-derecha, desde abajo, rápido):
```
Posición: Abajo-derecha
Entrada: Desde abajo
Velocidad: Rápido
```

Alertas con rebote (centro, desde centro):
```
Posición: Centro
Entrada: Desde el centro
Velocidad: Normal
```

## Patrón recomendado (controller + layout)

### Controller (ejemplo completo)

```php
<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../alertas/php/AlertRepository.php';

$alertRepository = new AlertRepository();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ok = true; // tu validación o guardado real

    if ($ok) {
        $alertRepository->push($_SESSION, [
            'type' => 'success',
            'title' => 'Operación',
            'message' => 'Datos guardados correctamente.'
        ]);
    } else {
        $alertRepository->push($_SESSION, [
            'type' => 'danger',
            'title' => 'Operación',
            'message' => 'No fue posible guardar los datos.'
        ]);
    }

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

$alerts = $alertRepository->collect($_GET, $_SESSION);

require __DIR__ . '/layout.php';
```

### Layout (fragmento)

```php
<!-- contenido de la página -->

<?php
$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = rtrim(dirname($scriptName), '/');
$basePath = $basePath === '.' ? '' : $basePath;

$alertsCssPath = ($basePath !== '' ? $basePath : '') . '/assets/alerts.css';
$alertsJsPath = ($basePath !== '' ? $basePath : '') . '/assets/alerts.js';

include __DIR__ . '/../alertas/templates/alerts.php';
?>
</body>
</html>
```

## Checklist (2 minutos)

1. Copia la carpeta `alertas` completa dentro de tu proyecto.
2. Inicia sesión con `session_start();` en el entrypoint.
3. Carga `AlertRepository.php`, recolecta alertas y pásalas a la plantilla.
4. Incluye `templates/alerts.php` al final del layout (antes de `</body>`).
5. Dispara alertas con `AlertSystem.notify()` o `AlertSystem.show()`.
6. (Opcional) Personaliza en `config-panel.php` o edita `alertas-config.php`.

Si las rutas de assets fallan, la plantilla intenta resolver fallback automáticamente.

## Troubleshooting

### Solo funcionan alertas JS y no las del backend

- Verifica que `$alerts = $alertRepository->collect($_GET, $_SESSION);` se esté ejecutando.
- Verifica que incluyes `templates/alerts.php` en la misma petición donde renderizas HTML.
- Verifica que `session_start();` se ejecuta antes de `collect()` y `push()`.

### No cargan CSS/JS de alertas

- Revisa `$alertsCssPath` y `$alertsJsPath`.
- Si moviste rutas, usa la versión dinámica con `$basePath`.
- La plantilla tiene fallback por URL y por error de script para recuperación automática.

### Después de redirección no aparece el flash

- Confirma que llamas `push()` antes de `header('Location: ...')`.
- Asegura `exit;` inmediatamente después del `header()`.
- Comprueba que la sesión no se reinicia entre requests.

## Alertas por sesión (flash)

```php
$alertRepository->push($_SESSION, [
    'type' => 'success',
    'title' => 'Perfil',
    'message' => 'Cambios guardados'
]);
header('Location: /perfil');
exit;
```

## Tipos soportados

- `success`
- `danger`
- `warning`
- `info`

## Recomendaciones para reutilizar en cualquier proyecto

- Conserva la carpeta `alertas` sin cambios para facilitar upgrades.
- Centraliza la inclusión de `templates/alerts.php` en un único layout.
- Usa `push()` para eventos de backend (guardar, eliminar, autenticar).
- Usa `AlertSystem.notify()` o `AlertSystem.show()` para eventos frontend.
- Personaliza animaciones en `config-panel.php` según UX deseado.
- Edita `alertas-config.php` directamente si necesitas programación avanzada.

## Versionado y releases

- Este repositorio usa versionado semántico: `MAJOR.MINOR.PATCH`.
- Historial de cambios: ver `CHANGELOG.md`.
- Versión estable inicial recomendada: `v1.0.0`.

Regla práctica:

- `PATCH`: correcciones internas sin romper integración.
- `MINOR`: mejoras compatibles (nuevas opciones o utilidades).
- `MAJOR`: cambios que requieren ajustar integración existente.

## Integración en proyectos sin Bootstrap

Sí es posible usar este sistema sin Bootstrap, con una salvedad importante:

- `assets/alerts.css` no depende de Bootstrap para el layout principal de las alertas.
- `assets/alerts.js` sí usa clases de iconos `bi bi-*` (Bootstrap Icons).

### Opción A (recomendada): mantener solo Bootstrap Icons

No necesitas Bootstrap CSS completo. Basta con cargar Bootstrap Icons en tu `<head>`:

```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
```

Con eso, las alertas se verán correctamente con iconos.

### Opción B: no usar ningún paquete externo

Si no quieres Bootstrap Icons, tienes dos alternativas:

1. Editar `assets/alerts.js` para no renderizar el `<i class="bi ...">`.
2. Reemplazar iconos por texto/emoji (`✓`, `⚠`, `ℹ`) o por SVG inline.

### Checklist sin Bootstrap

1. Copia carpeta `alertas`.
2. Inicia sesión (`session_start();`).
3. Recolecta alertas con `collect($_GET, $_SESSION)`.
4. Incluye `templates/alerts.php` antes de `</body>`.
5. Carga Bootstrap Icons (opción A) o adapta iconos (opción B).

### Snippet mínimo sin Bootstrap (con iconos)

```php
<?php
session_start();
require_once __DIR__ . '/../alertas/php/AlertRepository.php';

$alertRepository = new AlertRepository();
$alerts = $alertRepository->collect($_GET, $_SESSION);

$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = rtrim(dirname($scriptName), '/');
$basePath = $basePath === '.' ? '' : $basePath;

$alertsCssPath = ($basePath !== '' ? $basePath : '') . '/assets/alerts.css';
$alertsJsPath = ($basePath !== '' ? $basePath : '') . '/assets/alerts.js';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <h1>Mi proyecto sin Bootstrap</h1>

    <?php include __DIR__ . '/../alertas/templates/alerts.php'; ?>
</body>
</html>
```

## Estado actual

- Portable: sí (copiar carpeta + incluir plantilla).
- Persistencia: archivo `alertas-config.php` auto-generado y editable.
- Configuracion: panel profesional con 7 pestañas + edición en archivo.
- Animaciones: 5 direcciones + 3 velocidades + automático.
- Dependencia frontend opcional: Bootstrap Icons.
- Fallback: activo (ruta URL + error de carga del script).

## Despliegue automático (GitHub -> FTP)

Este repositorio incluye el workflow `/.github/workflows/deploy-ftp.yml` para desplegar automáticamente por FTP cuando haces push a `main` o `master`.

### 1) Crear secretos en GitHub

En tu repositorio de GitHub, ve a:

- `Settings` -> `Secrets and variables` -> `Actions` -> `New repository secret`

Crea estos secretos:

- `FTP_SERVER`: host FTP (ejemplo: `ftp.tudominio.cl` o IP del servidor).
- `FTP_USERNAME`: usuario FTP.
- `FTP_PASSWORD`: contraseña FTP.
- `FTP_SERVER_DIR`: carpeta remota de despliegue (ejemplo: `/public_html/` o `/public_html/alertas/`).

Opcionales:

- `FTP_PROTOCOL`: `ftp` o `ftps` (si no existe, usa `ftp`).
- `FTP_PORT`: puerto FTP (si no existe, usa `21`).

### 2) Ejecutar despliegue

- Automático: cada `push` a `main` o `master`.
- Manual: pestaña `Actions` -> workflow `Deploy FTP` -> `Run workflow`.

### 3) Recomendaciones

- No guardes credenciales FTP en archivos del proyecto.
- Si expones una contraseña por error, cámbiala inmediatamente en tu hosting y actualiza el secreto en GitHub.
- Ajusta `FTP_SERVER_DIR` hasta que `https://tu-dominio/` cargue los archivos correctos.
