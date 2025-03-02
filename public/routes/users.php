<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Laudis\Neo4j\Databags\Statement;

// Endpoint para crear un nuevo usuario
$app->post($prefix . '/users', function (RequestInterface $request, ResponseInterface $response) use ($client) {
    $data = $request->getParsedBody() ?? [];

    // Validar los datos de entrada
    if (!array_key_exists('name', $data) || !array_key_exists('email', $data)) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid input'
        ]);
    }

    // Crear un nuevo nodo de usuario en la base de datos
    $statement = new Statement('CREATE (u:User {name: $name, email: $email}) RETURN u', $data);
    $result = $client->runStatement($statement);

    // Obtener el ID del nuevo usuario
    $result = $result->first()->get('u')->getId();

    // Devolver la respuesta con el ID del nuevo usuario
    return createJsonResponse($response->withStatus(201), [
        'status' => 201,
        'message' => 'User created',
        'userId' => $result
    ]);
});

// Endpoint para obtener todos los usuarios
$app->get($prefix . '/users', function (RequestInterface $request, ResponseInterface $response) use ($client) {
    // Obtener todos los nodos de usuarios de la base de datos
    $statement = new Statement('MATCH (u:User) RETURN u', []);
    $result = $client->runStatement($statement);

    // Preparar la lista de usuarios
    $users = [];
    foreach ($result as $user) {
        $node = $user->get('u');
        $users[] = [
            'id' => $node->getId(),
            'name' => $node->getProperty('name'),
            'email' => $node->getProperty('email')
        ];
    }

    // Devolver la respuesta con la lista de usuarios
    return createJsonResponse($response, [
        'status' => 200,
        'users' => $users
    ]);
});

// Endpoint para registrar que un usuario sigue a otro
$app->post($prefix . '/users/{id}/follows/{followedId}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($client) {
    $id = $args['id'];
    $followedId = $args['followedId'];

    // Verificar si el usuario a seguir existe
    $statement = new Statement('MATCH (u:User) WHERE id(u) = $followedId RETURN u', ['followedId' => (int)$followedId]);
    $result = $client->runStatement($statement);
    if ($result->count() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'User to follow not found'
        ]);
    }

    // Crear una relación de FOLLOWS entre los dos usuarios
    $statement = new Statement(
        'MATCH (u1:User), (u2:User) WHERE id(u1) = $id AND id(u2) = $followedId 
         CREATE (u1)-[r:FOLLOWS]->(u2) 
         RETURN r',
        [
            'id' => (int)$id,
            'followedId' => (int)$followedId
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
        'message' => 'User followed'
    ]);
});

// Endpoint para obtener los seguidores de un usuario
$app->get($prefix . '/users/{id}/followers', function (RequestInterface $request, ResponseInterface $response, array $args) use ($client) {
    $id = $args['id'];

    // Obtener los nodos de usuarios que siguen al usuario
    $statement = new Statement(
        'MATCH (u1:User)-[:FOLLOWS]->(u2:User) WHERE id(u2) = $id RETURN u1',
        ['id' => (int)$id]
    );
    $result = $client->runStatement($statement);

    // Preparar la lista de seguidores
    $followers = [];
    foreach ($result as $record) {
        $node = $record->get('u1');
        $followers[] = [
            'id' => $node->getId(),
            'name' => $node->getProperty('name'),
            'email' => $node->getProperty('email')
        ];
    }

    // Devolver la respuesta con la lista de seguidores
    return createJsonResponse($response, [
        'status' => 200,
        'followers' => $followers
    ]);
});

// Endpoint para obtener un usuario específico
$app->get($prefix . '/users/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($client) {
    $id = $args['id'];

    // Obtener el nodo de usuario de la base de datos
    $statement = new Statement('MATCH (u:User) WHERE id(u) = $id RETURN u', ['id' => (int)$id]);
    $result = $client->runStatement($statement);

    // Verificar si el usuario existe
    if ($result->count() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'User not found'
        ]);
    }

    // Obtener los datos del usuario
    $node = $result->first()->get('u');
    $user = [
        'id' => $node->getId(),
        'name' => $node->getProperty('name'),
        'email' => $node->getProperty('email')
    ];

    // Devolver la respuesta con los datos del usuario
    return createJsonResponse($response, [
        'status' => 200,
        'user' => $user
    ]);
});

// Endpoint para actualizar un usuario existente
$app->put($prefix . '/users/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($client) {
    $id = $args['id'];
    $data = $request->getParsedBody() ?? [];

    // Validar los datos de entrada
    if (!array_key_exists('name', $data) || !array_key_exists('email', $data)) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid input'
        ]);
    }

    $name = $data['name'];
    $email = $data['email'];

    // Actualizar el nodo de usuario en la base de datos
    $statement = new Statement('MATCH (u:User) WHERE id(u) = $id SET u.name = $name, u.email = $email RETURN u', array_merge($data, ['id' => (int)$id]));
    $result = $client->runStatement($statement);

    // Verificar si el usuario existe
    if ($result->count() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'User not found'
        ]);
    }

    // Devolver la respuesta
    return createJsonResponse($response, [
        'status' => 200,
        'message' => 'User updated'
    ]);
});

// Endpoint para eliminar un usuario existente
$app->delete($prefix . '/users/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($client) {
    $id = $args['id'];

    // Eliminar el nodo de usuario de la base de datos
    $statement = new Statement('MATCH (u:User) WHERE id(u) = $id DELETE u', ['id' => (int)$id]);
    $result = $client->runStatement($statement);

    // Devolver la respuesta
    return createJsonResponse($response, [
        'status' => 200,
        'message' => 'User deleted'
    ]);
});
