<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\User;
use Ylezzanne\Dao\GameDAO;

// define controllers for a user
$user = $app ['controllers_factory'];
$user->get ( '/{name}', function ($name) use($app) {
		
	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
	}

	if ($user->getUsername () !== $name) {
		return $app ['twig']->render ( 'user.twig', array (
				'error' => 'Access denied!' 
		) );
	} 
	
	$gameDAO = new Ylezzanne\Dao\GameDAO ( $app ['pdo'] );
	$topGames = $gameDAO->topGames();
	
	return $app ['twig']->render ('user.twig', array (
			'name' => $user->getUsername (),
			'user' => $user,
			'topGames' => $topGames
	) );
	
} );

$user->get ( '/', function () use($app) {
	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
	}
	
	$gameDAO = new Ylezzanne\Dao\GameDAO ( $app ['pdo'] );
	$topGames = $gameDAO->topGames();
	
	return $app ['twig']->render ('user.twig', array (
			'name' => $user->getUsername (),
			'user' => $user,
			'topGames' => $topGames
	) );
} );

// define controllers for a game
$game = $app ['controllers_factory'];


$game->get ( '/cointoss', function () use($app) {
	$gameDAO = new Ylezzanne\Dao\GameDAO ( $app ['pdo'] );
	$games = $gameDAO->findAll ();

	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
		$stats = $gameDAO->getStatistics ( $id, $user->getUsername () );
	}

	return $app ['twig']->render ( 'gamecoin.twig', array (
			'name' => $user->getUsername (),
			'games' => $games
	) );
} );

$game->post ( '/cointoss', function () use($app) {
	$gameDAO = new Ylezzanne\Dao\GameDAO ( $app ['pdo'] );
	$games = $gameDAO->findAll ();

	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
		$stats = $gameDAO->getStatistics ( $id, $user->getUsername () );
	}

	$valik = $app ['session']->get ( 'valik' );
	echo ($valik);
	
	$score = cointoss($valik, 10);
	
	echo ($score);
	
	return $app ['twig']->render ( 'gamecoin.twig', array (
			'name' => $user->getUsername (),
			'games' => $games
	) );
} );

$game->get ( '/{id}', function ($id) use($app) {
	$gameDAO = new Ylezzanne\Dao\GameDAO ( $app ['pdo'] );
	$game = $gameDAO->find ( $id );
	$topScores = $gameDAO->getTopScores ( $id );
	$games = $gameDAO->findAll ();
	
	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
		$stats = $gameDAO->getStatistics ( $id, $user->getUsername () );
	}
	
	return $app ['twig']->render ( 'game.twig', array (
			'name' => $user->getUsername (),
			'game' => $game,
			'topScores' => $topScores,
			'games' => $games 
	) );
} );

$game->get ( '/', function () use($app) {
	$gameDAO = new Ylezzanne\Dao\GameDAO ( $app ['pdo'] );
	$games = $gameDAO->findAll ();
	
	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
		$stats = $gameDAO->getStatistics ( $id, $user->getUsername () );
	}

	if ($_POST['valik']) { 
    cointoss($_POST['valik'], (int)file_get_contents(__DIR__.'cointoss.txt'));
} 

	return $app ['twig']->render ( 'gamecoin.twig', array (
			'name' => $user->getUsername (),
			'games' => $games ,
			'skoor'=> (int)file_get_contents(__DIR__.'cointoss.txt')
	) );
} );


// define controllers for a game statistics
$statistics = $app ['controllers_factory'];
$statistics->get ( '/game/{id}', function ($id) use($app) {
	$gameDAO = new Ylezzanne\Dao\GameDAO ( $app ['pdo'] );
	$games = $gameDAO->findAll ();
	$game = $gameDAO->find($id);
	
	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
		$stats = $gameDAO->getStatistics ( $id, $user->getUsername () );
	}
	
	return $app ['twig']->render ( 'statistics.twig', array (
			'name' => $user->getUsername (),
			'games' => $games,
			'game' => $game,
			'statistics' => $stats 
	) );
} );

