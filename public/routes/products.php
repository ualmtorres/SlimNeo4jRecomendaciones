<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Laudis\Neo4j\Databags\Statement;


// Endpoint para crear un nuevo producto
$app->post($prefix . '/products', function (RequestInterface $request, ResponseInterface $response) use ($client) {
    $data = $request->getParsedBody() ?? [];

    // Validar los datos de entrada
    if (!array_key_exists('name', $data) || !array_key_exists('description', $data) || !array_key_exists('price', $data) || !array_key_exists('categoryId', $data)) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid input'
        ]);
    }

    $name = $data['name'];
    $description = $data['description'];
    $price = $data['price'];
    $categoryId = $data['categoryId'];

    // Crear un nuevo nodo de producto en la base de datos
    $statement = new Statement('CREATE (p:Product {name: $name, description: $description, price: $price, categoryId: $categoryId}) RETURN p', $data);
    $result = $client->runStatement($statement);

    // Obtener el ID del nuevo producto
    $result = $result->first()->get('p')->getId();

    // Devolver la respuesta con el ID del nuevo producto
    return createJsonResponse($response->withStatus(201), [
        'status' => 201,
        'message' => 'Product created',
        'productId' => $result
    ]);
});

// Endpoint para obtener todos los productos
$app->get($prefix . '/products', function (RequestInterface $request, ResponseInterface $response) use ($client) {
    // Obtener todos los nodos de productos de la base de datos
    $statement = new Statement('MATCH (p:Product) RETURN p', []);
    $result = $client->runStatement($statement);

    // Preparar la lista de productos
    $products = [];
    foreach ($result as $product) {
        $node = $product->get('p');
        $products[] = [
            'id' => $node->getId(),
            'name' => $node->getProperty('name'),
            'description' => $node->getProperty('description'),
            'price' => $node->getProperty('price'),
            'categoryId' => $node->getProperty('categoryId')
        ];
    }

    // Devolver la respuesta con la lista de productos
    return createJsonResponse($response, [
        'status' => 200,
        'products' => $products
    ]);
});

// Endpoint para obtener un producto por ID
$app->get($prefix . '/products/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($client) {
    $id = $args['id'];

    // Obtener el nodo de producto de la base de datos
    $statement = new Statement('MATCH (p:Product) WHERE id(p) = $id RETURN p', ['id' => (int) $id]);
    $result = $client->runStatement($statement);

    // Verificar si el producto existe
    if ($result->count() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'Product not found'
        ]);
    }

    // Obtener los datos del producto
    $node = $result->first()->get('p');
    $product = [
        'id' => $node->getId(),
        'name' => $node->getProperty('name'),
        'description' => $node->getProperty('description'),
        'price' => $node->getProperty('price'),
        'categoryId' => $node->getProperty('categoryId')
    ];

    // Devolver la respuesta con los datos del producto
    return createJsonResponse($response, [
        'status' => 200,
        'product' => $product
    ]);
});

// Endpoint para actualizar un producto por ID
$app->put($prefix . '/products/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($client) {
    $id = $args['id'];
    $data = $request->getParsedBody() ?? [];

    // Validar los datos de entrada
    if (!array_key_exists('name', $data) || !array_key_exists('description', $data) || !array_key_exists('price', $data) || !array_key_exists('categoryId', $data)) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid input'
        ]);
    }

    $data['id'] = (int) $id;
    $name = $data['name'];
    $description = $data['description'];
    $price = $data['price'];
    $categoryId = $data['categoryId'];

    // Actualizar el nodo de producto en la base de datos
    $statement = new Statement('MATCH (p:Product) WHERE id(p) = $id SET p.name = $name, p.description = $description, p.price = $price, p.categoryId = $categoryId RETURN p', $data);
    $result = $client->runStatement($statement);

    // Verificar si el producto existe
    if ($result->count() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'Product not found'
        ]);
    }

    // Devolver la respuesta con el mensaje de éxito
    return createJsonResponse($response, [
        'status' => 200,
        'message' => 'Product updated'
    ]);
});
