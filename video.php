<?php
require_once 'config.php';
require_once 'functions.php';

$video_id = $_GET['id'] ?? 0;
$video = getVideoById($video_id);

if (!$video) {
    header('Location: index.php');
    exit;
}

// Увеличиваем просмотры
incrementViews($video_id);
$video = getVideoById($video_id); // Обновляем данные

$comments = getCommentsByVideoId($video_id);
$user = getCurrentUser();
$user_like = $user ? getUserLike($video_id, $user['id']) : null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($video['title']) ?> - Видеохостинг</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Видеохостинг</h1>
            <nav>
                <a href="index.php">На главную</a>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="admin.php">Админ-панель</a>
                    <?php endif; ?>
                    <a href="logout.php">Выход</a>
                <?php else: ?>
                    <a href="login.php">Вход</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="video-page">
            <div class="video-player">
                <video controls>
                    <source src="<?= htmlspecialchars(UPLOAD_URL . $video['filename']) ?>" type="video/mp4">
                    Ваш браузер не поддерживает видео.
                </video>
            </div>
            
            <div class="video-info">
                <h2><?= htmlspecialchars($video['title']) ?></h2>
                <p class="video-meta">
                    Автор: <?= htmlspecialchars($video['username']) ?> | 
                    Дата: <?= date('d.m.Y H:i', strtotime($video['upload_date'])) ?> | 
                    Просмотров: <?= $video['views'] ?>
                </p>
                <?php if ($video['description']): ?>
                    <p class="video-description"><?= nl2br(htmlspecialchars($video['description'])) ?></p>
                <?php endif; ?>
                
                <div class="likes-section">
                    <?php if (isLoggedIn()): ?>
                        <button class="like-btn <?= $user_like === 'like' ? 'active' : '' ?>" 
                                data-video-id="<?= $video_id ?>" data-type="like">
                            👍 <span id="likes-count"><?= $video['likes_count'] ?></span>
                        </button>
                        <button class="dislike-btn <?= $user_like === 'dislike' ? 'active' : '' ?>" 
                                data-video-id="<?= $video_id ?>" data-type="dislike">
                            👎 <span id="dislikes-count"><?= $video['dislikes_count'] ?></span>
                        </button>
                    <?php else: ?>
                        <p>👍 <?= $video['likes_count'] ?> | 👎 <?= $video['dislikes_count'] ?></p>
                        <p><a href="login.php">Войдите</a>, чтобы поставить лайк</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="comments-section">
                <h3>Комментарии (<?= count($comments) ?>)</h3>
                
                <?php if (isLoggedIn()): ?>
                    <form class="comment-form" id="comment-form">
                        <input type="hidden" name="video_id" value="<?= $video_id ?>">
                        <textarea name="comment" placeholder="Оставьте комментарий..." required></textarea>
                        <button type="submit">Отправить</button>
                    </form>
                <?php else: ?>
                    <p><a href="login.php">Войдите</a>, чтобы оставить комментарий</p>
                <?php endif; ?>
                
                <div class="comments-list" id="comments-list">
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <strong><?= htmlspecialchars($comment['username']) ?></strong>
                            <span class="comment-date"><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></span>
                            <p><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>

