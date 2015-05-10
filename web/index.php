<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();

ini_set('display_errors', 0);
mb_internal_encoding("UTF-8");
error_reporting(E_ALL);// | E_STRICT

define('YLEZZANNE_PUBLIC_ROOT', __DIR__);

require_once __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../src/controllers.php';

require_once __DIR__.'/../src/Auth/openid.ee-authentication.php';

$app->run();

?>
