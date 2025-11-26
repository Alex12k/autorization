/**
 * Модуль обработки генерации тестовых пользователей
 */

// Вызов формы генерации пользователей по клику (открывает модальное окно)
$(document).on('click', '.open_modal_database_demo, .open_modal_seed_users', function(e) {
    e.preventDefault();
    
    // Закрываем все открытые модальные окна перед открытием нового
    $.arcticmodal('close');
    
    $.post('/components/auth/dev/database-demo/ajax/ajax.php', {action: 'open_modal_seed_users'}, function(res) {
        $(res).arcticmodal({closeOnOverlayClick: false});
        
        // Инициализируем форму после открытия модального окна
        initSeedUsersForm();
    });
});

/**
 * Инициализация формы генерации пользователей
 */
function initSeedUsersForm() {
    const form = $('#seed-users-form');
    const submitBtn = $('#seed-submit-btn');
    const cancelBtn = $('#seed-cancel-btn');
    const progressContainer = $('#seed-progress-container');
    const progressBar = $('#seed-progress-bar');
    const progressText = $('#seed-progress-text');
    const statusText = $('#seed-status-text');
    const resultDiv = $('#seed-result');
    
    let isProcessing = false;
    
    // Обработка отправки формы
    form.on('submit', function(e) {
        e.preventDefault();
        
        if (isProcessing) {
            return;
        }
        
        const amount = parseInt($('input[name="amount"]:checked').val(), 10);
        
        if (!amount || amount <= 0) {
            showToast('Пожалуйста, выберите количество пользователей', 'error');
            return;
        }
        
        // Подтверждение для больших объемов
        if (amount >= 1000) {
            const confirmMessage = `Вы уверены, что хотите создать ${amount.toLocaleString('ru-RU')} пользователей? Это может занять некоторое время.`;
            if (!confirm(confirmMessage)) {
                return;
            }
        }
        
        // Блокируем форму
        isProcessing = true;
        submitBtn.prop('disabled', true).html('<i class="ri-loader-4-line mr-2 animate-spin"></i>Создание...');
        cancelBtn.removeClass('hidden');
        progressContainer.removeClass('hidden');
        resultDiv.addClass('hidden');
        
        // Сбрасываем прогресс
        updateProgress(0, 'Подготовка...');
        
        // Отправляем AJAX запрос
        $.ajax({
            url: '/components/auth/dev/database-demo/ajax/ajax.php',
            method: 'POST',
            data: {
                action: 'seed_users',
                amount: amount
            },
            dataType: 'json',
            timeout: 300000, // 5 минут таймаут
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                
                // Симуляция прогресса (так как мы не можем получить реальный прогресс из PHP)
                let progress = 0;
                const progressInterval = setInterval(function() {
                    if (progress < 90 && isProcessing) {
                        progress += Math.random() * 5;
                        if (progress > 90) progress = 90;
                        updateProgress(Math.floor(progress), 'Создание пользователей...');
                    }
                }, 500);
                
                // Очищаем интервал при завершении
                $(document).one('ajaxComplete', function() {
                    clearInterval(progressInterval);
                });
                
                return xhr;
            },
            success: function(response) {
                clearInterval();
                updateProgress(100, 'Завершено!');
                
                if (response.success) {
                    setTimeout(function() {
                        showResult('success', response);
                        resetForm();
                    }, 500);
                } else {
                    showResult('error', response);
                    resetForm();
                }
            },
            error: function(xhr, status, error) {
                clearInterval();
                updateProgress(0, 'Ошибка');
                
                let errorMessage = 'Произошла ошибка при создании пользователей';
                
                if (status === 'timeout') {
                    errorMessage = 'Превышено время ожидания. Попробуйте создать меньше пользователей или повторите попытку.';
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                
                showResult('error', {error: errorMessage});
                resetForm();
            }
        });
    });
    
    // Обработка отмены
    cancelBtn.on('click', function() {
        if (confirm('Вы уверены, что хотите отменить создание пользователей?')) {
            // Отменяем AJAX запрос (если возможно)
            // К сожалению, мы не можем отменить уже начатый запрос на сервере
            resetForm();
        }
    });
    
    /**
     * Обновление прогресс-бара
     */
    function updateProgress(percent, status) {
        progressBar.css('width', percent + '%');
        progressText.text(percent + '%');
        statusText.text(status);
    }
    
    /**
     * Показ результата
     */
    function showResult(type, data) {
        resultDiv.removeClass('hidden');
        
        if (type === 'success') {
            resultDiv.html(`
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="ri-checkbox-circle-line text-green-600 text-xl mr-3 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="font-semibold text-green-800 mb-1">Успешно!</p>
                            <p class="text-sm text-green-700">${data.message || 'Пользователи успешно созданы'}</p>
                            ${data.created ? `<p class="text-sm text-green-600 mt-2">Создано: <strong>${data.created.toLocaleString('ru-RU')}</strong> из <strong>${data.requested.toLocaleString('ru-RU')}</strong></p>` : ''}
                        </div>
                    </div>
                </div>
            `);
            showToast(data.message || 'Пользователи успешно созданы', 'success');
        } else {
            resultDiv.html(`
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="ri-error-warning-line text-red-600 text-xl mr-3 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="font-semibold text-red-800 mb-1">Ошибка</p>
                            <p class="text-sm text-red-700">${data.error || 'Произошла ошибка при создании пользователей'}</p>
                        </div>
                    </div>
                </div>
            `);
            showToast(data.error || 'Произошла ошибка', 'error');
        }
    }
    
    /**
     * Сброс формы
     */
    function resetForm() {
        isProcessing = false;
        submitBtn.prop('disabled', false).html('<i class="ri-play-line mr-2"></i>Создать пользователей');
        cancelBtn.addClass('hidden');
        progressContainer.addClass('hidden');
    }
}

console.log('Seed users module initialized');
