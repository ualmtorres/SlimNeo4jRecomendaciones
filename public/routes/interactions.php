<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Laudis\Neo4j\Databags\Statement;


// Endpoint para registrar que un usuario ha dado "like" a un producto
$app->post($prefix . '/users/{id}/likes/{productId}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($client) {
    $userId = $args['id'];
    $productId = $args['productId'];

    // Verificar si el usuario existe
    $statement = new Statement('MATCH (u:User) WHERE id(u) = $userId RETURN u', ['userId' => (int)$userId]);
    $result = $client->runStatement($statement);
    if ($result->count() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'User not found'
        ]);
    }

    // Verificar si el producto existe
    $statement = new Statement('MATCH (p:Product) WHERE id(p) = $productId RETURN p', ['productId' => (int)$productId]);
    $result = $client->runStatement($statement);
    if ($result->count() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'Product not found'
        ]);
    }

    // Crear una relación de LIKES entre el usuario y el producto
    $statement = new Statement(
        'MATCH (u:User), (p:Product) WHERE id(u) = $userId AND id(p) = $productId 
         CREATE (u)-[r:LIKES]->(p) 
         RETURN r',
        [
            'userId' => (int)$userId,
            'productId' => (int)$productId
        ]
    );
    $result = $client->runStatement($statement);

    // Verificar si la relación se creó correctamente
    if ($result->count() === 0) {
        return createJsonResponse($response->withStatus(500), [
            'status' => 500,
            'message' => 'Failed to create relationship'
        ]);
    }

    // Devolver la respuesta
    return createJsonResponse($response->withStatus(201), [
        'status' => 201,
        'message' => 'Product liked'
    ]);
});

// Endpoint para registrar que un usuario ha comprado un producto
$app->post($prefix . '/users/{id}/purchases/{productId}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($client) {
    $userId = $args['id'];
    $productId = $args['productId'];

    // Verificar si el usuario existe
    $statement = new Statement('MATCH (u:User) WHERE id(u) = $userId RETURN u', ['userId' => (int)$userId]);
    $result = $client->runStatement($statement);
    if ($result->count() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'User not found'
        ]);
    }

    // Verificar si el producto existe
    $statement = new Statement('MATCH (p:Product) WHERE id(p) = $productId RETURN p', ['productId' => (int)$productId]);
    $result = $client->runStatement($statement);
    if ($result->count() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'Product not found'
        ]);
    }

    // Crear una relación de PURCHASED entre el usuario y el producto
    $statement = new Statement(
        'MATCH (u:User), (p:Product) WHERE id(u) = $userId AND id(p) = $productId 
         CREATE (u)-[r:PURCHASED]->(p) 
         RETURN r',
        [
            'userId' => (int)$userId,
            'productId' => (int)$productId
        ]
    );
    $result = $client->runStatement($statement);

    // Verificar si la relación se creó correctamente
    if ($result->count() === 0) {
        return createJsonResponse($response->withStatus(500), [
            'status' => 500,
            'message' => 'Failed to create relationship'
        ]);
    }

    // Devolver la respuesta
    return createJsonResponse($response->withStatus(201), [
        'status' => 201,
        'message' => 'Product purchased'
    ]);
});

// Endpoint para obtener productos con los que un usuario ha interactuado
$app->get($prefix . '/users/{id}/interactions', function (RequestInterface $request, ResponseInterface $response, array $args) use ($client) {
    $userId = $args['id'];

    // Verificar si el usuario existe
    $statement = new Statement('MATCH (u:User) WHERE id(u) = $userId RETURN u', ['userId' => (int)$userId]);
    $result = $client->runStatement($statement);
    if ($result->count() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'User not found'
        ]);
    }

    // Obtener productos con los que el usuario ha interactuado
    $statement = new Statement(
        'MATCH (u:User)-[r]->(p:Product) WHERE id(u) = $userId 
         RETURN p, type(r) as interactionType',
        ['userId' => (int)$userId]
    );
    $result = $client->runStatement($statement);

    // Preparar la lista de interacciones
    $interactions = [];
    foreach ($result as $record) {
        $node = $record->get('p');
        $interactions[] = [
            'productId' => $node->getId(),
            'name' => $node->getProperty('name'),
            'description' => $node->getProperty('description'),
            'price' => $node->getProperty('price'),
            'categoryId' => $node->getProperty('categoryId'),
            'interactionType' => $record->get('interactionType')
        ];
    }

    // Devolver la respuesta con la lista de interacciones
    return createJsonResponse($response, [
        'status' => 200,
        'interactions' => $interactions
    ]);
});
