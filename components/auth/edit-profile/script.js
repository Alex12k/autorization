/**
 * Модуль обработки редактирования профиля
 * Переиспользует структуру из change-password
 */

// Вызов формы редактирования профиля по клику
$(document).on('click', '.open_modal_edit_profile_form', function(e) {
    e.preventDefault();
    $.arcticmodal('close');
    $.post('/components/auth/edit-profile/ajax/ajax.php', {action: 'open_modal_edit_profile_form'}, function(res) {
        $(res).arcticmodal({closeOnOverlayClick: false});
    });
});

// Обработка отправки формы редактирования профиля
$(document).on('submit', '#editProfileForm', function(e) {
    e.preventDefault();
    
    const $form = $(this);
    const $submitBtn = $form.find('button[type="submit"]');
    const originalText = $submitBtn.html();
    
    // Блокируем кнопку
    $submitBtn.prop('disabled', true).html('<i class="ri-loader-4-line mr-2 animate-spin"></i>Сохранение...');
    
    // Собираем данные формы
    const formData = {
        action: 'edit-profile',
        csrf_token: $form.find('input[name="csrf_token"]').val(),
        username: $form.find('input[name="username"]').val(),
        email: $form.find('input[name="email"]').val()
    };
    
    $.post('/components/auth/edit-profile/ajax/ajax.php', formData, function(res) {
        if (res && res.success) {
            // Показываем успешное сообщение
            if (typeof showToast === 'function') {
                showToast(res.message || 'Профиль успешно обновлен', 'success');
            } else {
                alert(res.message || 'Профиль успешно обновлен');
            }
            
            // Закрываем модальное окно и перезагружаем страницу для обновления данных
            setTimeout(() => {
                $.arcticmodal('close');
                window.location.reload();
            }, 1500);
        } else {
            // Показываем ошибку
            const errorMsg = res.error || 'Ошибка при обновлении профиля';
            if (typeof showToast === 'function') {
                showToast(errorMsg, 'error');
            } else {
                alert(errorMsg);
            }
            
            // Разблокируем кнопку
            $submitBtn.prop('disabled', false).html(originalText);
        }
    }).fail(function() {
        // Ошибка сети
        const errorMsg = 'Ошибка сети. Попробуйте позже.';
        if (typeof showToast === 'function') {
            showToast(errorMsg, 'error');
        } else {
            alert(errorMsg);
        }
        
        // Разблокируем кнопку
        $submitBtn.prop('disabled', false).html(originalText);
    });
});

console.log('Edit profile module initialized');

