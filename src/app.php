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

$app->register(new Provider\UrlGeneratorServiceProvider());

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
$app['pdo']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Register the SessionProvider.
$app->register(new Silex\Provider\SessionServiceProvider());



// Register the SecurityServiceProvider.
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
		'security.firewalls' => array(
				'login' => array(
					'pattern' => '^/login$',
				),
				'secured' => array(
						'pattern' => '^.*$',
						'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
						'logout' => array(
							'logout_path' => '/logout',
						),
//  						'users' => array(
//  				            'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
//  				        ),
						'users' => $app->share(function() use ($app) {
							return new Ylezzanne\Dao\UserDAO($app['pdo'], $app['security.encoder.digest']);
						}),
// 						'users' => $app->share(function() use ($app) {
// 							return new Ylezzanne\Dao\UserProvider($app['pdo']);
// 						}),
				),
		),
));
return $app;

?>