<?php

define('_DIR_TRACK_', 'track');
define('_DIR_TRACK_COMMON_', 'track-common');
define('_DIR_TRACK_SHOW_', 'track-show');

define('_TRACK_VER',           'v1.6');

define('_GLOBAL_CACHE_', dirname (__FILE__) . '/../../_cache');

define('_TRACK_STATIC_PATH',   dirname (__FILE__) . '/../' . _DIR_TRACK_COMMON_ .'/static');
define('_TRACK_SETTINGS_PATH', _GLOBAL_CACHE_);

define('_CACHE_PATH',          _GLOBAL_CACHE_);
define('_CACHE_COMMON_PATH',   _GLOBAL_CACHE_);
define('_TRACK_PATH',          dirname (__FILE__) . '/../' . _DIR_TRACK_COMMON_ . '/' . _TRACK_VER . '/track');
define('_TRACK_SHOW_PATH',     dirname (__FILE__));
define('_TRACK_SHOW_COMMON_PATH', dirname (__FILE__) . '/../' . _DIR_TRACK_COMMON_ . '/' . _TRACK_VER . '/track-show');
define('_TRACK_LIB_PATH',      _TRACK_PATH . '/lib');
define('_ENABLE_DEBUG',        true);
define('_CLICKS_SPOT_SIZE',    500);

define('_HTML_LIB_PATH',       '../' . _DIR_TRACK_COMMON_ . '/' . _TRACK_VER . '/track-show/lib');
define('_HTML_TEMPLATE_PATH',  '../' . _DIR_TRACK_COMMON_ . '/' . _TRACK_VER . '/track-show/templates');

// Get full HTML url
$s = (empty($_SERVER["HTTPS"]) && empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) ? '' : ((!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") || $_SERVER['HTTP_X_FORWARDED_PROTO']=='https' ) ? "s" : "";
$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")) . $s;
$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
$port = '';
$uri = $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
$segments = explode('?', $uri, 2);
$url = $segments[0];

define('_HTML_ROOT_PATH',      rtrim(str_replace (end(explode('/', $_SERVER['PHP_SELF'])), '', $url), '/'));
define('_HTML_TRACK_PATH',     strrev(preg_replace(strrev('/' . _DIR_TRACK_SHOW_ . '/'), strrev(_DIR_TRACK_), strrev(_HTML_ROOT_PATH), 1)));

define('_TRACK_MASTER_PATH',   dirname (__FILE__) . '/../' . _DIR_TRACK_);

// Вспомогательные трекеры
$tracklist = array(
    /*
    array(
        'path' =>  dirname (__FILE__) . '/../track_slave', // Может быть 'http://www.ПУТЬ_К_ТРЕКЕРУ.ru/track',
        'key'  => 'key123'
    ),
    */
);
