/**
 * panel de configuración de alertas
 * gestiona todos los eventos y lógica del panel de configuración
 * la configuración inicial se inyecta desde config-panel.php como window.alertsConfig
 */

console.log('Iniciando panel de configuración...');

// variable global para almacenar configuración (inyectada desde PHP)
let currentConfig = window.alertsConfig || {};
if (!currentConfig.animation) {
    currentConfig.animation = { enterFrom: 'auto', speed: 'normal' };
}

console.log('Configuración inicial:', currentConfig);

/**
 * actualiza los estilos dinámicos de las alertas según la configuración
 */
function updateDynamicStyles() {
    const positionMap = {
        'top-left': 'top: 70px; left: 20px; right: auto;',
        'top-center': 'top: 70px; left: 50%; transform: translateX(-50%);',
        'top-right': 'top: 70px; right: 20px; left: auto;',
        'center-left': 'top: 50%; left: 20px; right: auto; transform: translateY(-50%);',
        'center-center': 'top: 50%; left: 50%; transform: translate(-50%, -50%);',
        'center-right': 'top: 50%; right: 20px; left: auto; transform: translateY(-50%);',
        'bottom-left': 'bottom: 20px; left: 20px; right: auto; top: auto;',
        'bottom-center': 'bottom: 20px; left: 50%; right: auto; top: auto; transform: translateX(-50%);',
        'bottom-right': 'bottom: 20px; right: 20px; left: auto; top: auto;'
    };

    const positionCSS = positionMap[currentConfig.position] || positionMap['top-right'];
    const enterFrom = (currentConfig.animation && currentConfig.animation.enterFrom) ? currentConfig.animation.enterFrom : 'auto';
    const autoDirectionMap = {
        'top-left': 'top',
        'top-center': 'top',
        'top-right': 'top',
        'center-left': 'left',
        'center-center': 'top',
        'center-right': 'right',
        'bottom-left': 'bottom',
        'bottom-center': 'bottom',
        'bottom-right': 'bottom'
    };
    const effectiveEnterFrom = enterFrom === 'auto'
        ? (autoDirectionMap[currentConfig.position] || 'right')
        : enterFrom;
    const animationNameMap = {
        top: 'slideInFromTop',
        bottom: 'slideInFromBottom',
        left: 'slideInFromLeft',
        right: 'slideInFromRight',
        center: 'popInCenter'
    };
    const animationName = animationNameMap[effectiveEnterFrom] || 'slideInFromRight';
    const speedMap = {
        slow: 450,
        normal: 300,
        fast: 200
    };
    const animationDuration = speedMap[currentConfig.animation.speed] || speedMap.normal;
    
    const css = `
        /* estilos globales base para garantizar visibilidad sin bootstrap */
        * {
            box-sizing: border-box;
        }
        
        /* asegurar que el contenedor sea visible */
        #alertsContainer, .alerts-container {
            position: fixed !important;
            ${positionCSS}
            max-width: ${currentConfig.maxWidth}px;
            z-index: 9999;
            pointer-events: none;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .alert-toast {
            padding: ${currentConfig.padding}px;
            border-radius: ${currentConfig.borderRadius}px;
            display: flex !important;
            align-items: flex-start;
            margin-bottom: 0;
            border: 1px solid;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: ${animationName} ${animationDuration}ms ease-out;
            opacity: 0;
            transition: opacity 0.3s ease, transform 0.3s ease;
            pointer-events: auto;
            min-width: 300px;
            width: 100%;
            background-color: white;
        }

        .alert-toast.show {
            opacity: 1;
        }

        .alert-icon {
            flex-shrink: 0;
            font-size: 20px;
            margin-right: 12px;
            margin-top: 2px;
        }

        .alert-content {
            display: flex;
            align-items: flex-start;
            width: 100%;
            gap: 12px;
            flex-wrap: nowrap;
            overflow: visible;
        }

        .alert-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
            overflow: visible;
        }

        .alert-title {
            font-weight: 600;
            display: block;
            line-height: 1.4;
            visibility: visible;
            opacity: 1;
            margin: 0;
            font-size: ${currentConfig.fontSize}px;
            word-break: break-word;
        }

        .alert-message {
            font-size: ${currentConfig.fontSize}px;
            display: block;
            word-break: break-word;
            line-height: 1.4;
            opacity: 1;
            visibility: visible;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 14px;
        }

        .alert-close {
            flex-shrink: 0;
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            padding: 0;
            margin-left: 8px;
            font-size: 20px;
            opacity: 0.6;
            transition: opacity 0.2s;
        }

        .alert-close:hover {
            opacity: 1;
        }

        .alert-toast.alert-success {
            background-color: ${currentConfig.colors.success.bg};
            border-color: ${currentConfig.colors.success.border};
            color: ${currentConfig.colors.success.text};
        }

        .alert-toast.alert-success .alert-icon {
            color: ${currentConfig.colors.success.icon};
        }

        .alert-toast.alert-success .alert-message,
        .alert-toast.alert-success .alert-title {
            color: ${currentConfig.colors.success.text} !important;
            font-size: inherit !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            text-indent: 0 !important;
            clip: auto !important;
        }

        .alert-toast.alert-danger {
            background-color: ${currentConfig.colors.danger.bg};
            border-color: ${currentConfig.colors.danger.border};
            color: ${currentConfig.colors.danger.text};
        }

        .alert-toast.alert-danger .alert-icon {
            color: ${currentConfig.colors.danger.icon};
        }

        .alert-toast.alert-danger .alert-message,
        .alert-toast.alert-danger .alert-title {
            color: ${currentConfig.colors.danger.text} !important;
            font-size: inherit !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            text-indent: 0 !important;
            clip: auto !important;
        }

        .alert-toast.alert-warning {
            background-color: ${currentConfig.colors.warning.bg};
            border-color: ${currentConfig.colors.warning.border};
            color: ${currentConfig.colors.warning.text};
        }

        .alert-toast.alert-warning .alert-icon {
            color: ${currentConfig.colors.warning.icon};
        }

        .alert-toast.alert-warning .alert-message,
        .alert-toast.alert-warning .alert-title {
            color: ${currentConfig.colors.warning.text} !important;
            font-size: inherit !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            text-indent: 0 !important;
            clip: auto !important;
        }

        .alert-toast.alert-info {
            background-color: ${currentConfig.colors.info.bg};
            border-color: ${currentConfig.colors.info.border};
            color: ${currentConfig.colors.info.text};
        }

        .alert-toast.alert-info .alert-icon {
            color: ${currentConfig.colors.info.icon};
        }

        .alert-toast.alert-info .alert-message,
        .alert-toast.alert-info .alert-title {
            color: ${currentConfig.colors.info.text} !important;
            font-size: inherit !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            text-indent: 0 !important;
            clip: auto !important;
        }

        @keyframes slideInFromRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInFromLeft {
            from {
                opacity: 0;
                transform: translateX(-100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInFromTop {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInFromBottom {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes popInCenter {
            0% {
                opacity: 0;
                transform: scale(0.96);
            }
            70% {
                opacity: 1;
                transform: scale(1.03);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
    `;
    
    document.getElementById('dynamicStyles').textContent = css;
    console.log('CSS dinámico actualizado:', css);
}

