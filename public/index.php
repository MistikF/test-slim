<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Faker\Factory;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// GET маршрут
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write('<a href="/hello/world">Try /hello/world</a>');
    return $response;
});

// POST маршрут
$app->post('/', function (Request $request, Response $response) {
    $data = $request->getParsedBody();

    // Check if the data is an array
    if (!is_array($data)) {
        $data = [];
    }

    // Create an instance of Faker
    $faker = Factory::create();

    // Generate fake data
    $fakeData = [
        'name' => $faker->name,
        'email' => $faker->email,
        'address' => $faker->address,
    ];

    // Merge the data
    $data = array_merge($data, $fakeData);

    // Add more fake data
    for ($i = 0; $i < 10; $i++) {
        $data[] = [
            'name' => $faker->name,
            'email' => $faker->email,
            'address' => $faker->address,
        ];
    }

    // Encode the data as JSON
    $response->getBody()->write(json_encode($data));

    return $response;
});


// DELETE маршрут
$app->delete('/', function (Request $request, Response $response) {
    $response->getBody()->write('<h1>DELETE request</h1>');
    return $response;
});

// GET with dynamic route parameter
$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

// GET with template rendering
$app->get('/about', function ($request, $response) {
    $phpView = new PhpRenderer('../templates');
    return $phpView->render($response, 'about.phtml');
});

$app->run();
