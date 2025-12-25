<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REST API - Комфорт-отдых</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
        }
        h2 {
            color: #555;
            margin-top: 30px;
        }
        .endpoint {
            background: #f9f9f9;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #4CAF50;
            border-radius: 4px;
        }
        .method {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            margin-right: 10px;
            font-size: 12px;
        }
        .get { background: #2196F3; color: white; }
        .post { background: #4CAF50; color: white; }
        .put { background: #FF9800; color: white; }
        .delete { background: #f44336; color: white; }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .example {
            background: #fff3cd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            border-left: 4px solid #ffc107;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 REST API - Комфорт-отдых</h1>
        <p>API для управления странами, клиентами и турами туристической компании.</p>
        
        <h2>Быстрый старт</h2>
        <p>Базовый URL: <code>http://localhost/RestApi/api.php</code></p>
        
        <h2>📋 Endpoints</h2>
        
        <h3>Страны (Countries)</h3>
        <div class="endpoint">
            <span class="method get">GET</span>
            <code>/countries</code> - Получить все страны<br>
            <a href="api.php/countries" target="_blank">Попробовать →</a>
        </div>
        <div class="endpoint">
            <span class="method get">GET</span>
            <code>/countries/{id}</code> - Получить страну по ID
        </div>
        <div class="endpoint">
            <span class="method post">POST</span>
            <code>/countries</code> - Создать страну
        </div>
        <div class="endpoint">
            <span class="method put">PUT</span>
            <code>/countries/{id}</code> - Обновить страну
        </div>
        <div class="endpoint">
            <span class="method delete">DELETE</span>
            <code>/countries/{id}</code> - Удалить страну
        </div>
        
        <h3>Клиенты (Clients)</h3>
        <div class="endpoint">
            <span class="method get">GET</span>
            <code>/clients</code> - Получить всех клиентов<br>
            <a href="api.php/clients" target="_blank">Попробовать →</a>
        </div>
        <div class="endpoint">
            <span class="method get">GET</span>
            <code>/clients/{id}</code> - Получить клиента по ID
        </div>
        <div class="endpoint">
            <span class="method post">POST</span>
            <code>/clients</code> - Создать клиента
        </div>
        <div class="endpoint">
            <span class="method put">PUT</span>
            <code>/clients/{id}</code> - Обновить клиента
        </div>
        <div class="endpoint">
            <span class="method delete">DELETE</span>
            <code>/clients/{id}</code> - Удалить клиента
        </div>
        
        <h3>Туры (Tours)</h3>
        <div class="endpoint">
            <span class="method get">GET</span>
            <code>/tours</code> - Получить все туры<br>
            <a href="api.php/tours" target="_blank">Попробовать →</a>
        </div>
        <div class="endpoint">
            <span class="method get">GET</span>
            <code>/tours/{id}</code> - Получить тур по ID
        </div>
        <div class="endpoint">
            <span class="method post">POST</span>
            <code>/tours</code> - Создать тур
        </div>
        <div class="endpoint">
            <span class="method put">PUT</span>
            <code>/tours/{id}</code> - Обновить тур
        </div>
        <div class="endpoint">
            <span class="method delete">DELETE</span>
            <code>/tours/{id}</code> - Удалить тур
        </div>
        
        <div class="example">
            <strong>💡 Пример использования с curl:</strong><br>
            <code>curl http://localhost/RestApi/api.php/countries</code>
        </div>
        
        <p style="margin-top: 30px; color: #666;">
            📖 Подробная документация доступна в файле <code>README.md</code>
        </p>
    </div>
</body>
</html>