/**
 * guardado automático (placeholder para futuras mejoras)
 */
function autoSave() {
    // auto-guardado en el navegador
}

/**
 * guarda la configuración actual en el servidor (alertas-config.php)
 */
function saveConfig() {
    fetch('config-panel.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'updateConfig',
            config: currentConfig
        })
    })
    .then(response => response.json())
    .then(data => {
        if (window.AlertSystem && window.AlertSystem.show) {
            window.AlertSystem.show(data.message || 'Configuración guardada en alertas-config.php', 'success', currentConfig.duration.success);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (window.AlertSystem && window.AlertSystem.show) {
            window.AlertSystem.show('Error al guardar la configuración', 'danger', currentConfig.duration.danger);
        }
    });
}

/**
 * restaura la configuración a valores por defecto
 */
function resetConfig() {
    const sendResetRequest = () => {
        fetch('config-panel.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'resetConfig'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.config) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (window.AlertSystem && window.AlertSystem.show) {
                window.AlertSystem.show('Error al restaurar la configuración', 'danger', currentConfig.duration.danger);
            }
        });
    };

    if (window.AlertSystem && window.AlertSystem.show) {
        const alertElement = window.AlertSystem.show({
            type: 'warning',
            title: 'Restaurar valores por defecto',
            message: '¿Restaurar valores por defecto? Esta acción recargará la página.' +
                '<div class="alert-actions">' +
                '<button type="button" class="alert-action-btn primary" data-action="confirm-reset">Confirmar</button>' +
                '<button type="button" class="alert-action-btn" data-action="cancel-reset">Cancelar</button>' +
                '</div>',
            duration: 0,
            dismissible: false
        });

        if (!alertElement) {
            return;
        }

        const confirmBtn = alertElement.querySelector('[data-action="confirm-reset"]');
        const cancelBtn = alertElement.querySelector('[data-action="cancel-reset"]');

        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                alertElement.remove();
                sendResetRequest();
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                alertElement.remove();
            });
        }

        return;
    }

    if (!confirm('¿Estás seguro de que deseas restaurar la configuración por defecto?')) {
        return;
    }

    sendResetRequest();
}

