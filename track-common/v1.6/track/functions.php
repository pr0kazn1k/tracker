<?php

// Будем показывать только серьёзные ошибки
ini_set('display_errors', true);
error_reporting(E_ERROR | E_PARSE);

/*
 * Список файлов из директории
 */
function dir_files($path, $type = '') {
	$files = array();
	if ($handle = opendir($path)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..' && !is_dir($path . $file)) {
				if($type != '' and (strstr($file, '*') !== false or $file == '.' . $type . '_' . date('Y-m-d-H-i'))) continue;
				$files[] = $file;
			}
		}
	}
	sort($files);
	return $files;
}

/*
* Проверяем платформу
*/
function get_platform($os) {
	switch($os) {
		case 'Android':
			$p = 'Android';
			break;
		
		case 'iOS':
			$p = 'iOS';
			break;
		
		case 'Symbian OS':
		case 'Tizen':
		case 'Linux Smartphone OS':
		case 'Windows Mobile OS':
		case 'Windows Phone OS':
		case 'Windows RT':
			$p = 'Мобильная';
			break;
		
		case 'Windows':
		case 'Windows':
		case 'Windows 2000':
		case 'Windows 7':
		case 'Windows 8':
		case 'Windows 98':
		case 'Windows Vista':
		case 'Windows XP':
			$p = 'Windows';
			break;
			
		case 'Mac OS X':
			$p = 'MacOS';
			break;
		
		case 'Linux':
		case 'OpenBSD':
		case 'Ubuntu':
			$p = 'Linux';
			break;
			
		default:
			$p = 'Десктоп';
	}
	return $p;
}

function check_device($mask, $device) {
	if($mask == 'DEFINED_IPAD') {
		return (substr($device, 0, 11) == 'Apple; iPad') ? true : false;
	}
	if($mask == 'DEFINED_IPHONE') {
		return (substr($device, 0, 13) == 'Apple; iPhone') ? true : false;
	}
	return false;
}

function check_platform($mask, $platform) {
	$platforms = array(
		'DEFINED_IOS' => array(
			'iOS'
		),
		'DEFINED_ANDROID' => array(
			'Android'
		),
		'DEFINED_WINDOWS' => array(
			'Windows',
			'Windows',
			'Windows 2000',
			'Windows 7',
			'Windows 8',
			'Windows 98',
			'Windows Vista',
			'Windows XP'
		),
		'DEFINED_MACOS' => array(
			'Mac OS X'
		),
		'DEFINED_LINUX' => array(
			'Linux',
			'OpenBSD',
			'Ubuntu'
		),
		'DEFINED_MOBILE' => array(
			'Symbian OS',
			'Tizen',
			'Linux Smartphone OS',
			'Windows Mobile OS',
			'Windows Phone OS',
			'Windows RT'
		)
	);
	
	if(!empty($platform)) {
		if($mask == 'DEFINED_DESKTOP') {
			$mobile = array_merge($platforms['DEFINED_IOS'], $platforms['DEFINED_ANDROID'], $platforms['DEFINED_MOBILE']);
			return !in_array($platform, $mobile);
			
		} elseif($mask == 'DEFINED_MOBILE') {
			$mobile = array_merge($platforms['DEFINED_IOS'], $platforms['DEFINED_ANDROID'], $platforms['DEFINED_MOBILE']);
			return in_array($platform, $mobile);
			
		} elseif(array_key_exists($mask, $platforms)) {
			return in_array($platform, $platforms[$mask]);
		}
	}
	return false;
}

/*
 * Проверка правила IP
 * Примеры верных диапазонов: 8.8.8.9 - 8.8.10.255, 212.11.92.*, 212.11.*.*, 212.10.*.100 
 */
 
function check_ip($mask, $ip) {
	// Убираем пробелы рядом с дефисом
	$mask = str_replace(' -', '-', $mask);
	$mask = str_replace('- ', '-', $mask);
	
	// Заменяем все разделители запятыми
	$mask = str_replace(';', ' ', $mask);
	$mask = str_replace(',', ' ', $mask);
	$mask = preg_replace("/\s+/", ' ', $mask);
	
	$mask = explode(' ', $mask);
	foreach($mask as $current_mask) {
		// Имеем дело с диапазоном IP
		if(strstr($current_mask, '-') !== false) {
			list($ip_start, $ip_end) = explode('-', $current_mask);
			if(ip_in_range($ip, $ip_start, $ip_end)) {
				return true;
			}
		// Одиночный IP, возможно с * 
		} else {
			if(ip_in_range($ip, $current_mask)) {
				return true;
			}
		}
	}
	
	return false;
}

/*
 * Преобразуем строковый IP в массив из 4-х элементов
 */
function ip2arr($ip) {
	if(empty($ip)) return array();
	$ip_arr = explode('.', $ip);
	return count($ip_arr) == 4 ? $ip_arr : array();
}

/*
 * Проверка принадлежности IP диапазону
 * Либо явно задан диапазон с дефисом, либо звёдочка
 */
