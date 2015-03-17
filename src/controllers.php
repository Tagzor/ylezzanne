<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\User;
use Ylezzanne\Dao\GameDAO;

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
	$stmt = $app ['pdo']->prepare("SELECT u.* FROM users u WHERE u.username = :name");
	//$stmt->bindValue(':name', $name, PDO::PARAM_STR);
	$stmt->execute(array(':name' => $name));
	
	$usersData = array();
	while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
		$app ['monolog']->addDebug ( 'Row ' . $row ['username'] );
		$app ['monolog']->addDebug ( 'Row ' . $row ['password'] );
	
		$user =  new User($row['id'],$row['username'], $row['password'], explode(',', $row['role']), true, true, true, true);
	
		array_push($usersData, $user);
	
		$salt = uniqid(mt_rand());
		$password = $app['security.encoder.digest']->encodePassword('foo', $salt);
	
		$app ['monolog']->addDebug ( 'RowSalt ' . $salt );
		$app ['monolog']->addDebug ( 'RowPAssword ' . $password );
	
	
		print count($usersData );
	}
	
	if (empty ( $usersData )) {
		echo "no user stored ";
		return $app ['twig']->render ( 'user.twig', array (
				'name' => 'no data stored name',
		) );
		
	} else {
		echo $usersData;
	}
	//PHP Catchable fatal error:  Object of class Ylezzanne\Dao\User could not be converted to string
	//print join(",", $usersData);
	$winner = $usersData[rand(0,count($usersData)-1)];
	
	return $app ['twig']->render ( 'user.twig', array (
			'name' => $winner->getUsername(),
	) );
} );

$user->get ( '/', function () use($app) {
	echo "load users! ";
	
	$st = $app ['pdo']->prepare('SELECT u.*  FROM users u');
	$st->execute();
	
	$usersData = array();
	while ( $row = $st->fetch ( PDO::FETCH_ASSOC ) ) {
		$app ['monolog']->addDebug ( 'Row ' . $row ['username'] );
		$app ['monolog']->addDebug ( 'Row ' . $row ['password'] );
		
		$user = buildUser ( $row );
		
		array_push($usersData, $user);
		
		$salt = uniqid(mt_rand());
		$password = $app['security.encoder.digest']->encodePassword('foo', $salt);
		
		$app ['monolog']->addDebug ( 'RowSalt ' . $salt );
		$app ['monolog']->addDebug ( 'RowPAssword ' . $password );
		
		
        print count($usersData ); 
	}
	
	if (empty ( $usersData )) {
		echo "no user stored ";
	} else {
		echo $usersData;	
	}
	//PHP Catchable fatal error:  Object of class Ylezzanne\Dao\User could not be converted to string
	//print join(",", $usersData);
	$winner = $usersData[rand(0,count($usersData)-1)];
	
	return $app ['twig']->render ( 'user.twig', array (
		'name' => $winner->getUsername(),
	) );
} );
	
	
// define controllers for a game
$game = $app ['controllers_factory'];
$game->get ( '/{id}', function ($id) use($app) {
	$gameDAO = new Ylezzanne\Dao\GameDAO($app['pdo']);
	$game = $gameDAO->find($id);
	$topScores = $gameDAO->getTopScores($id);
	$games = $gameDAO->findAll();
	
	return $app ['twig']->render ( 'game.twig', array (
			'game' => $game,
			'topScores' => $topScores,
			'games' => $games
	) );
} );

$game->get ( '/', function () use($app) {
	$gameDAO = new Ylezzanne\Dao\GameDAO($app['pdo']);
	$games = $gameDAO->findAll();
	return $app ['twig']->render ( 'games.twig', array (
			'games' => $games 
	) );
} );
	
// define controllers for a game statistics
$statistics = $app ['controllers_factory'];
$statistics->get ( '/game/{id}', function ($id) use($app) {
	$gameDAO = new Ylezzanne\Dao\GameDAO($app['pdo']);
	$games = $gameDAO->findAll();
	
	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
		$stats = $gameDAO->getStatistics($id, $user->getUsername());
	}
	
	return $app ['twig']->render ( 'statistics.twig', array (
			'games' => $games,
			'statistics' => $stats 
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
