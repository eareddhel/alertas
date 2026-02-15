<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/php/AlertRepository.php';

$alertRepository = new AlertRepository();

$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/alertas/index.php'));
$basePath = rtrim(dirname($scriptName), '/');
$basePath = $basePath === '.' ? '' : $basePath;

if (isset($_GET['demo']) && $_GET['demo'] === 'flash') {
    $type = (string) ($_GET['type'] ?? 'info');

    $flashMap = [
        'success' => [
            'type' => 'success',
            'title' => 'Flash success',
            'message' => 'Esta alerta fue guardada en sesión y mostrada tras redirección.'
        ],
        'danger' => [
            'type' => 'danger',
            'title' => 'Flash danger',
            'message' => 'Se simuló un error persistido en sesión (flash message).'
        ],
        'warning' => [
            'type' => 'warning',
            'title' => 'Flash warning',
            'message' => 'Recuerda revisar los datos antes de guardar.'
        ],
        'info' => [
            'type' => 'info',
            'title' => 'Flash info',
            'message' => 'Este mensaje informativo viene desde la sesión.'
        ],
    ];

    $alertRepository->push($_SESSION, $flashMap[$type] ?? $flashMap['info']);
    header('Location: index.php?demo=flash_done');
    exit;
}

$alerts = $alertRepository->collect($_GET, $_SESSION);

$alertsCssPath = 'assets/alerts.css';
$alertsJsPath = 'assets/alerts.js';
?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tutorial de Alertas</title>
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

        .hero-card {
            background: linear-gradient(135deg, #8b7ba8 0%, #a5c9d4 100%);
            color: white;
        }

        .hero-card h1 {
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .hero-card .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .hero-card .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-primary:hover {
            background-color: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.4);
        }
    </style>
</head>
<body>
<main class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card border-0 shadow-sm mb-4 hero-card">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex flex-column justify-content-between gap-4">
                        <div>
                            <span class="badge bg-white text-dark mb-3">Demo e integración</span>
                            <h1 class="mb-3">Sistema de alertas reutilizable</h1>
                            <p class="mb-0 opacity-90">Prueba las alertas por query string, sesión y JavaScript. Personaliza el diseño desde el panel de configuración.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-2 pt-3 border-top border-white border-opacity-20">
                            <a href="config-panel.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-sliders"></i> Panel de configuración
                            </a>
                            <a href="#ejemplos" class="btn btn-outline-light">
                                <i class="bi bi-arrow-down"></i> Ver ejemplos
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" id="ejemplos">
                <div class="card-header">
                    <h2 class="h5 mb-1 d-flex align-items-center gap-2"><i class="bi bi-link-45deg"></i> 1) Alertas por query string</h2>
                    <p class="text-secondary mb-0">Parámetros procesados por <code>AlertRepository::collect()</code>.</p>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-success btn-sm" href="?success=registered">success=registered</a>
                        <a class="btn btn-outline-success btn-sm" href="?success=password_updated">success=password_updated</a>
                        <a class="btn btn-danger btn-sm" href="?error=auth">error=auth</a>
                        <a class="btn btn-outline-danger btn-sm" href="?error=ticket_reply_error">error=ticket_reply_error</a>
                        <a class="btn btn-warning btn-sm" href="?msg=error">msg=error</a>
                        <a class="btn btn-info btn-sm" href="?status=sent&amp;email=demo@correo.com">status=sent</a>
                        <a class="btn btn-secondary btn-sm" href="?message=Mensaje%20libre&amp;type=info">message=...&amp;type=info</a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <h2 class="h5 mb-1 d-flex align-items-center gap-2"><i class="bi bi-arrow-repeat"></i> 2) Alertas flash por sesión</h2>
                    <p class="text-secondary mb-0">Guarda en sesión, redirige y muestra una sola vez.</p>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-success btn-sm" href="?demo=flash&amp;type=success">Flash success</a>
                        <a class="btn btn-danger btn-sm" href="?demo=flash&amp;type=danger">Flash danger</a>
                        <a class="btn btn-warning btn-sm" href="?demo=flash&amp;type=warning">Flash warning</a>
                        <a class="btn btn-info btn-sm" href="?demo=flash&amp;type=info">Flash info</a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h2 class="h5 mb-1 d-flex align-items-center gap-2"><i class="bi bi-code-slash"></i> 3) Alertas desde JavaScript</h2>
                    <p class="text-secondary mb-0">Llamadas directas a <code>AlertSystem.notify()</code>.</p>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-success btn-sm" data-demo-js="success">JS success</button>
                        <button type="button" class="btn btn-danger btn-sm" data-demo-js="danger">JS danger</button>
                        <button type="button" class="btn btn-warning btn-sm" data-demo-js="warning">JS warning</button>
                        <button type="button" class="btn btn-info btn-sm" data-demo-js="info">JS info</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/templates/alerts.php'; ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // esperar a que AlertSystem esté disponible
    const checkAlertSystem = setInterval(() => {
      if (window.AlertSystem) {
        clearInterval(checkAlertSystem);
        
        document.querySelectorAll('[data-demo-js]').forEach((button) => {
          button.addEventListener('click', () => {
            const type = button.getAttribute('data-demo-js') || 'info';
            const labels = {
              success: 'Operación completada correctamente.',
              danger: 'Ocurrió un error inesperado.',
              warning: 'Atención: revisa los datos ingresados.',
              info: 'Información general para el usuario.'
            };

            window.AlertSystem.notify(labels[type] || labels.info, type, `Demo ${type}`);
          });
        });
      }
    }, 100);
    
    // timeout de seguridad
    setTimeout(() => clearInterval(checkAlertSystem), 5000);
  });
</script>
</body>
</html>
