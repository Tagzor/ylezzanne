<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);// | E_STRICT

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/Auth/openid.ee-authentication.php';

$app = require __DIR__.'/../src/app.php';

require __DIR__.'/../src/controllers.php';

$app->run();

?>
