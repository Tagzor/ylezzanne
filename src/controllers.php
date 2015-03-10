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

$user->get ( '/', function () use($app) {
	echo "load users! ";
	
	$st = $app ['pdo']->prepare('SELECT u.*  FROM users u');
	$st->execute();
	 
	$usersData = $st->fetchAll ();
	if (empty ( $usersData )) {
		echo "no user stored ";
	} else {
	    echo "dbusername:" . $usersData [0]->getUsername();
	}
	
	return $app ['twig']->render ( 'user.twig', array (
		'name' => $usersData [0]->getUsername(),
	) );
} );
	
	
// define controllers for a game
$game = $app ['controllers_factory'];
$game->get ( '/', function () {
	return 'game list';
} );

// define controllers for a game
$statistics = $app ['controllers_factory'];
$statistics->get ( '/', function () use($app) {
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

$app->get ( '/tbd/{name}', function ($name) use($app) {
	return $app ['twig']->render ( 'tbd.twig', array (
			'name' => $name 
	) );
} );

$app->get ( '/eid', function () use($app) {
	return $app ['twig']->render ( 'eid.twig', array (
			'name' => $name 
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

?>
