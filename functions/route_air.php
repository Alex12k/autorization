<?php
function get_clean_uri(){$url_parts = parse_url($_SERVER['REQUEST_URI']);$path = $url_parts['path'];if($path !== '/'){$path = rtrim($path, "/");}return $path;}
function rt(){$url = get_clean_uri();
	switch ($url) {
			case '/': 															home(); 						   	break;
			case '/dashboard': 												dashboard(); 						break;
			case '/admin': 										   		admin();  							break;
		default: 															   echo 'Страница не найдена'; 	break;
	}}