$ajax = $app ['controllers_factory'];
$ajax->get ( '/game/{id}', function ($id) use($app) {
	$gameDAO = new Ylezzanne\Dao\GameDAO ( $app ['pdo'] );
	$games = $gameDAO->findAll ();
	
	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
		$stats = $gameDAO->getStatistics ( $id, $user->getUsername () );
	}
	
	$aaData = array();
	foreach ($stats as $row)
	{

		echo $row['time'];
		echo (' score: ');
		echo $row['score'];
		echo ('</br>');
		$aaData[] = array(
				"time" => $row['time'],
				"score" => $row['score'],
		);
	}
	
	$output = array(
			"recordsTotal" => count($stats),
			"data" => $aaData,
	);
	
	return $app->json($output);
	//return json_encode($output);
} );
	
$app->mount ( '/user', $user );
$app->mount ( '/game', $game );
$app->mount ( '/db', $statistics );
$app->mount ( '/ajax', $ajax );

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

$app->get ( '/login-openId', function () use($app) {
	
	// Vaatame, mis muutujad olemas on ...
	if (! empty ( $_SESSION )) {
		
		foreach ( $_SESSION as $k => $v ) {
			if (strcasecmp ( $k, 'openid' ) == 0) {
				$openid = $v;
			}
			if (strcasecmp ( $k, 'email' ) == 0) {
				$email = $v;
				$password = $v;
			}
			if (strcasecmp ( $k, 'fullname' ) == 0) {
				$fullname = $v;
			}
			
			if (strcasecmp ( $k, 'gender' ) == 0) {
				$gender = $v;
			}
			if (strcasecmp ( $k, 'dob' ) == 0) {
				$dob = $v;
			}
		}
		
		if (null !== $openid) {
			$userDAO = new Ylezzanne\Dao\UserDAO ( $app ['pdo'], $app ['security.encoder.digest'] );
			$user = new \Ylezzanne\Dao\User ();
			$user->setPassword ( 'ylezzanne' );
			$name = explode(" ", $fullname);
			$user->setUsername($name[0]);
			if (null !== $email) {
				$user->setMail ( $email );
			} else {
				$user->setMail($name[0].".".$name[1]."@eesti.ee");
			}
						
			$userDAO->save ( $user );
			$message = 'The user ' . $user->getUsername () . ' has been saved.';
			$app ['session']->getFlashBag ()->add ( 'success', $message );
			
			return $app ['twig']->render ( 'openId.twig', array (
					'error' => $name,
					'msg' => $message,
					'success' => $name,
					'gender' => $gender,
					'name' => $name,
					'openid' => $openid,
					'email' => $email,
					'password' => $password,
					'fullname' => $fullname,
					'dob' => $dob,
					'user' => $user
			) );
		}
		
	}
	
	$app ['monolog']->addDebug ( 'logging output.' );
	return $app ['twig']->render ( 'login.twig', array (
					'error' => 'Isikuandmeid ei leitud!'));
} );

$app->get ( '/', function () use($app) {
	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
		
		$gameDAO = new Ylezzanne\Dao\GameDAO ( $app ['pdo'] );
		$topGames = $gameDAO->topGames();
		
		return $app ['twig']->render ('user.twig', array (
				'name' => $user->getUsername (),
				'user' => $user,
				'topGames' => $topGames  
		) );
	}
	$app ['monolog']->addDebug ( 'logging output.' );
	return $app ['twig']->render ( 'login.twig' );
} );


// Register the error handler.
$app->error(function (\Exception $e, $code) use ($app) {
	if ($app['debug']) {
		return;
	}
	switch ($code) {
		case 404:
			$message = 'The requested page could not be found.';
			break;
		default:
			$message = 'We are sorry, but something went terribly wrong.';
	}
	return new Response($message, $code);
});
	
	// lisab skoorile +1 kui 6igesti ja paneb nulli kui valesti
function cointoss($sisse, $skoor) {
	$result = Rand ( 1, 2 );
	if ($result == 1 and $sisse == 'Kull') {
		$skoor = ( int ) file_get_contents ( __DIR__ . 'cointoss.txt' ) + 1;
		file_put_contents ( __DIR__ . 'cointoss.txt', ( string ) $skoor );
	} elseif ($result == 2 and $sisse == 'Kiri') {
		$skoor = ( int ) file_get_contents ( __DIR__ . 'cointoss.txt' ) + 1;
		return file_put_contents ( __DIR__ . 'cointoss.txt', ( string ) $skoor );
	} else {
		print "Sinu skoor on: " . ( int ) file_get_contents ( __DIR__ . 'cointoss.txt' );
		$skoor = 0;
		return file_put_contents ( __DIR__ . 'cointoss.txt', ( string ) $skoor );
	}
}
	
?>
