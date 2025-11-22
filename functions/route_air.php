<?php
function get_clean_uri(){$url_parts = parse_url($_SERVER['REQUEST_URI']);$path = $url_parts['path'];if($path !== '/'){$path = rtrim($path, "/");}return $path;}
function rt(){$url = get_clean_uri();
	// Проверка токена для сброса пароля на корневом URL
	if ($url === '/' && isset($_GET['token']) && !empty($_GET['token'])) {
		// Загружаем функцию resetPassword
		require_once __DIR__ . '/../components/auth/reset-password/reset_password.php';
		resetPassword();
		return;
	}
	
	switch ($url) {
			case '/': 															home(); 						   	break;
			case '/auth': 														auth(); 						      break;
			case '/dashboard': 												dashboard(); 						break;
			case '/admin': 										   		admin();  							break;
		default: 															   echo 'Страница не найдена'; 	break;
	}}