function ip_in_range($ip, $ip_start, $ip_end = '') {

	// Обычный IP
	if(empty($ip_end) and strstr($ip_start, '*') === false) {
		return $ip == $ip_start;
		
	// Диапазон или маска со звёздочкой
	} else {
		$ip_arr = ip2arr($ip);
		$ip_start_arr = ip2arr($ip_start);
		
		// Диапазон
		if(!empty($ip_end)) {
			$ip_end_arr = ip2arr($ip_end);
		
		// Маска со звёздочкой
		} else {
			for ($i=0; $i<4; $i++) {
				if ($ip_start_arr[$i]=='*') {
					$ip_start_arr[$i]='0';
					$ip_end_arr[$i]='255';
				} else {
					$ip_end_arr[$i]=$ip_start_arr[$i]; 
				}
			}
		}
		
		$ip_num = ip2long($ip);
		return ($ip_num >= ip2long(join('.', $ip_start_arr)) && $ip_num <= ip2long(join('.', $ip_end_arr)));
	}
	return false;
}

// Проверка на повторый клик по IP
function ip_check_repeat_click($ip) {
    try {
        require_once _TRACK_SHOW_COMMON_PATH . "/DatabaseConnection.php";
        require_once _TRACK_LIB_PATH . '/mysql-backport/mysql.php';
        require_once _TRACK_LIB_PATH . '/php5-backport/string.php';

        $settings_file=_TRACK_SETTINGS_PATH.'/settings.php';
        $str=file_get_contents($settings_file);
        $str=str_replace('<?php exit(); ?>', '', $str);
        $arr_settings=unserialize($str);

        $_DB_LOGIN=$arr_settings['login'];
        $_DB_PASSWORD=$arr_settings['password'];
        $_DB_NAME=$arr_settings['dbname'];
        $_DB_HOST=$arr_settings['dbserver'];

        // Connect to DB

        mysql_connect($_DB_HOST, $_DB_LOGIN, $_DB_PASSWORD) or die("Could not connect: " .mysql_error());
        mysql_select_db($_DB_NAME);

        $sql = "select count(user_ip) as total from tbl_clicks where user_ip='" . $ip . "'";
        $result = mysql_fetch_assoc(mysql_query($sql));

        return (isset($result['total']) && $result['total'] > 0);
    } catch (Exception $e) {}

    return false;
}

// Провка на уникальность перехода по куке.
function check_unique_click($cookie_key) {
    $cookie_key = 'cpa_uniqueclick_' . $cookie_key;

    $result = isset($_COOKIE[$cookie_key]);

    $cookie_time = $_SERVER['REQUEST_TIME'] + (60 * 60 * 24 * 365);
    setcookie($cookie_key, 1, $cookie_time, "/", $_SERVER['HTTP_HOST']);

    if ($cookie_key != 'cpa_uniqueclick_all') {
        setcookie('cpa_uniqueclick_all', 1, $cookie_time, "/", $_SERVER['HTTP_HOST']);
    }

    return $result;
}

/**
 * Лог ошибок
 */
function track_error($error) {
	if($error == '') return false;
	/*
	Отключаем в угоду производительности
	https://uniquedesign.teamworkpm.net/tasks/4041672
	
	$log_dir = _CACHE_PATH . '/log';
	if(!is_dir($log_dir)) {
		mkdir ($log_dir);
		chmod ($log_dir, 0777);
	}
	
	$path = $log_dir . '/.' . date('Y-m-d') . '.txt';
	$fp = fopen($path, 'a');
	fwrite($fp, date("Y-m-d H:i:s") . ' ' . $error . "\n");
	fclose($fp);
	chmod($path, 0777);
	*/
}




/*
 * Получение переменной из POST|GET|REQUEST 
 *
 * @param string $name - имя переменной
 * @param string $type - p|g|r откуда получаем
 * @param int $num - ожидаемый тип данных: 0 - строка, 1 - целое число, 2 - целое положительное, 3 - json, 4 - date YYYY-MM-DD
 * @param mixed $df - значение по умолчанию
 * @return mised
 */
function rq($name, $num = 0, $df = null, $type = 'r') {
	global $_POST, $_GET, $_REQUEST;
	
	if ($type == 'r') {
		$d = &$_REQUEST;
	} elseif ($type == 'p') {
		$d = &$_POST;
	} elseif ($type == 'g') {
		$d = &$_GET;
	}

	if ($num == 0) {
		$def = ($df == null ? '' : $df);
		return array_key_exists($name, $d) ? $d[$name] : $def;
	} elseif($num < 3) {
		$def = ($df == null ? 0 : $df);
		$out = array_key_exists($name, $d) ? intval($d[$name]) : $def;
		return $num == 2 ? abs($out) : $out;
	} elseif($num == 4) {
		$def = ($df === null ? date('Y-m-d') : $df);
		if(array_key_exists($name, $d)) { 
			if(preg_match('/^\d{4}-\d{2}-\d{2}$/', $d[$name])) {
				return $d[$name];
			} elseif(preg_match('/^\d{2}\.\d{4}$/', $d[$name])) {
				$tmp = explode('.', $d[$name]);
				return date('Y-m-d', mktime(0, 0, 0, $tmp[0], 1, $tmp[1]));
			} else {
				return $def;
			}
		} else {
			return $def;
		}
	} else {
		return array_key_exists($name, $d) ? json_decode($d[$name], true) : array();
	}
	return false;
}

function stripslashes2($v) {
	if(is_array($v)) {
		$v = array_map('stripslashes2', $v);
	} else {
		$v = stripslashes($v);
	}
	return $v;
}

/**
 * Отладочная информация
 */	
function dmp(&$v) {
	echo '<pre>'.print_r($v, true).'</pre>';
}


function onlyword($v) {
	return preg_replace("/[^a-zA-Z0-9_-]/u", '', $v);
}
?>
