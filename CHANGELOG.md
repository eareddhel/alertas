# Changelog

Este archivo documenta los cambios relevantes del proyecto.

## [1.0.0] - 2026-02-14

### Added
- Sistema de alertas reutilizable en PHP con `AlertRepository`.
- Render de contenedor y payload JSON desde plantilla dedicada.
- API global frontend `AlertSystem.notify()`.
- Estilos y comportamiento de toasts para tipos `success`, `danger`, `warning`, `info`.
- Soporte de alertas por query string y alertas flash por sesión.
- Fallback automático de assets por ruta actual y por error de carga de script.
- Demo profesional en `index.php` con pruebas para backend/session/frontend.
- Documentación extensa de integración, troubleshooting y uso sin Bootstrap.

### Changed
- Resolución de rutas de assets y redirecciones hecha dinámica para facilitar portabilidad.
- Serialización JSON ajustada para evitar fallos de parseo en alertas provenientes del backend.
