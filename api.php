<?php
require_once 'config.php';

// Получение пути запроса
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Удаляем query string
$path = parse_url($requestUri, PHP_URL_PATH);

// Удаляем базовый путь проекта и имя файла api.php
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = str_replace('\\', '/', $scriptName);
if ($basePath !== '/' && $basePath !== '') {
    $path = str_replace($basePath, '', $path);
}
$path = str_replace('/api.php', '', $path);
$path = trim($path, '/');

// Разбиваем путь на части
$pathParts = explode('/', $path);

// Получение данных из запроса
$input = json_decode(file_get_contents('php://input'), true);
if ($input === null && $requestMethod !== 'GET' && $requestMethod !== 'DELETE') {
    $input = $_POST;
}

$db = getDB();
setJsonHeaders();

// Роутинг
$resource = $pathParts[0] ?? '';
$id = $pathParts[1] ?? null;

// Обработка OPTIONS запросов (CORS)
if ($requestMethod === 'OPTIONS') {
    sendJsonResponse(['message' => 'OK']);
}

// Роутинг по ресурсам
switch ($resource) {
    case 'countries':
        handleCountries($db, $requestMethod, $id, $input);
        break;
    case 'clients':
        handleClients($db, $requestMethod, $id, $input);
        break;
    case 'tours':
        handleTours($db, $requestMethod, $id, $input);
        break;
    default:
        sendError('Ресурс не найден. Используйте: /countries, /clients, /tours', 404);
}

// Обработка запросов для стран
function handleCountries($db, $method, $id, $input) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $db->prepare('SELECT * FROM countries WHERE id = ?');
                $stmt->execute([$id]);
                $country = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$country) {
                    sendError('Страна не найдена', 404);
                }
                sendJsonResponse($country);
            } else {
                $stmt = $db->query('SELECT * FROM countries ORDER BY id');
                $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
                sendJsonResponse($countries);
            }
            break;
            
        case 'POST':
            if (!isset($input['name']) || empty($input['name'])) {
                sendError('Имя страны обязательно');
            }
            $stmt = $db->prepare('INSERT INTO countries (name, capital, description) VALUES (?, ?, ?)');
            $stmt->execute([
                $input['name'] ?? null,
                $input['capital'] ?? null,
                $input['description'] ?? null
            ]);
            $id = $db->lastInsertId();
            $stmt = $db->prepare('SELECT * FROM countries WHERE id = ?');
            $stmt->execute([$id]);
            sendJsonResponse($stmt->fetch(PDO::FETCH_ASSOC), 201);
            break;
            
        case 'PUT':
            if (!$id) {
                sendError('ID обязателен для обновления');
            }
            $stmt = $db->prepare('SELECT * FROM countries WHERE id = ?');
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                sendError('Страна не найдена', 404);
            }
            $stmt = $db->prepare('UPDATE countries SET name = ?, capital = ?, description = ? WHERE id = ?');
            $stmt->execute([
                $input['name'] ?? null,
                $input['capital'] ?? null,
                $input['description'] ?? null,
                $id
            ]);
            $stmt = $db->prepare('SELECT * FROM countries WHERE id = ?');
            $stmt->execute([$id]);
            sendJsonResponse($stmt->fetch(PDO::FETCH_ASSOC));
            break;
            
        case 'DELETE':
            if (!$id) {
                sendError('ID обязателен для удаления');
            }
            $stmt = $db->prepare('SELECT * FROM countries WHERE id = ?');
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                sendError('Страна не найдена', 404);
            }
            $stmt = $db->prepare('DELETE FROM countries WHERE id = ?');
            $stmt->execute([$id]);
            sendJsonResponse(['message' => 'Страна удалена']);
            break;
            
        default:
            sendError('Метод не поддерживается', 405);
    }
}

// Обработка запросов для клиентов
function handleClients($db, $method, $id, $input) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $db->prepare('SELECT * FROM clients WHERE id = ?');
                $stmt->execute([$id]);
                $client = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$client) {
                    sendError('Клиент не найден', 404);
                }
                sendJsonResponse($client);
            } else {
                $stmt = $db->query('SELECT * FROM clients ORDER BY id');
                $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
                sendJsonResponse($clients);
            }
            break;
            
        case 'POST':
            if (!isset($input['first_name']) || !isset($input['last_name']) || !isset($input['email'])) {
                sendError('Имя, фамилия и email обязательны');
            }
            $stmt = $db->prepare('INSERT INTO clients (first_name, last_name, email, phone) VALUES (?, ?, ?, ?)');
            try {
                $stmt->execute([
                    $input['first_name'],
                    $input['last_name'],
                    $input['email'],
                    $input['phone'] ?? null
                ]);
                $id = $db->lastInsertId();
                $stmt = $db->prepare('SELECT * FROM clients WHERE id = ?');
                $stmt->execute([$id]);
                sendJsonResponse($stmt->fetch(PDO::FETCH_ASSOC), 201);
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'UNIQUE constraint') !== false) {
                    sendError('Клиент с таким email уже существует', 409);
                }
                sendError('Ошибка при создании клиента: ' . $e->getMessage());
            }
            break;
            
        case 'PUT':
            if (!$id) {
                sendError('ID обязателен для обновления');
            }
            $stmt = $db->prepare('SELECT * FROM clients WHERE id = ?');
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                sendError('Клиент не найден', 404);
            }
            $stmt = $db->prepare('UPDATE clients SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?');
            try {
                $stmt->execute([
                    $input['first_name'] ?? null,
                    $input['last_name'] ?? null,
                    $input['email'] ?? null,
                    $input['phone'] ?? null,
                    $id
                ]);
                $stmt = $db->prepare('SELECT * FROM clients WHERE id = ?');
                $stmt->execute([$id]);
                sendJsonResponse($stmt->fetch(PDO::FETCH_ASSOC));
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'UNIQUE constraint') !== false) {
                    sendError('Клиент с таким email уже существует', 409);
                }
                sendError('Ошибка при обновлении клиента');
            }
            break;
            
        case 'DELETE':
            if (!$id) {
                sendError('ID обязателен для удаления');
            }
            $stmt = $db->prepare('SELECT * FROM clients WHERE id = ?');
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                sendError('Клиент не найден', 404);
            }
            $stmt = $db->prepare('DELETE FROM clients WHERE id = ?');
            $stmt->execute([$id]);
            sendJsonResponse(['message' => 'Клиент удален']);
            break;
            
        default:
            sendError('Метод не поддерживается', 405);
    }
}

