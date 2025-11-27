/**
 * Утилита для показа toast уведомлений
 * Используется во всех компонентах для единообразных уведомлений
 */

/**
 * Показать toast уведомление
 * @param {string} message - Текст сообщения
 * @param {string} type - Тип уведомления: 'success' или 'error'
 */
function showToast(message, type = 'success') {
    // Ищем или создаем контейнер для toast
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 space-y-2';
        container.style.zIndex = '9999'; // Выше модального окна (z-index: 1000)
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const icon = type === 'success' ? 'ri-check-line' : 'ri-error-warning-line';

    toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 transform transition-all duration-300 opacity-0 translate-x-full`;
    toast.innerHTML = `
        <i class="${icon} text-2xl"></i>
        <span class="font-medium">${message}</span>
    `;

    container.appendChild(toast);

    // Анимация появления
    setTimeout(() => {
        toast.classList.remove('opacity-0', 'translate-x-full');
    }, 10);

    // Удаление через 3 секунды
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

