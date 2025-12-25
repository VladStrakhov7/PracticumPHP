// Обработка лайков и дизлайков
document.addEventListener('DOMContentLoaded', function() {
    // Лайки
    const likeButtons = document.querySelectorAll('.like-btn, .dislike-btn');
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const videoId = this.dataset.videoId;
            const type = this.dataset.type;
            
            const formData = new FormData();
            formData.append('video_id', videoId);
            formData.append('type', type);
            
            fetch('api.php?action=like', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Обновляем счетчики
                    document.getElementById('likes-count').textContent = data.likes_count;
                    document.getElementById('dislikes-count').textContent = data.dislikes_count;
                    
                    // Обновляем активные кнопки
                    const likeBtn = document.querySelector('.like-btn');
                    const dislikeBtn = document.querySelector('.dislike-btn');
                    
                    likeBtn.classList.remove('active');
                    dislikeBtn.classList.remove('active');
                    
                    if (data.user_like === 'like') {
                        likeBtn.classList.add('active');
                    } else if (data.user_like === 'dislike') {
                        dislikeBtn.classList.add('active');
                    }
                } else {
                    alert('Ошибка: ' + (data.error || 'Неизвестная ошибка'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при отправке запроса');
            });
        });
    });
    
    // Комментарии
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('comment', this.querySelector('textarea[name="comment"]').value);
            
            fetch('api.php?action=comment', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Добавляем новый комментарий в список
                    const commentsList = document.getElementById('comments-list');
                    const commentDiv = document.createElement('div');
                    commentDiv.className = 'comment';
                    
                    const date = new Date(data.comment.created_at);
                    const formattedDate = date.toLocaleDateString('ru-RU') + ' ' + 
                                        date.toLocaleTimeString('ru-RU', {hour: '2-digit', minute: '2-digit'});
                    
                    commentDiv.innerHTML = `
                        <strong>${escapeHtml(data.comment.username)}</strong>
                        <span class="comment-date">${formattedDate}</span>
                        <p>${escapeHtml(data.comment.text).replace(/\n/g, '<br>')}</p>
                    `;
                    
                    commentsList.insertBefore(commentDiv, commentsList.firstChild);
                    
                    // Очищаем форму
                    this.querySelector('textarea[name="comment"]').value = '';
                } else {
                    alert('Ошибка: ' + (data.error || 'Неизвестная ошибка'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при отправке комментария');
            });
        });
    }
    
    // Сохранение ограничений в админ-панели
    const saveButtons = document.querySelectorAll('.save-restrictions-btn');
    saveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const videoId = this.dataset.videoId;
            const restrictionsInput = document.querySelector(`.restrictions-input[data-video-id="${videoId}"]`);
            const restrictions = restrictionsInput.value;
            
            const formData = new FormData();
            formData.append('video_id', videoId);
            formData.append('restrictions', restrictions);
            
            fetch('api.php?action=update_restrictions', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Ограничения успешно сохранены');
                } else {
                    alert('Ошибка: ' + (data.error || 'Неизвестная ошибка'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при сохранении');
            });
        });
    });
});

// Функция для экранирования HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

