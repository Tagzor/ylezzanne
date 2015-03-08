<?php
use Silex\Application;
use Silex\Provider;
//
// Application setup
//
$app = new Application ();
$app ['debug'] = true;
// Register the monolog logging service
$app->register ( new Silex\Provider\MonologServiceProvider (), array (
		'monolog.logfile' => 'php://stderr' 
) );
// Register the Twig templating engine
$app->register ( new Silex\Provider\TwigServiceProvider (), array (
		'twig.path' => __DIR__ . '/../views' 
) );
// PDO connection
$dbopts = parse_url ( getenv ( 'DATABASE_URL' ) );
$app->register ( new Herrera\Pdo\PdoServiceProvider (), array (
		'pdo.dsn' => 'pgsql:dbname=' . ltrim ( $dbopts ["path"], '/' ) . ';host=' . $dbopts ["host"],
		'pdo.port' => $dbopts ["port"],
		'pdo.username' => $dbopts ["user"],
		'pdo.password' => $dbopts ["pass"] 
) );

return $app;

?>