<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// define controllers for a twig
$twig = $app ['controllers_factory'];
$twig->get ( '/{name}', function ($name) use($app) {
	return $app ['twig']->render ( 'index.twig', array (
			'name' => $name 
	) );
} );

// define controllers for a user
$user = $app ['controllers_factory'];
$user->get ( '/{name}', function ($name) use($app) {
	return $app ['twig']->render ( 'user.twig', array (
			'name' => $name 
	) );
} );

// define controllers for a game
$game = $app ['controllers_factory'];
$game->get ( '/', function () {
	return 'game list';
} );

// define controllers for a game
$statistics = $app ['controllers_factory'];
$statistics->get ( '/db/', function () use($app) {
	$st = $app ['pdo']->prepare ( 'SELECT name FROM test_table' );
	$st->execute ();
	
	$names = array ();
	while ( $row = $st->fetch ( PDO::FETCH_ASSOC ) ) {
		$app ['monolog']->addDebug ( 'Row ' . $row ['name'] );
		$names [] = $row;
	}
	
	return $app ['twig']->render ( 'database.twig', array (
			'names' => $names 
	) );
} );

$app->mount ( '/twig', $twig );
$app->mount ( '/user', $user );
$app->mount ( '/game', $game );
$app->mount ( '/db', $statistics );

// define "global" controllers
$app->get ( '/login', function (Request $request) use($app) {
	return $app ['twig']->render ( 'login.twig', array (
			'error' => $app ['security.last_error'] ( $request ),
			'last_username' => $app ['session']->get ( '_security.last_username' ) 
	) );
} );

$app->get ( '/', function () use($app) {
	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
		return $app ['twig']->render ( 'user.twig', array (
				'name' => $user->getUsername () 
		) );
	}
	$app ['monolog']->addDebug ( 'logging output.' );
	return $app ['twig']->render ( 'login.twig' );
} );

$app->get ( '/eid', function () use($app) {
	ini_set('display_errors', 'stderr');
	mb_internal_encoding("UTF-8");
	error_reporting(E_ALL);// | E_STRICT
	
	require_once("openid.ee-authentication.php");
	
	if (isset ( $msg ))
		echo "<div class=\"alert\">$msg</div>";
	if (isset ( $error ))
		echo "<div class=\"error\">$error</div>";
	if (isset ( $success ))
		echo "<div class=\"success\">$success</div>";
		
		// Vaatame, mis muutujad olemas on ...
	if (! empty ( $_SESSION )) {
		echo "<h2>Massiivi \$_SESSION sisu:</h2>";
		echo "<div class='alert'>";
		foreach ( $_SESSION as $k => $v )
			echo "$k = $v <br />";
		echo "</div>";
	}
	
	if (isset ( $_GET ["action"] ) and $_GET ["action"] == "finishAuth") {
		echo "<h2>OpenID teegi töö tulemus:</h2>";
		echo "<div class='alert'>";
		foreach ( $_GET as $k => $v )
			echo "$k = $v <br />";
		echo "</div>";
	}
	$app ['monolog']->addDebug ( 'logging output.' );
	return $app ['twig']->render ( 'login.twig' );
} );

?>