// Обработка запросов для туров
function handleTours($db, $method, $id, $input) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $db->prepare('
                    SELECT t.*, 
                           c.name as country_name, 
                           CONCAT(cl.first_name, " ", cl.last_name) as client_name
                    FROM tours t
                    LEFT JOIN countries c ON t.country_id = c.id
                    LEFT JOIN clients cl ON t.client_id = cl.id
                    WHERE t.id = ?
                ');
                $stmt->execute([$id]);
                $tour = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$tour) {
                    sendError('Тур не найден', 404);
                }
                sendJsonResponse($tour);
            } else {
                $stmt = $db->query('
                    SELECT t.*, 
                           c.name as country_name, 
                           CONCAT(cl.first_name, " ", cl.last_name) as client_name
                    FROM tours t
                    LEFT JOIN countries c ON t.country_id = c.id
                    LEFT JOIN clients cl ON t.client_id = cl.id
                    ORDER BY t.id
                ');
                $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                sendJsonResponse($tours);
            }
            break;
            
        case 'POST':
            if (!isset($input['country_id']) || !isset($input['client_id']) || 
                !isset($input['start_date']) || !isset($input['end_date']) || 
                !isset($input['price'])) {
                sendError('country_id, client_id, start_date, end_date и price обязательны');
            }
            
            // Проверка существования страны и клиента
            $stmt = $db->prepare('SELECT id FROM countries WHERE id = ?');
            $stmt->execute([$input['country_id']]);
            if (!$stmt->fetch()) {
                sendError('Страна не найдена', 404);
            }
            
            $stmt = $db->prepare('SELECT id FROM clients WHERE id = ?');
            $stmt->execute([$input['client_id']]);
            if (!$stmt->fetch()) {
                sendError('Клиент не найден', 404);
            }
            
            $stmt = $db->prepare('INSERT INTO tours (country_id, client_id, start_date, end_date, price, status) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $input['country_id'],
                $input['client_id'],
                $input['start_date'],
                $input['end_date'],
                $input['price'],
                $input['status'] ?? 'planned'
            ]);
            $id = $db->lastInsertId();
            
            $stmt = $db->prepare('
                SELECT t.*, 
                       c.name as country_name, 
                       CONCAT(cl.first_name, " ", cl.last_name) as client_name
                FROM tours t
                LEFT JOIN countries c ON t.country_id = c.id
                LEFT JOIN clients cl ON t.client_id = cl.id
                WHERE t.id = ?
            ');
            $stmt->execute([$id]);
            sendJsonResponse($stmt->fetch(PDO::FETCH_ASSOC), 201);
            break;
            
        case 'PUT':
            if (!$id) {
                sendError('ID обязателен для обновления');
            }
            $stmt = $db->prepare('SELECT * FROM tours WHERE id = ?');
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                sendError('Тур не найден', 404);
            }
            
            // Проверка страны если указана
            if (isset($input['country_id'])) {
                $stmt = $db->prepare('SELECT id FROM countries WHERE id = ?');
                $stmt->execute([$input['country_id']]);
                if (!$stmt->fetch()) {
                    sendError('Страна не найдена', 404);
                }
            }
            
            // Проверка клиента если указан
            if (isset($input['client_id'])) {
                $stmt = $db->prepare('SELECT id FROM clients WHERE id = ?');
                $stmt->execute([$input['client_id']]);
                if (!$stmt->fetch()) {
                    sendError('Клиент не найден', 404);
                }
            }
            
            $stmt = $db->prepare('UPDATE tours SET country_id = ?, client_id = ?, start_date = ?, end_date = ?, price = ?, status = ? WHERE id = ?');
            $stmt->execute([
                $input['country_id'] ?? null,
                $input['client_id'] ?? null,
                $input['start_date'] ?? null,
                $input['end_date'] ?? null,
                $input['price'] ?? null,
                $input['status'] ?? null,
                $id
            ]);
            
            $stmt = $db->prepare('
                SELECT t.*, 
                       c.name as country_name, 
                       CONCAT(cl.first_name, " ", cl.last_name) as client_name
                FROM tours t
                LEFT JOIN countries c ON t.country_id = c.id
                LEFT JOIN clients cl ON t.client_id = cl.id
                WHERE t.id = ?
            ');
            $stmt->execute([$id]);
            sendJsonResponse($stmt->fetch(PDO::FETCH_ASSOC));
            break;
            
        case 'DELETE':
            if (!$id) {
                sendError('ID обязателен для удаления');
            }
            $stmt = $db->prepare('SELECT * FROM tours WHERE id = ?');
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                sendError('Тур не найден', 404);
            }
            $stmt = $db->prepare('DELETE FROM tours WHERE id = ?');
            $stmt->execute([$id]);
            sendJsonResponse(['message' => 'Тур удален']);
            break;
            
        default:
            sendError('Метод не поддерживается', 405);
    }
}

