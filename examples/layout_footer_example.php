<?php
session_start();
require_once __DIR__ . '/../php/AlertRepository.php';

$alertRepository = new AlertRepository();
$alerts = $alertRepository->collect($_GET, $_SESSION);
$scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = rtrim(dirname($scriptName), '/');
$basePath = $basePath === '.' ? '' : $basePath;
$alertsCssPath = ($basePath !== '' ? $basePath : '') . '/assets/alerts.css';
$alertsJsPath = ($basePath !== '' ? $basePath : '') . '/assets/alerts.js';

include __DIR__ . '/../templates/alerts.php';
