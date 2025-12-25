<?php
// Включение отображения ошибок для отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Конфигурация базы данных
define('DB_HOST', 'localhost');
define('DB_NAME', 'video_hosting');
define('DB_USER', 'root');
define('DB_PASS', '');

// Настройки сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Подключение к базе данных
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }
    return $pdo;
}

// Путь для загрузки видео
define('UPLOAD_DIR', __DIR__ . '/uploads/videos/');
// Определяем базовый URL проекта
$script_name = dirname($_SERVER['SCRIPT_NAME']);
$script_name = str_replace('\\', '/', $script_name);
if ($script_name === '/' || $script_name === '\\') {
    $script_name = '';
}
define('UPLOAD_URL', $script_name . '/uploads/videos/');

// Создание директории для загрузок, если её нет
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

