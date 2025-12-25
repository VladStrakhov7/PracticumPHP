<?php
require_once 'config.php';
error_reporting(E_ALL);

// Проверка авторизации
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Получение текущего пользователя
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Проверка роли
function hasRole($role) {
    $user = getCurrentUser();
    return $user && $user['role'] === $role;
}

// Проверка является ли администратором
function isAdmin() {
    return hasRole('admin');
}

// Получение всех видеороликов
function getAllVideos($limit = 50) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare('SELECT * FROM videos');
        $stmt->execute();
        //var_dump($stmt->fetchAll());
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        var_dump($e->errorInfo);
        return [];
    }
}

// Получение видео по ID
function getVideoById($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT v.*, u.username,
                          (SELECT COUNT(*) FROM likes WHERE video_id = v.id AND type = 'like') as likes_count,
                          (SELECT COUNT(*) FROM likes WHERE video_id = v.id AND type = 'dislike') as dislikes_count
                          FROM videos v 
                          JOIN users u ON v.user_id = u.id 
                          WHERE v.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Получение комментариев к видео
function getCommentsByVideoId($video_id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT c.*, u.username 
                          FROM comments c 
                          JOIN users u ON c.user_id = u.id 
                          WHERE c.video_id = ? 
                          ORDER BY c.created_at DESC");
    $stmt->execute([$video_id]);
    return $stmt->fetchAll();
}

// Проверка лайка пользователя
function getUserLike($video_id, $user_id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT type FROM likes WHERE video_id = ? AND user_id = ?");
    $stmt->execute([$video_id, $user_id]);
    $result = $stmt->fetch();
    return $result ? $result['type'] : null;
}

// Увеличение просмотров
function incrementViews($video_id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("UPDATE videos SET views = views + 1 WHERE id = ?");
    $stmt->execute([$video_id]);
}

