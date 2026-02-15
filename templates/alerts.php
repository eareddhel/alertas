<?php

declare(strict_types=1);

$alerts = $alerts ?? [];
$alertsCssPath = $alertsCssPath ?? '/alertas/assets/alerts.css';
$alertsJsPath = $alertsJsPath ?? '/alertas/assets/alerts.js';
$jsonAlerts = (string) json_encode(
	$alerts,
	JSON_UNESCAPED_UNICODE
	| JSON_HEX_TAG
	| JSON_HEX_AMP
	| JSON_HEX_APOS
	| JSON_HEX_QUOT
);
?>
<link rel="stylesheet" href="<?= htmlspecialchars($alertsCssPath, ENT_QUOTES, 'UTF-8'); ?>">
<div id="alertsContainer" class="alerts-container"></div>
<script id="alertRepositoryData" type="application/json"><?= $jsonAlerts; ?></script>
<script>
	window.__alertsAssetFallback = function __alertsAssetFallback() {
		if (window.AlertSystem) {
			return;
		}

		const pathname = window.location.pathname || '';
		const basePath = pathname.replace(/\/[^/]*$/, '');

		if (!basePath) {
			return;
		}

		const fallbackCssPath = `${basePath}/assets/alerts.css`;
		const fallbackJsPath = `${basePath}/assets/alerts.js`;

		const hasCss = Array.from(document.querySelectorAll('link[rel="stylesheet"]')).some((link) => {
			try {
				return new URL(link.href, window.location.origin).pathname.endsWith('/assets/alerts.css');
			} catch (error) {
				return false;
			}
		});

		if (!hasCss) {
			const css = document.createElement('link');
			css.rel = 'stylesheet';
			css.href = fallbackCssPath;
			document.head.appendChild(css);
		}

		const hasFallbackScript = Array.from(document.querySelectorAll('script[src]')).some((script) => {
			try {
				const srcPath = new URL(script.src, window.location.origin).pathname;
				return srcPath === fallbackJsPath;
			} catch (error) {
				return false;
			}
		});

		if (!hasFallbackScript) {
			const js = document.createElement('script');
			js.src = fallbackJsPath;
			document.head.appendChild(js);
		}
	};
</script>
<script src="<?= htmlspecialchars($alertsJsPath, ENT_QUOTES, 'UTF-8'); ?>" onerror="window.__alertsAssetFallback && window.__alertsAssetFallback()"></script>
<script>
	if (!window.AlertSystem && window.__alertsAssetFallback) {
		window.__alertsAssetFallback();
	}
</script>
