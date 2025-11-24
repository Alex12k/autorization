/**
 * Модуль обработки смены пароля
 * Переиспользует функции из password-recovery
 */

// Вызов формы смены пароля по клику
$(document).on('click', '.open_modal_change_password_form', function(e) {
    e.preventDefault();
    $.arcticmodal('close');
    $.post('/components/auth/change-password/ajax/ajax.php', {action: 'open_modal_change_password_form'}, function(res) {
        $(res).arcticmodal({closeOnOverlayClick: false});
    });
});

// Обработка отправки формы смены пароля
$(document).on('submit', '#changePasswordForm', function(e) {
    e.preventDefault();
    
    const $form = $(this);
    const $submitBtn = $form.find('button[type="submit"]');
    const originalText = $submitBtn.html();
    
    // Блокируем кнопку
    $submitBtn.prop('disabled', true).html('<i class="ri-loader-4-line mr-2 animate-spin"></i>Изменение...');
    
    // Собираем данные формы
    const formData = {
        action: 'change-password',
        csrf_token: $form.find('input[name="csrf_token"]').val(),
        current_password: $form.find('input[name="current_password"]').val(),
        new_password: $form.find('input[name="new_password"]').val(),
        confirm_password: $form.find('input[name="confirm_password"]').val()
    };
    
    $.post('/components/auth/change-password/ajax/ajax.php', formData, function(res) {
        if (res && res.success) {
            // Показываем успешное сообщение
            if (typeof showToast === 'function') {
                showToast(res.message || 'Пароль успешно изменен', 'success');
            } else {
                alert(res.message || 'Пароль успешно изменен');
            }
            
            // Закрываем модальное окно
            setTimeout(() => {
                $.arcticmodal('close');
            }, 1500);
        } else {
            // Показываем ошибку
            const errorMsg = res.error || 'Ошибка при изменении пароля';
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

console.log('Change password module initialized');

