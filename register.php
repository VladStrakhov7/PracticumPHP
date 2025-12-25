<?php
require_once 'config.php';
require_once 'functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if ($username && $email && $password && $password_confirm) {
        if ($password !== $password_confirm) {
            $error = 'Пароли не совпадают';
        } elseif (strlen($password) < 6) {
            $error = 'Пароль должен быть не менее 6 символов';
        } else {
            $pdo = getDB();
            
            // Проверка существования пользователя
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = 'Пользователь с таким именем или email уже существует';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
                if ($stmt->execute([$username, $email, $hashed_password])) {
                    $success = 'Регистрация успешна! <a href="login.php">Войдите</a>';
                } else {
                    $error = 'Ошибка при регистрации';
                }
            }
        }
    } else {
        $error = 'Заполните все поля';
    }
}

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Видеохостинг</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h1>Регистрация</h1>
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success"><?= $success ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Имя пользователя:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Пароль:</label>
                    <input type="password" name="password" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Подтверждение пароля:</label>
                    <input type="password" name="password_confirm" required minlength="6">
                </div>
                <button type="submit">Зарегистрироваться</button>
            </form>
            <p><a href="login.php">Уже есть аккаунт? Войдите</a></p>
            <p><a href="index.php">На главную</a></p>
        </div>
    </div>
</body>
</html>

