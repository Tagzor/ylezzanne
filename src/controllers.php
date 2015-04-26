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
				'error' => 'Tegevus pole lubatud!' 
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

$game->get ( '/{id}/{score}', function ($id, $score) use($app) {
	$gameDAO = new Ylezzanne\Dao\GameDAO ( $app ['pdo'] );
	$game = $gameDAO->find ( $id );
	$topScores = $gameDAO->getTopScores ( $id );
	$games = $gameDAO->findAll ();

	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
		$stats = $gameDAO->getStatistics ( $id, $user->getUsername () );
	}

	$gameDAO->saveScore( $user->getId (), $game->getId (), $score);
	
	return $app->redirect('db/game/'.$id, 303);
} );

$game->get ( '/snake', function () use($app) {
	$gameDAO = new Ylezzanne\Dao\GameDAO ( $app ['pdo'] );
	$games = $gameDAO->findAll ();
	$game = $gameDAO->find ( 3 );
	$topScores = $gameDAO->getTopScores ( 3 );
	
	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
		$stats = $gameDAO->getStatistics ( $id, $user->getUsername () );
	}

	return $app ['twig']->render ( 'snake.twig', array (
			'name' => $user->getUsername (),
			'game' => $game,
			'topScores' => $topScores,
			'games' => $games
	) );
} );

$game->get ( '/cointoss', function () use($app) {
	$gameDAO = new Ylezzanne\Dao\GameDAO ( $app ['pdo'] );
	$games = $gameDAO->findAll ();

	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
		$stats = $gameDAO->getStatistics ( $id, $user->getUsername () );
	}

	return $app ['twig']->render ( 'cointoss.twig', array (
			'name' => $user->getUsername (),
			'games' => $games
	) );
} );
	
$game->post ( '/cointoss', function (Request $request) use($app) {
	$gameDAO = new Ylezzanne\Dao\GameDAO ( $app ['pdo'] );
	$games = $gameDAO->findAll ();

	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
		$stats = $gameDAO->getStatistics ( $id, $user->getUsername () );
	}
			
	$valik = $request->request->get('valik', 'fail');
	echo ($valik);
	
	$result = Rand ( 1, 2 );
	echo ($result);
	
	if ($result == 1 and $valik == 'Kull') {
		$skoor = ( int ) file_get_contents ( __DIR__ .$user->getId (). 'cointoss.txt' ) + 1;
		file_put_contents ( __DIR__ .$user->getId (). 'cointoss.txt', ( string ) $skoor );
	} elseif ($result == 2 and $valik == 'Kiri') {
		$skoor = ( int ) file_get_contents ( __DIR__ .$user->getId (). 'cointoss.txt' ) + 1;
		file_put_contents ( __DIR__ .$user->getId (). 'cointoss.txt', ( string ) $skoor );
	} else {
		
		$score = ( int ) file_get_contents ( __DIR__ .$user->getId (). 'cointoss.txt' );
		$topScores = $gameDAO->getTopScores ( 2 );
		$game = $gameDAO->find ( 2 );
		
		$gameDAO->saveScore( $user->getId (), $game->getId (), $skoor);
		$skoor = 0;
		file_put_contents ( __DIR__ .$user->getId (). 'cointoss.txt', ( string ) $skoor);
	    
		return $app->redirect('/game/2/'.$score);
	}
	
	return $app ['twig']->render ( 'cointoss.twig', array (
			'name' => $user->getUsername (),
			'games' => $games,
			'score' => $skoor
			
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

$statistics->get ( '/', function () use($app) {
	$gameDAO = new Ylezzanne\Dao\GameDAO ( $app ['pdo'] );
	$games = $gameDAO->findAll ();

	$token = $app ['security']->getToken ();
	if (null !== $token) {
		$user = $token->getUser ();
		$stats = $gameDAO->getStatistics ( $id, $user->getUsername () );
	}

	return $app ['twig']->render ( 'games.twig', array (
			'name' => $user->getUsername (),
			'games' => $games
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
		$aaData[] = array(
				"time" => date("d-m-Y", $row['time']),
				"score" => $row['score'],
		);
	}
	
	$output = array(
			"recordsTotal" => count($stats),
			"data" => $aaData,
	);
	
	return $app->json($output);
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
			$message = 'Kasutaja ' . $user->getUsername () . ' on Ylezzanne keskonnas aktiveeritud!.';
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
