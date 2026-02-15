<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/php/AlertRepository.php';

$alertRepository = new AlertRepository();

$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/alertas/config-panel.php'));
$basePath = rtrim(dirname($scriptName), '/');
$basePath = $basePath === '.' ? '' : $basePath;

$alertsCssPath = 'assets/alerts.css';
$alertsJsPath = 'assets/alerts.js';
$alertsCssVersion = file_exists(__DIR__ . '/' . $alertsCssPath)
    ? (string) filemtime(__DIR__ . '/' . $alertsCssPath)
    : '1';
$alertsJsVersion = file_exists(__DIR__ . '/' . $alertsJsPath)
    ? (string) filemtime(__DIR__ . '/' . $alertsJsPath)
    : '1';
$alertsCssPathVersioned = $alertsCssPath . '?v=' . $alertsCssVersion;
$alertsJsPathVersioned = $alertsJsPath . '?v=' . $alertsJsVersion;

// archivo de configuración editable
$configFile = __DIR__ . '/alertas-config.php';

// función para guardar configuración en el archivo
function saveConfigToFile(string $filePath, array $config): bool {
    $content = "<?php\n";
    $content .= "/**\n";
    $content .= " * archivo de configuración de alertas\n";
    $content .= " * este archivo contiene solo la configuración editable de las alertas\n";
    $content .= " * modifica los valores según tus necesidades\n";
    $content .= " * \n";
    $content .= " * generado automáticamente por el panel de configuración\n";
    $content .= " * url: config-panel.php\n";
    $content .= " * fecha: " . date('Y-m-d H:i:s') . "\n";
    $content .= " */\n\n";
    $content .= "return [\n";
    $content .= "  // posición de las alertas en la pantalla\n";
    $content .= "  // opciones: top-left, top-center, top-right, center-left, center-center, center-right, bottom-left, bottom-center, bottom-right\n";
    $content .= "  'position' => '{$config['position']}',\n";
    $content .= "  \n";
    $content .= "  // dimensiones y espaciado\n";
    $content .= "  'maxWidth' => {$config['maxWidth']},      // ancho máximo en píxeles\n";
    $content .= "  'padding' => {$config['padding']},        // padding interno en píxeles\n";
    $content .= "  'fontSize' => {$config['fontSize']},       // tamaño de fuente en píxeles\n";
    $content .= "  'borderRadius' => {$config['borderRadius']},    // radio del borde redondeado en píxeles\n";
    $content .= "  \n";
    $content .= "  // animación de entrada\n";
    $content .= "  // opciones: auto, top, bottom, left, right, center\n";
    $content .= "  'animation' => [\n";
    $content .= "    'enterFrom' => '{$config['animation']['enterFrom']}',\n";
    $content .= "    'speed' => '{$config['animation']['speed']}'\n";
    $content .= "  ],\n";
    $content .= "  \n";
    $content .= "  // duración de las alertas en milisegundos (0 = no se cierra automáticamente)\n";
    $content .= "  'duration' => [\n";
    $content .= "    'success' => {$config['duration']['success']},\n";
    $content .= "    'danger' => {$config['duration']['danger']},\n";
    $content .= "    'warning' => {$config['duration']['warning']},\n";
    $content .= "    'info' => {$config['duration']['info']}\n";
    $content .= "  ],\n";
    $content .= "  \n";
    $content .= "  // colores por tipo de alerta\n";
    $content .= "  'colors' => [\n";
    $content .= "    'success' => [\n";
    $content .= "      'bg' => '{$config['colors']['success']['bg']}',\n";
    $content .= "      'border' => '{$config['colors']['success']['border']}',\n";
    $content .= "      'text' => '{$config['colors']['success']['text']}',\n";
    $content .= "      'icon' => '{$config['colors']['success']['icon']}'\n";
    $content .= "    ],\n";
    $content .= "    'danger' => [\n";
    $content .= "      'bg' => '{$config['colors']['danger']['bg']}',\n";
    $content .= "      'border' => '{$config['colors']['danger']['border']}',\n";
    $content .= "      'text' => '{$config['colors']['danger']['text']}',\n";
    $content .= "      'icon' => '{$config['colors']['danger']['icon']}'\n";
    $content .= "    ],\n";
    $content .= "    'warning' => [\n";
    $content .= "      'bg' => '{$config['colors']['warning']['bg']}',\n";
    $content .= "      'border' => '{$config['colors']['warning']['border']}',\n";
    $content .= "      'text' => '{$config['colors']['warning']['text']}',\n";
    $content .= "      'icon' => '{$config['colors']['warning']['icon']}'\n";
    $content .= "    ],\n";
    $content .= "    'info' => [\n";
    $content .= "      'bg' => '{$config['colors']['info']['bg']}',\n";
    $content .= "      'border' => '{$config['colors']['info']['border']}',\n";
    $content .= "      'text' => '{$config['colors']['info']['text']}',\n";
    $content .= "      'icon' => '{$config['colors']['info']['icon']}'\n";
    $content .= "    ]\n";
    $content .= "  ]\n";
    $content .= "];\n";
    
    return file_put_contents($filePath, $content) !== false;
}