/**
 * descarga el archivo de configuración (alertas-config.php)
 */
function downloadConfig() {
    // generar contenido PHP del archivo de configuración
    const phpContent = "<?php\n" +
"/**\n" +
" * archivo de configuración de alertas\n" +
" * este archivo contiene solo la configuración editable de las alertas\n" +
" * modifica los valores según tus necesidades\n" +
" * \n" +
" * generado automáticamente por el panel de configuración\n" +
" * url: config-panel.php\n" +
" * fecha: " + new Date().toLocaleString('es-CL') + "\n" +
" */\n" +
"\n" +
"return [\n" +
"  // posición de las alertas en la pantalla\n" +
"  // opciones: top-left, top-center, top-right, center-left, center-center, center-right, bottom-left, bottom-center, bottom-right\n" +
"  'position' => '" + currentConfig.position + "',\n" +
"  \n" +
"  // dimensiones y espaciado\n" +
"  'maxWidth' => " + currentConfig.maxWidth + ",      // ancho máximo en píxeles\n" +
"  'padding' => " + currentConfig.padding + ",        // padding interno en píxeles\n" +
"  'fontSize' => " + currentConfig.fontSize + ",       // tamaño de fuente en píxeles\n" +
"  'borderRadius' => " + currentConfig.borderRadius + ",    // radio del borde redondeado en píxeles\n" +
"  \n" +
"  // animación de entrada\n" +
"  // opciones: auto, top, bottom, left, right, center\n" +
"  'animation' => [\n" +
"    'enterFrom' => '" + (currentConfig.animation ? currentConfig.animation.enterFrom : 'auto') + "',\n" +
"    'speed' => '" + (currentConfig.animation ? currentConfig.animation.speed : 'normal') + "'\n" +
"  ],\n" +
"  \n" +
"  // duración de las alertas en milisegundos (0 = no se cierra automáticamente)\n" +
"  'duration' => [\n" +
"    'success' => " + currentConfig.duration.success + ",\n" +
"    'danger' => " + currentConfig.duration.danger + ",\n" +
"    'warning' => " + currentConfig.duration.warning + ",\n" +
"    'info' => " + currentConfig.duration.info + "\n" +
"  ],\n" +
"  \n" +
"  // colores por tipo de alerta\n" +
"  'colors' => [\n" +
"    'success' => [\n" +
"      'bg' => '" + currentConfig.colors.success.bg + "',\n" +
"      'border' => '" + currentConfig.colors.success.border + "',\n" +
"      'text' => '" + currentConfig.colors.success.text + "',\n" +
"      'icon' => '" + currentConfig.colors.success.icon + "'\n" +
"    ],\n" +
"    'danger' => [\n" +
"      'bg' => '" + currentConfig.colors.danger.bg + "',\n" +
"      'border' => '" + currentConfig.colors.danger.border + "',\n" +
"      'text' => '" + currentConfig.colors.danger.text + "',\n" +
"      'icon' => '" + currentConfig.colors.danger.icon + "'\n" +
"    ],\n" +
"    'warning' => [\n" +
"      'bg' => '" + currentConfig.colors.warning.bg + "',\n" +
"      'border' => '" + currentConfig.colors.warning.border + "',\n" +
"      'text' => '" + currentConfig.colors.warning.text + "',\n" +
"      'icon' => '" + currentConfig.colors.warning.icon + "'\n" +
"    ],\n" +
"    'info' => [\n" +
"      'bg' => '" + currentConfig.colors.info.bg + "',\n" +
"      'border' => '" + currentConfig.colors.info.border + "',\n" +
"      'text' => '" + currentConfig.colors.info.text + "',\n" +
"      'icon' => '" + currentConfig.colors.info.icon + "'\n" +
"    ]\n" +
"  ]\n" +
"];\n";
    
    const dataUri = 'data:text/plain;charset=utf-8,' + encodeURIComponent(phpContent);
    const exportFileDefaultName = 'alertas-config.php';
    
    const linkElement = document.createElement('a');
    linkElement.setAttribute('href', dataUri);
    linkElement.setAttribute('download', exportFileDefaultName);
    linkElement.click();
    
    if (window.AlertSystem && window.AlertSystem.show) {
        window.AlertSystem.show('Archivo alertas-config.php descargado. Reemplázalo en tu proyecto.', 'success', currentConfig.duration.success);
    }
}

