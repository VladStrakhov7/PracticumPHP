<?php
require_once 'config.php';
require_once 'functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Требуется авторизация']);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'like':
        $video_id = intval($_POST['video_id'] ?? 0);
        $type = $_POST['type'] ?? '';
        $user = getCurrentUser();
        
        if ($video_id && in_array($type, ['like', 'dislike'])) {
            $pdo = getDB();
            
            // Проверяем существующий лайк
            $stmt = $pdo->prepare("SELECT type FROM likes WHERE video_id = ? AND user_id = ?");
            $stmt->execute([$video_id, $user['id']]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                if ($existing['type'] === $type) {
                    // Удаляем лайк, если кликнули на тот же тип
                    $stmt = $pdo->prepare("DELETE FROM likes WHERE video_id = ? AND user_id = ?");
                    $stmt->execute([$video_id, $user['id']]);
                } else {
                    // Меняем тип лайка
                    $stmt = $pdo->prepare("UPDATE likes SET type = ? WHERE video_id = ? AND user_id = ?");
                    $stmt->execute([$type, $video_id, $user['id']]);
                }
            } else {
                // Создаем новый лайк
                $stmt = $pdo->prepare("INSERT INTO likes (video_id, user_id, type) VALUES (?, ?, ?)");
                $stmt->execute([$video_id, $user['id'], $type]);
            }
            
            // Получаем обновленные счетчики
            $stmt = $pdo->prepare("SELECT 
                (SELECT COUNT(*) FROM likes WHERE video_id = ? AND type = 'like') as likes_count,
                (SELECT COUNT(*) FROM likes WHERE video_id = ? AND type = 'dislike') as dislikes_count,
                (SELECT type FROM likes WHERE video_id = ? AND user_id = ?) as user_like");
            $stmt->execute([$video_id, $video_id, $video_id, $user['id']]);
            $result = $stmt->fetch();
            
            echo json_encode([
                'success' => true,
                'likes_count' => $result['likes_count'],
                'dislikes_count' => $result['dislikes_count'],
                'user_like' => $result['user_like']
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Неверные параметры']);
        }
        break;
        
    case 'comment':
        $video_id = intval($_POST['video_id'] ?? 0);
        $text = trim($_POST['comment'] ?? '');
        $user = getCurrentUser();
        
        if ($video_id && $text) {
            $pdo = getDB();
            $stmt = $pdo->prepare("INSERT INTO comments (video_id, user_id, text) VALUES (?, ?, ?)");
            if ($stmt->execute([$video_id, $user['id'], $text])) {
                $comment_id = $pdo->lastInsertId();
                $stmt = $pdo->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.id = ?");
                $stmt->execute([$comment_id]);
                $comment = $stmt->fetch();
                
                echo json_encode([
                    'success' => true,
                    'comment' => [
                        'id' => $comment['id'],
                        'username' => $comment['username'],
                        'text' => $comment['text'],
                        'created_at' => $comment['created_at']
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Ошибка при сохранении комментария']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Заполните все поля']);
        }
        break;
        
    case 'update_restrictions':
        if (!isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Доступ запрещен']);
            exit;
        }
        
        $video_id = intval($_POST['video_id'] ?? 0);
        $restrictions = trim($_POST['restrictions'] ?? '');
        
        if ($video_id) {
            $pdo = getDB();
            $stmt = $pdo->prepare("UPDATE videos SET restrictions = ? WHERE id = ?");
            if ($stmt->execute([$restrictions, $video_id])) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Ошибка при обновлении']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Неверные параметры']);
        }
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Неизвестное действие']);
}

