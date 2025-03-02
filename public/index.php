<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Factory\AppFactory;
use \Laudis\Neo4j\ClientBuilder;
use \Laudis\Neo4j\Authentication\Authenticate;

// Crear la aplicación
$app = AppFactory::create();

// Conectar a Neo4j
$servername = "neo4j";
$username = "neo4j";
$password = "mypassword";
$database = "recommendations";

$auth = Authenticate::basic($username, $password);

$client = ClientBuilder::create()
    ->withDriver('bolt', "bolt://$username:$password@$servername")
    ->withDriver('neo4j', "neo4j://$servername:7687", $auth)
    ->withDefaultDriver('neo4j')
    ->build();

// Configurar el prefijo para las rutas de la API
$prefix = '/api';

// Configurar Slim para procesar datos JSON
$app->addBodyParsingMiddleware();

// Función auxiliar para manejar la respuesta
function createJsonResponse(ResponseInterface $response, array $data): ResponseInterface
{
    // Establecer el tipo de contenido de la respuesta
    $response = $response->withHeader('Content-Type', 'application/json; charset=utf-8');

    // Escribir la respuesta
    $response->getBody()->write(json_encode($data));

    // Devolver la respuesta
    return $response;
}

// Definir las rutas de la aplicación
$app->get("/", function (RequestInterface $request, ResponseInterface $response, array $args) {
    echo file_get_contents('./index.html');

    return $response;
});

// Endpoint de prueba
$app->get($prefix . '/test', function (RequestInterface $request, ResponseInterface $response) {
    $data = [
        'status' => 200,
        'message' => 'API is working'
    ];

    return createJsonResponse($response, $data);
});

require __DIR__ . '/routes/users.php';
require __DIR__ . '/routes/products.php';
require __DIR__ . '/routes/interactions.php';
require __DIR__ . '/routes/recommendations.php';

// Interceptar todas las rutas no definidas
$app->any('{routes:.+}', function (RequestInterface $request, ResponseInterface $response) {
    $data = [
        'status' => 404,
        'message' => 'Route not found'
    ];
    return createJsonResponse($response->withStatus(404), $data);
});

// Ejecutar la aplicación
$app->run();
