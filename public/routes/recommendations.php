<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Laudis\Neo4j\Databags\Statement;

// Endpoint para obtener recomendaciones de productos basadas en compras y "likes" de usuarios con intereses similares
$app->get($prefix . '/users/{id}/recommendations', function (RequestInterface $request, ResponseInterface $response, array $args) use ($client) {
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

    // Obtener recomendaciones de productos basadas en compras y "likes" de usuarios con intereses similares
    $statement = new Statement(
        'MATCH (u:User)-[:LIKES|PURCHASED]->(p:Product)<-[:LIKES|PURCHASED]-(other:User)-[:LIKES|PURCHASED]->(rec:Product)
         WHERE id(u) = $userId AND NOT (u)-[:LIKES|PURCHASED]->(rec)
         RETURN DISTINCT rec',
        ['userId' => (int)$userId]
    );
    $result = $client->runStatement($statement);

    // Preparar la lista de recomendaciones
    $recommendations = [];
    foreach ($result as $record) {
        $node = $record->get('rec');
        $recommendations[] = [
            'productId' => $node->getId(),
            'name' => $node->getProperty('name'),
            'description' => $node->getProperty('description'),
            'price' => $node->getProperty('price'),
            'categoryId' => $node->getProperty('categoryId')
        ];
    }

    // Devolver la respuesta con la lista de recomendaciones
    return createJsonResponse($response, [
        'status' => 200,
        'recommendations' => $recommendations
    ]);
});

// Endpoint para obtener productos recomendados en función de lo que han comprado/valorado amigos del usuario
$app->get($prefix . '/users/{id}/friends-recommendations', function (RequestInterface $request, ResponseInterface $response, array $args) use ($client) {
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

    // Obtener recomendaciones de productos basadas en compras y "likes" de amigos del usuario
    $statement = new Statement(
        'MATCH (u:User)-[:FOLLOWS]->(friend:User)-[:LIKES|PURCHASED]->(rec:Product)
         WHERE id(u) = $userId AND NOT (u)-[:LIKES|PURCHASED]->(rec)
         RETURN DISTINCT rec',
        ['userId' => (int)$userId]
    );
    $result = $client->runStatement($statement);

    // Preparar la lista de recomendaciones
    $recommendations = [];
    foreach ($result as $record) {
        $node = $record->get('rec');
        $recommendations[] = [
            'productId' => $node->getId(),
            'name' => $node->getProperty('name'),
            'description' => $node->getProperty('description'),
            'price' => $node->getProperty('price'),
            'categoryId' => $node->getProperty('categoryId')
        ];
    }

    // Devolver la respuesta con la lista de recomendaciones
    return createJsonResponse($response, [
        'status' => 200,
        'recommendations' => $recommendations
    ]);
});

// Endpoint para obtener productos relacionados basados en patrones de compra y likes
$app->get($prefix . '/products/{id}/related', function (RequestInterface $request, ResponseInterface $response, array $args) use ($client) {
    $productId = $args['id'];

    // Verificar si el producto existe
    $statement = new Statement('MATCH (p:Product) WHERE id(p) = $productId RETURN p', ['productId' => (int)$productId]);
    $result = $client->runStatement($statement);
    if ($result->count() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'Product not found'
        ]);
    }

    // Obtener productos relacionados basados en patrones de compra y likes
    $statement = new Statement(
        'MATCH (p:Product)<-[:LIKES|PURCHASED]-(u:User)-[:LIKES|PURCHASED]->(related:Product)
         WHERE id(p) = $productId AND id(related) <> $productId
         RETURN DISTINCT related',
        ['productId' => (int)$productId]
    );
    $result = $client->runStatement($statement);

    // Preparar la lista de productos relacionados
    $relatedProducts = [];
    foreach ($result as $record) {
        $node = $record->get('related');
        $relatedProducts[] = [
            'productId' => $node->getId(),
            'name' => $node->getProperty('name'),
            'description' => $node->getProperty('description'),
            'price' => $node->getProperty('price'),
            'categoryId' => $node->getProperty('categoryId')
        ];
    }

    // Devolver la respuesta con la lista de productos relacionados
    return createJsonResponse($response, [
        'status' => 200,
        'relatedProducts' => $relatedProducts
    ]);
});
