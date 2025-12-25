<?php
// Тестовая страница для диагностики
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Тест PHP</h1>";
echo "<p>PHP работает! Версия: " . phpversion() . "</p>";

// Проверка подключения к БД
echo "<h2>Проверка подключения к БД:</h2>";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=video_hosting;charset=utf8mb4", "root", "");
    echo "<p style='color: green;'>✓ Подключение к БД успешно!</p>";
    
    // Проверка таблиц
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Таблицы в БД: " . implode(", ", $tables) . "</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Ошибка подключения к БД: " . $e->getMessage() . "</p>";
}

// Проверка директорий
echo "<h2>Проверка директорий:</h2>";
$upload_dir = __DIR__ . '/uploads/videos/';
if (file_exists($upload_dir)) {
    echo "<p style='color: green;'>✓ Папка uploads/videos/ существует</p>";
} else {
    echo "<p style='color: orange;'>⚠ Папка uploads/videos/ не существует, попытка создать...</p>";
    if (mkdir($upload_dir, 0777, true)) {
        echo "<p style='color: green;'>✓ Папка создана</p>";
    } else {
        echo "<p style='color: red;'>✗ Не удалось создать папку</p>";
    }
}

// Проверка файлов
echo "<h2>Проверка основных файлов:</h2>";
$files = ['config.php', 'functions.php', 'index.php'];
foreach ($files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "<p style='color: green;'>✓ $file существует</p>";
    } else {
        echo "<p style='color: red;'>✗ $file не найден</p>";
    }
}

echo "<hr>";
echo "<p><a href='index.php'>Перейти на главную страницу</a></p>";
?>