/**
 * muestra una alerta de previsualización en vivo
 * @param {string} type - tipo de alerta (success, danger, warning, info)
 * @param {string} title - título de la alerta
 * @param {string} message - mensaje de la alerta
 */
function previewAlert(type, title, message) {
    // usar el sistema real de alertas para mostrar la previsualización
    if (window.AlertSystem && window.AlertSystem.show) {
        // obtener nombre amigable de la posición
        const positionNames = {
            'top-left': 'Arriba a la Izquierda',
            'top-center': 'Arriba al Centro',
            'top-right': 'Arriba a la Derecha',
            'center-left': 'Centro a la Izquierda',
            'center-center': 'Centro',
            'center-right': 'Centro a la Derecha',
            'bottom-left': 'Abajo a la Izquierda',
            'bottom-center': 'Abajo al Centro',
            'bottom-right': 'Abajo a la Derecha'
        };
        
        const positionName = positionNames[currentConfig.position] || 'Arriba a la Derecha';
        const finalMessage = message + ' [Aparecerá en: ' + positionName + ']';
        
        window.AlertSystem.show({
            type: type,
            title: title,
            message: finalMessage,
            duration: currentConfig.duration[type]
        });
    }
}

/**
 * hace scroll a una sección y actualiza el estado activo del sidebar
 * @param {string} sectionId - id de la sección destino
 */
function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
        // actualizar sidebar activo
        document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));
        const activeLink = document.querySelector('.sidebar-menu a[href="#' + sectionId + '"]');
        if (activeLink) {
            activeLink.classList.add('active');
        }
    }
}

