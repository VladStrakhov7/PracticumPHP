<?php
require_once 'config.php';
require_once 'functions.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$pdo = getDB();
$videos = $pdo->query("SELECT v.*, u.username FROM videos v JOIN users u ON v.user_id = u.id ORDER BY v.upload_date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Видеохостинг</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Видеохостинг</h1>
            <nav>
                <a href="index.php">На главную</a>
                <a href="logout.php">Выход</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Админ-панель - Управление видеороликами</h2>
        
        <div class="admin-videos">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Автор</th>
                        <th>Просмотры</th>
                        <th>Дата загрузки</th>
                        <th>Ограничения</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($videos as $video): ?>
                        <tr>
                            <td><?= $video['id'] ?></td>
                            <td><?= htmlspecialchars($video['title']) ?></td>
                            <td><?= htmlspecialchars($video['username']) ?></td>
                            <td><?= $video['views'] ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($video['upload_date'])) ?></td>
                            <td>
                                <textarea class="restrictions-input" 
                                          data-video-id="<?= $video['id'] ?>" 
                                          rows="2"><?= htmlspecialchars($video['restrictions'] ?? '') ?></textarea>
                            </td>
                            <td>
                                <a href="video.php?id=<?= $video['id'] ?>">Просмотр</a>
                                <button class="save-restrictions-btn" data-video-id="<?= $video['id'] ?>">Сохранить</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>

