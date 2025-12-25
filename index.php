<?php
require_once 'config.php';
require_once 'functions.php';

// Проверка подключения к БД перед выполнением запросов
try {
    $pdo = getDB();
    // Проверяем существование таблицы videos
    $pdo->query("SELECT 1 FROM videos LIMIT 1");
} catch (PDOException $e) {
    die("<h1>Ошибка подключения к базе данных</h1>
         <p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>
         <p><strong>Решение:</strong></p>
         <ol>
             <li>Убедитесь, что MySQL запущен в XAMPP</li>
             <li>Откройте phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>
             <li>Выполните SQL-скрипт из файла <code>database.sql</code></li>
         </ol>
         <p><a href='test.php'>Проверить настройки</a></p>");
}

try {
    $videos = getAllVideos();
    var_dump($videos);
    $user = getCurrentUser();
} catch (Exception $e) {
    die("Ошибка: " . htmlspecialchars($e->getMessage()) . "<br><a href='test.php'>Проверить настройки</a>");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Видеохостинг</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Видеохостинг</h1>
            <nav>
                <?php if (isLoggedIn()): ?>
                    <span>Привет, <?= htmlspecialchars($user['username']) ?>!</span>
                    <?php if (isAdmin()): ?>
                        <a href="admin.php">Админ-панель</a>
                    <?php endif; ?>
                    <a href="upload.php">Загрузить видео</a>
                    <a href="logout.php">Выход</a>
                <?php else: ?>
                    <a href="login.php">Вход</a>
                    <a href="register.php">Регистрация</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Все видеоролики</h2>
        <div class="videos-grid">
            <?php if (empty($videos)): ?>
                <p>Видеороликов пока нет. Будьте первым, кто загрузит видео!</p>
            <?php else: ?>
                <?php foreach ($videos as $video): ?>
                    <div class="video-card">
                        <a href="video.php?id=<?= $video['id'] ?>">
                            <div class="video-thumbnail">
                                <video>
                                    <source src="<?= htmlspecialchars(UPLOAD_URL . $video['filename']) ?>" type="video/mp4">
                                </video>
                                <div class="play-overlay">▶</div>
                            </div>
                            <h3><?= htmlspecialchars($video['title']) ?></h3>
                            <p class="video-meta">
                                Автор: <?= htmlspecialchars($video['username']) ?><br>
                                Просмотров: <?= $video['views'] ?><br>
                                👍 <?= $video['likes_count'] ?> | 👎 <?= $video['dislikes_count'] ?>
                            </p>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>

