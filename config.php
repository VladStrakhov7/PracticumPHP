<?php
// Конфигурация подключения к базе данных MySQL

// Настройки подключения к MySQL
define('DB_HOST', 'localhost');
define('DB_NAME', 'restapi');
define('DB_USER', 'root');
define('DB_PASS', ''); // По умолчанию в XAMPP пароль пустой

// Инициализация базы данных при первом запуске
function initDatabase() {
    try {
        // Подключаемся без указания базы данных
        $db = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Создаем базу данных если не существует
        $db->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $db->exec("USE " . DB_NAME);
        
        // Проверяем, существуют ли таблицы
        $stmt = $db->query("SHOW TABLES LIKE 'countries'");
        if ($stmt->rowCount() == 0) {
            // Читаем и выполняем SQL скрипт
            $sql = file_get_contents(__DIR__ . '/database_mysql.sql');
            
            // Удаляем CREATE DATABASE и USE из скрипта, так как уже выполнили
            $sql = preg_replace('/CREATE DATABASE.*?;/is', '', $sql);
            $sql = preg_replace('/USE.*?;/is', '', $sql);
            
            // Разбиваем на отдельные запросы
            $statements = [];
            $current = '';
            $lines = explode("\n", $sql);
            
            foreach ($lines as $line) {
                $line = trim($line);
                // Пропускаем комментарии и пустые строки
                if (empty($line) || strpos($line, '--') === 0) {
                    continue;
                }
                $current .= $line . "\n";
                // Если строка заканчивается на ;, это конец запроса
                if (substr(rtrim($line), -1) === ';') {
                    $statements[] = trim($current);
                    $current = '';
                }
            }
            
            // Выполняем все запросы
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    try {
                        $db->exec($statement);
                    } catch (PDOException $e) {
                        // Игнорируем ошибки при повторной вставке данных
                        if (strpos($statement, 'INSERT') === false) {
                            throw $e;
                        }
                    }
                }
            }
        }
    } catch (PDOException $e) {
        die("Ошибка инициализации БД: " . $e->getMessage());
    }
}

// Получение подключения к базе данных
function getDB() {
    initDatabase();
    try {
        $db = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die("Ошибка подключения к БД: " . $e->getMessage());
    }
}

// Установка заголовков для JSON ответов
function setJsonHeaders() {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}

// Отправка JSON ответа
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    setJsonHeaders();
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Отправка ошибки
function sendError($message, $statusCode = 400) {
    sendJsonResponse(['error' => $message], $statusCode);
}

