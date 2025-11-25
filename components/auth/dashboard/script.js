/**
 * Модуль обновления времени в dashboard
 * Обновляет время в системе и текущее время в реальном времени
 */

(function() {
    'use strict';

    /**
     * Форматирует секунды в формат H:i:s
     */
    function formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        
        return [
            String(hours).padStart(2, '0'),
            String(minutes).padStart(2, '0'),
            String(secs).padStart(2, '0')
        ].join(':');
    }

    /**
     * Обновляет время в системе
     */
    function updateSessionTime() {
        const sessionTimeElements = document.querySelectorAll('.dashboard-session-time');
        
        sessionTimeElements.forEach(function(element) {
            const loginTime = parseInt(element.getAttribute('data-login-time'), 10);
            
            if (loginTime) {
                const currentTime = Math.floor(Date.now() / 1000);
                const sessionDuration = currentTime - loginTime;
                element.textContent = formatTime(sessionDuration);
            }
        });
    }

    /**
     * Обновляет текущее время
     */
    function updateCurrentTime() {
        const currentTimeElements = document.querySelectorAll('.dashboard-current-time');
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const timeString = `${hours}:${minutes}:${seconds}`;
        
        currentTimeElements.forEach(function(element) {
            element.textContent = timeString;
        });
    }

    /**
     * Инициализация обновления времени
     */
    function initTimeUpdates() {
        // Обновляем сразу при загрузке
        updateSessionTime();
        updateCurrentTime();
        
        // Обновляем каждую секунду
        setInterval(function() {
            updateSessionTime();
            updateCurrentTime();
        }, 1000);
    }

    // Запускаем при загрузке DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTimeUpdates);
    } else {
        initTimeUpdates();
    }
})();