// configuración por defecto (solo si no existe el archivo)
$defaultConfig = [
    'position' => 'top-right',
    'maxWidth' => 400,
    'padding' => 16,
    'fontSize' => 14,
    'borderRadius' => 8,
    'animation' => [
        'enterFrom' => 'auto',
        'speed' => 'normal'
    ],
    'duration' => [
        'success' => 3000,
        'danger' => 5000,
        'warning' => 4000,
        'info' => 4000
    ],
    'colors' => [
        'success' => ['bg' => '#d1e7dd', 'border' => '#badbcc', 'text' => '#0f5132', 'icon' => '#198754'],
        'danger' => ['bg' => '#f8d7da', 'border' => '#f5c2c7', 'text' => '#842029', 'icon' => '#dc3545'],
        'warning' => ['bg' => '#fff3cd', 'border' => '#ffecb5', 'text' => '#664d03', 'icon' => '#ffc107'],
        'info' => ['bg' => '#cff4fc', 'border' => '#b6effb', 'text' => '#055160', 'icon' => '#0dcaf0']
    ]
];

// leer configuración del archivo o usar defaults
if (file_exists($configFile)) {
    $config = array_replace_recursive($defaultConfig, require $configFile);
} else {
    $config = $defaultConfig;
    // crear archivo con configuración por defecto
    saveConfigToFile($configFile, $config);
}

// procesar cambios de configuración
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode((string) file_get_contents('php://input'), true);
    
    if ($data && isset($data['action'])) {
        if ($data['action'] === 'updateConfig') {
            // actualizar configuración
            if (isset($data['config'])) {
                $config = array_merge($config, $data['config']);
                $saved = saveConfigToFile($configFile, $config);
                http_response_code(200);
                echo json_encode([
                    'success' => $saved, 
                    'message' => $saved ? 'Configuración guardada en alertas-config.php' : 'Error al guardar'
                ]);
            }
        } elseif ($data['action'] === 'resetConfig') {
            // restaurar configuración por defecto
            $config = $defaultConfig;
            $saved = saveConfigToFile($configFile, $config);
            http_response_code(200);
            echo json_encode([
                'success' => $saved, 
                'message' => 'Configuración restaurada', 
                'config' => $config
            ]);
        }
    }
    exit;
}

// Generar CSS dinámico basado en la configuración
$positionMap = [
    'top-left' => 'top: 70px; left: 20px; right: auto;',
    'top-center' => 'top: 70px; left: 50%; transform: translateX(-50%);',
    'top-right' => 'top: 70px; right: 20px; left: auto;',
    'center-left' => 'top: 50%; left: 20px; right: auto; transform: translateY(-50%);',
    'center-center' => 'top: 50%; left: 50%; transform: translate(-50%, -50%);',
    'center-right' => 'top: 50%; right: 20px; left: auto; transform: translateY(-50%);',
    'bottom-left' => 'bottom: 20px; left: 20px; right: auto; top: auto;',
    'bottom-center' => 'bottom: 20px; left: 50%; right: auto; top: auto; transform: translateX(-50%);',
    'bottom-right' => 'bottom: 20px; right: 20px; left: auto; top: auto;'
];

