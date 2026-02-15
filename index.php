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
    header('Location: ' . ($basePath !== '' ? $basePath : '') . '/index.php?demo=flash_done');
    exit;
}

$alerts = $alertRepository->collect($_GET, $_SESSION);

$alertsCssPath = ($basePath !== '' ? $basePath : '') . '/assets/alerts.css';
$alertsJsPath = ($basePath !== '' ? $basePath : '') . '/assets/alerts.js';
?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tutorial de Alertas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
<main class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <span class="badge text-bg-dark mb-2">Demo de integración</span>
                            <h1 class="h3 mb-2">Sistema de alertas reutilizable</h1>
                            <p class="text-secondary mb-0">Pruebas de alertas por repositorio, sesión y JavaScript en una sola pantalla.</p>
                        </div>
                        <div class="text-md-end">
                            <div class="small text-secondary">Ruta activa</div>
                            <code><?= htmlspecialchars(($basePath !== '' ? $basePath : '') . '/index.php', ENT_QUOTES, 'UTF-8'); ?></code>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
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
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
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
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
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
</script>
</body>
</html>
