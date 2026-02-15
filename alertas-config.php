<?php
/**
 * archivo de configuración de alertas
 * este archivo contiene solo la configuración editable de las alertas
 * modifica los valores según tus necesidades
 * 
 * generado automáticamente por el panel de configuración
 * url: config-panel.php
 * fecha: 2026-02-15 20:13:48
 */

return [
  // posición de las alertas en la pantalla
  // opciones: top-left, top-center, top-right, center-left, center-center, center-right, bottom-left, bottom-center, bottom-right
  'position' => 'center-center',
  
  // dimensiones y espaciado
  'maxWidth' => 400,      // ancho máximo en píxeles
  'padding' => 16,        // padding interno en píxeles
  'fontSize' => 14,       // tamaño de fuente en píxeles
  'borderRadius' => 8,    // radio del borde redondeado en píxeles
  
  // animación de entrada
  // opciones: auto, top, bottom, left, right, center
  'animation' => [
    'enterFrom' => 'center',
    'speed' => 'normal'
  ],
  
  // duración de las alertas en milisegundos (0 = no se cierra automáticamente)
  'duration' => [
    'success' => 3000,
    'danger' => 5000,
    'warning' => 4000,
    'info' => 4000
  ],
  
  // colores por tipo de alerta
  'colors' => [
    'success' => [
      'bg' => '#d1e7dd',
      'border' => '#badbcc',
      'text' => '#0f5132',
      'icon' => '#198754'
    ],
    'danger' => [
      'bg' => '#f8d7da',
      'border' => '#f5c2c7',
      'text' => '#842029',
      'icon' => '#dc3545'
    ],
    'warning' => [
      'bg' => '#fff3cd',
      'border' => '#ffecb5',
      'text' => '#664d03',
      'icon' => '#ffc107'
    ],
    'info' => [
      'bg' => '#cff4fc',
      'border' => '#b6effb',
      'text' => '#055160',
      'icon' => '#0dcaf0'
    ]
  ]
];