/**
 * inicialización cuando el DOM está listo
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando panel de configuración...');
    
    // actualizar estilos dinámicos iniciales
    updateDynamicStyles();
    
    // tabs del panel
    const panelTabs = Array.from(document.querySelectorAll('.panel-tab'));
    const panelSections = Array.from(document.querySelectorAll('.panel-section'));

    const setActiveTab = (tabId) => {
        panelTabs.forEach(tab => {
            tab.classList.toggle('active', tab.dataset.tab === tabId);
        });

        panelSections.forEach(section => {
            section.classList.toggle('is-hidden', section.dataset.panel !== tabId);
        });
    };

    if (panelTabs.length > 0) {
        setActiveTab(panelTabs[0].dataset.tab);
    }

    panelTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            setActiveTab(tab.dataset.tab);
        });
    });

    // clickeadores de botones de posición
    document.querySelectorAll('.position-btn').forEach(btn => {
        if (btn.dataset.position === currentConfig.position) {
            btn.classList.add('active');
        }
        btn.addEventListener('click', function() {
            document.querySelectorAll('.position-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentConfig.position = this.dataset.position;
            updateDynamicStyles();
            autoSave();
            
            // mostrar alerta de demostración en la nueva posición
            if (window.AlertSystem && window.AlertSystem.show) {
                window.AlertSystem.show('Posición actualizada: ' + this.title, 'info', currentConfig.duration.info);
            }
        });
    });

    // event listeners para inputs de tamaños
    document.getElementById('maxWidth').addEventListener('input', function() {
        currentConfig.maxWidth = parseInt(this.value);
        updateDynamicStyles();
        autoSave();
    });

    document.getElementById('padding').addEventListener('input', function() {
        currentConfig.padding = parseInt(this.value);
        updateDynamicStyles();
        autoSave();
    });

    document.getElementById('fontSize').addEventListener('input', function() {
        currentConfig.fontSize = parseInt(this.value);
        updateDynamicStyles();
        autoSave();
    });

    document.getElementById('borderRadius').addEventListener('input', function() {
        currentConfig.borderRadius = parseInt(this.value);
        updateDynamicStyles();
        autoSave();
    });

    // event listeners para inputs de duración
    document.getElementById('durationSuccess').addEventListener('input', function() {
        currentConfig.duration.success = parseInt(this.value);
        autoSave();
    });

    document.getElementById('durationDanger').addEventListener('input', function() {
        currentConfig.duration.danger = parseInt(this.value);
        autoSave();
    });

    document.getElementById('durationWarning').addEventListener('input', function() {
        currentConfig.duration.warning = parseInt(this.value);
        autoSave();
    });

    document.getElementById('durationInfo').addEventListener('input', function() {
        currentConfig.duration.info = parseInt(this.value);
        autoSave();
    });

    const enterFromSelect = document.getElementById('enterFrom');
    if (enterFromSelect) {
        enterFromSelect.value = currentConfig.animation.enterFrom || 'auto';
        enterFromSelect.addEventListener('change', function() {
            currentConfig.animation.enterFrom = this.value;
            updateDynamicStyles();
            autoSave();
        });
    }

    const enterSpeedSelect = document.getElementById('enterSpeed');
    if (enterSpeedSelect) {
        enterSpeedSelect.value = currentConfig.animation.speed || 'normal';
        enterSpeedSelect.addEventListener('change', function() {
            currentConfig.animation.speed = this.value;
            updateDynamicStyles();
            autoSave();
        });
    }

    // event listeners para inputs de color
    document.querySelectorAll('.color-input').forEach(input => {
        input.addEventListener('input', function() {
            const [type, field] = this.dataset.color.split('-');
            currentConfig.colors[type][field] = this.value;
            updateDynamicStyles();
            autoSave();
        });
    });

    console.log('Todos los event listeners configurados correctamente');
});

// mostrar alerta de bienvenida al cargar la ventana
window.addEventListener('load', function() {
    console.log('Panel de configuración completamente cargado');
    console.log('AlertSystem disponible:', !!window.AlertSystem);
    if (window.AlertSystem && window.AlertSystem.show) {
        setTimeout(() => {
            window.AlertSystem.show('Panel de configuración listo. Haz cambios y verás las alertas en la posición seleccionada.', 'success', currentConfig.duration.success);
        }, 500);
    }
});
