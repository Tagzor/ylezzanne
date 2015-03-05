<?php


require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register the Twig templating engine
$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path' => __DIR__.'/../views',
));

// Our web handlers
$app->get('/hello/{name}', function($name) use($app) { 
    return 'Hello '.$app->escape($name); 
}); 

$app->get('/twig/{name}', function ($name) use ($app) {
    return $app['twig']->render('index.twig', array(
        'name' => $name,
    ));
});

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return 'Hello world';
});

$app->run();

?>