$positionCSS = $positionMap[$config['position']] ?? $positionMap['top-right'];
$dynamicCSS = <<<CSS
.alerts-container {
    position: fixed;
    {$positionCSS}
    max-width: {$config['maxWidth']}px;
    z-index: 9999;
    pointer-events: none;
}

.alert-toast {
    padding: {$config['padding']}px;
    border-radius: {$config['borderRadius']}px;
}

.alert-message {
    font-size: {$config['fontSize']}px;
}

.alert-toast.alert-success {
    background-color: {$config['colors']['success']['bg']};
    border-color: {$config['colors']['success']['border']};
    color: {$config['colors']['success']['text']};
}

.alert-toast.alert-success .alert-icon {
    color: {$config['colors']['success']['icon']};
}

.alert-toast.alert-danger {
    background-color: {$config['colors']['danger']['bg']};
    border-color: {$config['colors']['danger']['border']};
    color: {$config['colors']['danger']['text']};
}

.alert-toast.alert-danger .alert-icon {
    color: {$config['colors']['danger']['icon']};
}

.alert-toast.alert-warning {
    background-color: {$config['colors']['warning']['bg']};
    border-color: {$config['colors']['warning']['border']};
    color: {$config['colors']['warning']['text']};
}

.alert-toast.alert-warning .alert-icon {
    color: {$config['colors']['warning']['icon']};
}

.alert-toast.alert-info {
    background-color: {$config['colors']['info']['bg']};
    border-color: {$config['colors']['info']['border']};
    color: {$config['colors']['info']['text']};
}

