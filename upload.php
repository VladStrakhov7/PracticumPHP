<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    if ($title && isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['video'];
        $allowed_types = ['video/mp4', 'video/webm', 'video/ogg'];
        $max_size = 500 * 1024 * 1024; // 500MB
        
        if (!in_array($file['type'], $allowed_types)) {
            $error = 'Разрешены только видеофайлы (MP4, WebM, OGG)';
        } elseif ($file['size'] > $max_size) {
            $error = 'Файл слишком большой (максимум 500MB)';
        } else {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $filepath = UPLOAD_DIR . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $pdo = getDB();
                $user = getCurrentUser();
                $stmt = $pdo->prepare("INSERT INTO videos (user_id, title, description, filename) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$user['id'], $title, $description, $filename])) {
                    $success = 'Видео успешно загружено!';
                } else {
                    unlink($filepath);
                    $error = 'Ошибка при сохранении в базу данных';
                }
            } else {
                $error = 'Ошибка при загрузке файла';
            }
        }
    } else {
        $error = 'Заполните все поля и выберите файл';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Загрузка видео - Видеохостинг</title>
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
        <h2>Загрузка видеоролика</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="upload-form">
            <div class="form-group">
                <label>Название видео:</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>Описание:</label>
                <textarea name="description" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Видеофайл (MP4, WebM, OGG, до 500MB):</label>
                <input type="file" name="video" accept="video/*" required>
            </div>
            <button type="submit">Загрузить</button>
        </form>
    </main>
</body>
</html>

