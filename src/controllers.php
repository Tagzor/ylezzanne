<?php

// define controllers for a twig
$twig = $app['controllers_factory'];
$twig->get('/{name}', function ($name) use ($app) {
    return $app['twig']->render('index.twig', array(
        'name' => $name,
    ));
});


// define controllers for a user
$user = $app['controllers_factory'];
$user->get('/{name}', function ($name) use ($app) {
    return $app['twig']->render('user.twig', array(
        'name' => $name,
    ));
}); 
	
// define controllers for a game
$game = $app['controllers_factory'];
$game->get('/', function () {
	return 'game list';
});

// define controllers for a game
$statistics = $app['controllers_factory'];
$statistics->get('/db/', function() use($app) {
	$st = $app['pdo']->prepare('SELECT name FROM test_table');
	$st->execute();

	$names = array();
	while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
		$app['monolog']->addDebug('Row ' . $row['name']);
		$names[] = $row;
	}

	return $app['twig']->render('database.twig', array(
			'names' => $names
	));
});

$app->mount('/twig', $twig);
$app->mount('/user', $user);
$app->mount('/game', $game);
$app->mount('/db', $statistics);


// define "global" controllers
$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('login.twig');
});

?>