.alert-toast.alert-info .alert-icon {
    color: {$config['colors']['info']['icon']};
}
CSS;
?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Configuración - Alertas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --color-primary: #8b7ba8;
            --color-secondary: #a5c9d4;
            --color-accent: #f4ead5;
            --color-success: #a8d5ba;
            --color-light: #f8f6f3;
            --color-text: #5a5a5a;
            --color-border: #e8dfd8;
        }

        * {
            --bs-primary: var(--color-primary);
            --bs-secondary: var(--color-secondary);
            --bs-success: var(--color-success);
            --bs-border-color: var(--color-border);
        }

        body {
            background: linear-gradient(135deg, #f8f6f3 0%, #f0ebe5 100%);
            color: var(--color-text);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .navbar-brand {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background: linear-gradient(135deg, rgba(139, 123, 168, 0.08) 0%, rgba(165, 201, 212, 0.08) 100%);
            border-bottom: 1px solid var(--color-border);
            border-radius: 12px 12px 0 0;
            padding: 1.5rem;
        }

        .card-header h5 {
            color: var(--color-primary);
            font-weight: 600;
            letter-spacing: 0.3px;
            margin: 0;
        }

        .section-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: var(--color-primary);
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-label:first-child {
            margin-top: 0;
        }

        .form-control, .form-select {
            border-color: var(--color-border);
            border-radius: 6px;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 0.2rem rgba(139, 123, 168, 0.15);
            background-color: #fff;
        }

        .color-picker-wrapper {
            position: relative;
            display: inline-block;
        }

        .color-input {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            border: 2px solid var(--color-border);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .color-input:hover {
            border-color: var(--color-primary);
            box-shadow: 0 2px 8px rgba(139, 123, 168, 0.2);
        }

        .position-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            margin: 1rem 0;
        }

        .position-btn {
            padding: 0.75rem;
            border-radius: 6px;
            border: 2px solid var(--color-border);
            background-color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--color-text);
            text-align: center;
        }

        .position-btn:hover {
            border-color: var(--color-primary);
            background-color: rgba(139, 123, 168, 0.05);
        }

        .position-btn.active {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
        }

        .sidebar-sticky {
            position: sticky;
            top: 80px;
            z-index: 100;
        }

        @media (max-width: 991px) {
            .sidebar-sticky {
                position: static;
            }
        }

        .preview-container {
            position: relative;
            background-color: #fff;
            border-radius: 8px;
            border: 2px dashed var(--color-border);
            min-height: 400px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 0.9rem;
        }

        .preview-sample-alert {
            padding: 14px;
            border-radius: 6px;
            border: 1px solid;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            animation: slideInRight 0.3s ease-out forwards;
        }

        .preview-sample-alert .icon {
            font-size: 18px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .preview-sample-alert .content {
            flex: 1;
        }

        .preview-sample-alert .title {
            font-weight: 600;
            margin-bottom: 2px;
            display: block;
        }

        .preview-sample-alert .message {
            font-size: 0.9rem;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .btn-primary {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #7a6995;
            border-color: #7a6995;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 123, 168, 0.3);
        }

        .btn-outline-primary {
            color: var(--color-primary);
            border-color: var(--color-primary);
        }

        .btn-outline-primary:hover {
            background-color: rgba(139, 123, 168, 0.1);
            border-color: var(--color-primary);
        }

        .alert-type-color {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background-color: #f9f8f6;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .alert-type-color label {
            margin: 0;
            font-weight: 500;
            min-width: 70px;
        }

        .color-fields {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 0.75rem;
            flex: 1;
        }

        .color-field {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .color-field small {
            font-size: 0.75rem;
            color: #999;
            min-width: 40px;
        }

        .form-group-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group-row .form-group {
            margin-bottom: 0;
        }

        .panel-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .panel-tab {
            border: 1px solid var(--color-border);
            background-color: #fff;
            color: var(--color-text);
            border-radius: 999px;
            padding: 0.45rem 0.9rem;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .panel-tab:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .panel-tab.active {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: #fff;
        }

        .panel-section.is-hidden {
            display: none;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }

        .sidebar-menu a {
            display: block;
            padding: 0.75rem 1rem;
            color: var(--color-text);
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover {
            background-color: rgba(139, 123, 168, 0.1);
            border-left-color: var(--color-primary);
        }

        .sidebar-menu a.active {
            background-color: rgba(139, 123, 168, 0.15);
            border-left-color: var(--color-primary);
            font-weight: 600;
            color: var(--color-primary);
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .demo-section {
            background-color: #f9f8f6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .demo-section h6 {
            color: var(--color-primary);
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .demo-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .demo-btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: none;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .demo-btn-success { background-color: #d1e7dd; color: #0f5132; }
        .demo-btn-success:hover { background-color: #c3e6cb; }
        
        .demo-btn-danger { background-color: #f8d7da; color: #842029; }
        .demo-btn-danger:hover { background-color: #f5c2c7; }
        
        .demo-btn-warning { background-color: #fff3cd; color: #664d03; }
        .demo-btn-warning:hover { background-color: #ffecb5; }
        
        .demo-btn-info { background-color: #cff4fc; color: #055160; }
        .demo-btn-info:hover { background-color: #b6effb; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-bell-fill" style="color: var(--color-primary);"></i> Sistema de Alertas
        </a>
        <span class="navbar-text ms-auto text-muted">
            <span class="badge text-bg-success">Modo Configuración</span>
        </span>
    </div>
</nav>

<main class="container my-5">
    <div class="card mb-4">
        <div class="card-body">
            <div class="panel-tabs" role="tablist">
                <button type="button" class="panel-tab active" data-tab="posicion"><i class="bi bi-arrow-left-right"></i> Posición</button>
                <button type="button" class="panel-tab" data-tab="tamaños"><i class="bi bi-rulers"></i> Tamaños</button>
                <button type="button" class="panel-tab" data-tab="colores"><i class="bi bi-palette"></i> Colores</button>
                <button type="button" class="panel-tab" data-tab="duracion"><i class="bi bi-hourglass-split"></i> Duración</button>
                <button type="button" class="panel-tab" data-tab="animacion"><i class="bi bi-lightning-charge"></i> Entrada</button>
                <button type="button" class="panel-tab" data-tab="acciones"><i class="bi bi-rocket-takeoff"></i> Acciones</button>
                <button type="button" class="panel-tab" data-tab="ejemplos"><i class="bi bi-play-circle"></i> Previsualización</button>
            </div>
        </div>
    </div>

    <div class="panel-section" data-panel="posicion">
            <!-- Posición -->
            <div class="card mb-4" id="posicion">
                <div class="card-header">
                    <h5><i class="bi bi-arrow-left-right"></i> Posición</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Selecciona dónde deseas que aparezcan las alertas en la pantalla.</p>
                    <div class="position-grid">
                        <button class="position-btn" data-position="top-left" title="Arriba a la izquierda">↖ Arriba<br>Izq</button>
                        <button class="position-btn" data-position="top-center" title="Arriba al centro">⬆ Arriba<br>Centro</button>
                        <button class="position-btn" data-position="top-right" title="Arriba a la derecha">➡ Arriba<br>Der</button>
                        <button class="position-btn" data-position="center-left" title="Centro a la izquierda">⬅ Centro<br>Izq</button>
                        <button class="position-btn" data-position="center-center" title="Centro">⬆ Centro</button>
                        <button class="position-btn" data-position="center-right" title="Centro a la derecha">Cen<br>Der ➡</button>
                        <button class="position-btn" data-position="bottom-left" title="Abajo a la izquierda">↙ Abajo<br>Izq</button>
                        <button class="position-btn" data-position="bottom-center" title="Abajo al centro">⬇ Abajo<br>Centro</button>
                        <button class="position-btn" data-position="bottom-right" title="Abajo a la derecha">Abajo<br>Der ↘</button>
                    </div>
                </div>
            </div>
    </div>

    <!-- Tamaños -->
    <div class="panel-section is-hidden" data-panel="tamaños">
            <div class="card mb-4" id="tamaños">
                <div class="card-header">
                    <h5><i class="bi bi-rulers"></i> Tamaños y espaciado</h5>
                </div>
                <div class="card-body">
                    <div class="form-group-row">
                        <div class="form-group">
                            <label for="maxWidth" class="form-label">Ancho máximo (px)</label>
                            <input type="number" class="form-control" id="maxWidth" min="300" max="800" value="<?= $config['maxWidth']; ?>" step="10">
                            <small class="text-muted">Ancho máximo de las alertas</small>
                        </div>
                        <div class="form-group">
                            <label for="padding" class="form-label">Padding interno (px)</label>
                            <input type="number" class="form-control" id="padding" min="8" max="32" value="<?= $config['padding']; ?>" step="1">
                            <small class="text-muted">Espaciado interno de la alerta</small>
                        </div>
                        <div class="form-group">
                            <label for="fontSize" class="form-label">Tamaño de fuente (px)</label>
                            <input type="number" class="form-control" id="fontSize" min="12" max="18" value="<?= $config['fontSize']; ?>" step="1">
                            <small class="text-muted">Tamaño del texto del mensaje</small>
                        </div>
                        <div class="form-group">
                            <label for="borderRadius" class="form-label">Redondeado (px)</label>
                            <input type="number" class="form-control" id="borderRadius" min="0" max="20" value="<?= $config['borderRadius']; ?>" step="1">
                            <small class="text-muted">Radio del borde redondeado</small>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <!-- Colores -->
    <div class="panel-section is-hidden" data-panel="colores">
            <div class="card mb-4" id="colores">
                <div class="card-header">
                    <h5><i class="bi bi-palette"></i> Colores por tipo</h5>
                </div>
                <div class="card-body">
                    <!-- Success -->
                    <div class="alert-type-color">
                        <label><i class="bi bi-check-circle-fill"></i> Éxito</label>
                        <div class="color-fields">
                            <div class="color-field">
                                <small>Fondo</small>
                                <input type="color" class="color-input" data-color="success-bg" value="<?= $config['colors']['success']['bg']; ?>">
                            </div>
                            <div class="color-field">
                                <small>Borde</small>
                                <input type="color" class="color-input" data-color="success-border" value="<?= $config['colors']['success']['border']; ?>">
                            </div>
                            <div class="color-field">
                                <small>Texto</small>
                                <input type="color" class="color-input" data-color="success-text" value="<?= $config['colors']['success']['text']; ?>">
                            </div>
                            <div class="color-field">
                                <small>Icono</small>
                                <input type="color" class="color-input" data-color="success-icon" value="<?= $config['colors']['success']['icon']; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Danger -->
                    <div class="alert-type-color">
                        <label><i class="bi bi-exclamation-circle-fill"></i> Error</label>
                        <div class="color-fields">
                            <div class="color-field">
                                <small>Fondo</small>
                                <input type="color" class="color-input" data-color="danger-bg" value="<?= $config['colors']['danger']['bg']; ?>">
                            </div>
                            <div class="color-field">
                                <small>Borde</small>
                                <input type="color" class="color-input" data-color="danger-border" value="<?= $config['colors']['danger']['border']; ?>">
                            </div>
                            <div class="color-field">
                                <small>Texto</small>
                                <input type="color" class="color-input" data-color="danger-text" value="<?= $config['colors']['danger']['text']; ?>">
                            </div>
                            <div class="color-field">
                                <small>Icono</small>
                                <input type="color" class="color-input" data-color="danger-icon" value="<?= $config['colors']['danger']['icon']; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Warning -->
                    <div class="alert-type-color">
                        <label><i class="bi bi-exclamation-triangle-fill"></i> Aviso</label>
                        <div class="color-fields">
                            <div class="color-field">
                                <small>Fondo</small>
                                <input type="color" class="color-input" data-color="warning-bg" value="<?= $config['colors']['warning']['bg']; ?>">
                            </div>
                            <div class="color-field">
                                <small>Borde</small>
                                <input type="color" class="color-input" data-color="warning-border" value="<?= $config['colors']['warning']['border']; ?>">
                            </div>
                            <div class="color-field">
                                <small>Texto</small>
                                <input type="color" class="color-input" data-color="warning-text" value="<?= $config['colors']['warning']['text']; ?>">
                            </div>
                            <div class="color-field">
                                <small>Icono</small>
                                <input type="color" class="color-input" data-color="warning-icon" value="<?= $config['colors']['warning']['icon']; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="alert-type-color">
                        <label><i class="bi bi-info-circle-fill"></i> Info</label>
                        <div class="color-fields">
                            <div class="color-field">
                                <small>Fondo</small>
                                <input type="color" class="color-input" data-color="info-bg" value="<?= $config['colors']['info']['bg']; ?>">
                            </div>
                            <div class="color-field">
                                <small>Borde</small>
                                <input type="color" class="color-input" data-color="info-border" value="<?= $config['colors']['info']['border']; ?>">
                            </div>
                            <div class="color-field">
                                <small>Texto</small>
                                <input type="color" class="color-input" data-color="info-text" value="<?= $config['colors']['info']['text']; ?>">
                            </div>
                            <div class="color-field">
                                <small>Icono</small>
                                <input type="color" class="color-input" data-color="info-icon" value="<?= $config['colors']['info']['icon']; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <!-- Duración -->
    <div class="panel-section is-hidden" data-panel="duracion">
            <div class="card mb-4" id="duracion">
                <div class="card-header">
                    <h5><i class="bi bi-hourglass-split"></i> Duración de alertas (ms)</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Tiempo en milisegundos antes de que se cierre automáticamente. Usa 0 para que no se cierre.</p>
                    <div class="form-group-row">
                        <div class="form-group">
                            <label for="durationSuccess" class="form-label">Éxito</label>
                            <input type="number" class="form-control" id="durationSuccess" min="0" max="10000" value="<?= $config['duration']['success']; ?>" step="500">
                        </div>
                        <div class="form-group">
                            <label for="durationDanger" class="form-label">Error</label>
                            <input type="number" class="form-control" id="durationDanger" min="0" max="10000" value="<?= $config['duration']['danger']; ?>" step="500">
                        </div>
                        <div class="form-group">
                            <label for="durationWarning" class="form-label">Aviso</label>
                            <input type="number" class="form-control" id="durationWarning" min="0" max="10000" value="<?= $config['duration']['warning']; ?>" step="500">
                        </div>
                        <div class="form-group">
                            <label for="durationInfo" class="form-label">Info</label>
                            <input type="number" class="form-control" id="durationInfo" min="0" max="10000" value="<?= $config['duration']['info']; ?>" step="500">
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <!-- Animación -->
    <div class="panel-section is-hidden" data-panel="animacion">
            <div class="card mb-4" id="animacion">
                <div class="card-header">
                    <h5><i class="bi bi-lightning-charge"></i> Entrada de alertas</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Define desde dónde aparecen las alertas. "Automático" usa la posición actual.</p>
                    <div class="form-group-row">
                        <div class="form-group">
                            <label for="enterFrom" class="form-label">Dirección de entrada</label>
                            <select id="enterFrom" class="form-select">
                                <option value="auto">Automático según posición</option>
                                <option value="top">Desde arriba</option>
                                <option value="bottom">Desde abajo</option>
                                <option value="left">Desde la izquierda</option>
                                <option value="right">Desde la derecha</option>
                                <option value="center">Desde el centro</option>
                            </select>
                            <small class="text-muted">Ejemplo: posición abajo + "Desde abajo"</small>
                        </div>
                        <div class="form-group">
                            <label for="enterSpeed" class="form-label">Velocidad de entrada</label>
                            <select id="enterSpeed" class="form-select">
                                <option value="slow">Lento</option>
                                <option value="normal">Normal</option>
                                <option value="fast">Rápido</option>
                            </select>
                            <small class="text-muted">Ajusta la duración de la animación</small>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <!-- Acciones -->
    <div class="panel-section is-hidden" data-panel="acciones">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-primary" onclick="saveConfig()">
                            <i class="bi bi-floppy"></i> Guardar configuración
                        </button>
                        <button class="btn btn-outline-primary" onclick="resetConfig()">
                            <i class="bi bi-arrow-counterclockwise"></i> Restaurar valores por defecto
                        </button>
                        <button class="btn btn-outline-primary" onclick="downloadConfig()">
                            <i class="bi bi-download"></i> Descargar configuración
                        </button>
                        <a href="index.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Ver ejemplos
                        </a>
                    </div>
                </div>
            </div>
    </div>

    <!-- Preview y ejemplos -->
    <div class="panel-section is-hidden" data-panel="ejemplos">
            <div class="card mb-4" id="ejemplos">
                <div class="card-header">
                    <h5><i class="bi bi-play-circle"></i> Previsualización en vivo</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3" role="alert">
                        <i class="bi bi-info-circle-fill"></i> 
                        Las alertas se mostrarán en la <strong>posición configurada</strong> en la pantalla. 
                        Haz clic en cualquier botón para verlas en acción.
                    </div>

                    <div class="demo-section">
                        <h6><i class="bi bi-check-circle-fill text-success"></i> Alertas de éxito</h6>
                        <div class="demo-buttons">
                            <button class="demo-btn demo-btn-success" onclick="previewAlert('success', 'Registro exitoso', '¡Tu cuenta ha sido creada correctamente!')">Registro</button>
                            <button class="demo-btn demo-btn-success" onclick="previewAlert('success', 'Guardado con éxito', 'Todos los cambios han sido guardados.')">Guardado</button>
                            <button class="demo-btn demo-btn-success" onclick="previewAlert('success', 'Enviado', 'Tu mensaje fue enviado correctamente.')">Enviado</button>
                        </div>
                    </div>

                    <div class="demo-section">
                        <h6><i class="bi bi-exclamation-circle-fill text-danger"></i> Alertas de error</h6>
                        <div class="demo-buttons">
                            <button class="demo-btn demo-btn-danger" onclick="previewAlert('danger', 'Credenciales inválidas', 'Verifica tu correo y contraseña.')">Error de auth</button>
                            <button class="demo-btn demo-btn-danger" onclick="previewAlert('danger', 'Error de conexión', 'No se pudo conectar al servidor. Intenta nuevamente.')">Conexión</button>
                            <button class="demo-btn demo-btn-danger" onclick="previewAlert('danger', 'Error', 'No se pudo completar la acción solicitada.')">Error genérico</button>
                        </div>
                    </div>

                    <div class="demo-section">
                        <h6><i class="bi bi-exclamation-triangle-fill text-warning"></i> Alertas de aviso</h6>
                        <div class="demo-buttons">
                            <button class="demo-btn demo-btn-warning" onclick="previewAlert('warning', 'Datos incompletos', 'Por favor, completa todos los campos obligatorios.')">Datos faltantes</button>
                            <button class="demo-btn demo-btn-warning" onclick="previewAlert('warning', 'Validación', 'Revisa los datos antes de guardar.')">Validación</button>
                            <button class="demo-btn demo-btn-warning" onclick="previewAlert('warning', 'Advertencia', 'Esta acción no se puede deshacer.')">Alerta</button>
                        </div>
                    </div>

                    <div class="demo-section">
                        <h6><i class="bi bi-info-circle-fill text-info"></i> Alertas informativas</h6>
                        <div class="demo-buttons">
                            <button class="demo-btn demo-btn-info" onclick="previewAlert('info', 'Información', 'Se ha cargado la nueva versión del sistema.')">Información</button>
                            <button class="demo-btn demo-btn-info" onclick="previewAlert('info', 'Noticia', 'Hay una nueva característica disponible.')">Novedad</button>
                            <button class="demo-btn demo-btn-info" onclick="previewAlert('info', 'Recordatorio', 'Recuerda completar tu perfil para mejor experiencia.')">Recordatorio</button>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</main>

<link rel="stylesheet" href="<?= htmlspecialchars($alertsCssPathVersioned, ENT_QUOTES, 'UTF-8'); ?>">

<style id="dynamicStyles">
<?= $dynamicCSS; ?>
</style>

<div id="alertsContainer" class="alerts-container"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= htmlspecialchars($alertsJsPathVersioned, ENT_QUOTES, 'UTF-8'); ?>"></script>
<script>
/**
 * inyectar configuración como objeto global para que pueda ser accedido desde config-panel.js
 */
window.alertsConfig = {
    position: '<?= addslashes($config['position']); ?>',
    maxWidth: <?= $config['maxWidth']; ?>,
    padding: <?= $config['padding']; ?>,
    fontSize: <?= $config['fontSize']; ?>,
    borderRadius: <?= $config['borderRadius']; ?>,
    animation: {
        enterFrom: '<?= addslashes($config['animation']['enterFrom']); ?>',
        speed: '<?= addslashes($config['animation']['speed']); ?>'
    },
    duration: {
        success: <?= $config['duration']['success']; ?>,
        danger: <?= $config['duration']['danger']; ?>,
        warning: <?= $config['duration']['warning']; ?>,
        info: <?= $config['duration']['info']; ?>
    },
    colors: {
        success: {
            bg: '<?= addslashes($config['colors']['success']['bg']); ?>',
            border: '<?= addslashes($config['colors']['success']['border']); ?>',
            text: '<?= addslashes($config['colors']['success']['text']); ?>',
            icon: '<?= addslashes($config['colors']['success']['icon']); ?>'
        },
        danger: {
            bg: '<?= addslashes($config['colors']['danger']['bg']); ?>',
            border: '<?= addslashes($config['colors']['danger']['border']); ?>',
            text: '<?= addslashes($config['colors']['danger']['text']); ?>',
            icon: '<?= addslashes($config['colors']['danger']['icon']); ?>'
        },
        warning: {
            bg: '<?= addslashes($config['colors']['warning']['bg']); ?>',
            border: '<?= addslashes($config['colors']['warning']['border']); ?>',
            text: '<?= addslashes($config['colors']['warning']['text']); ?>',
            icon: '<?= addslashes($config['colors']['warning']['icon']); ?>'
        },
        info: {
            bg: '<?= addslashes($config['colors']['info']['bg']); ?>',
            border: '<?= addslashes($config['colors']['info']['border']); ?>',
            text: '<?= addslashes($config['colors']['info']['text']); ?>',
            icon: '<?= addslashes($config['colors']['info']['icon']); ?>'
        }
    }
};
</script>
<script src="assets/js/config-panel.js"></script>
</body>
</html>