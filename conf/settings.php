<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
date_default_timezone_set('Europe/Moscow');
header('Content-type: text/html; charset=cp1251');

$time = 60*60*1;
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $time) . ' GMT');
header("Cache-Control: private, max-age=$time");