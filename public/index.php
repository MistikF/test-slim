<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Faker\Factory;
use DI\Container;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
AppFactory::setContainer($container);

$container->set('view', function() {
    return Twig::create(__DIR__ . '/../templates');
});

$app = AppFactory::create();

$app->add(TwigMiddleware::createFromContainer($app));

$view = $container->get('view');

$app->get('/', function (Request $request, Response $response) {
    return $response->withHeader('Location', '/welcome')->withStatus(302);
});

$app->get('/welcome', function (Request $request, Response $response, $args) use ($container) {
    $view = $container->get('view');
    return $view->render($response, 'welcome.phtml');
});

$app->get('/api/users', function (Request $request, Response $response, $args) {
    $faker = Factory::create();
    $limit = $request->getQueryParams()['limit'] ?? 5;
    $limit = min($limit, 10);
    $users = [];
    for ($i = 0; $i < $limit; $i++) {
        $users[] = [
            'id' => $i + 1,
            'name' => $faker->name,
            'email' => $faker->email,
            'image' => $faker->imageUrl
        ];
    }
    $response->getBody()->write(json_encode($users));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/api/users/{userId}', function (Request $request, Response $response, $args) {
    $faker = Factory::create();
    $user = [
        'id' => $args['userId'],
        'name' => $faker->name,
        'email' => $faker->email,
        'image' => $faker->imageUrl
    ];
    $response->getBody()->write(json_encode($user));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

$app->delete('/api/users/{userId}', function (Request $request, Response $response, $args) {
    return $response->withStatus(204);
});

$app->get('/users', function (Request $request, Response $response, $args) use ($container) {
    $faker = Factory::create();
    $limit = $request->getQueryParams()['limit'] ?? 5;
    $limit = min($limit, 10);
    $users = [];
    for ($i = 0; $i < $limit; $i++) {
        $users[] = [
            'id' => $i + 1,
            'name' => $faker->name,
            'email' => $faker->email,
            'image' => $faker->imageUrl
        ];
    }

    $view = $container->get('view');
    return $view->render($response, 'users.phtml', ['users' => $users]);
});

$app->get('/users/{userId}', function (Request $request, Response $response, $args) use ($container) {
    $faker = Factory::create();
    $user = [
        'id' => $args['userId'],
        'name' => $faker->name,
        'email' => $faker->email,
        'image' => $faker->imageUrl
    ];
    $view = $container->get('view');
    return $view->render($response, 'user.phtml', ['user' => $user]);
});

$app->run();
