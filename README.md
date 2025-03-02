# API REST para gestión de recomendaciones con Slim Framework y Neo4j

Este proyecto es un despliegue de una API REST para gestionar recomendaciones de productos utilizando Slim Framework y Neo4j. La API REST permite a los usuarios dar "me gusta" a productos, obtener recomendaciones personalizadas y ver las interacciones de otros usuarios. La API REST está desarrollada con Slim y se despliega con Docker.

## Instalación

Basta con clonar el repositorio y ejecutar el siguiente comando:

```bash
docker-compose up -d
```

## Acceso a la API REST

La API REST se despliega en `http://localhost:8080`. A continuación se describen los endpoints disponibles:

### Endpoints

#### Usuarios

- `POST /api/users`: Crear un nuevo usuario.
- `GET /api/users`: Obtener todos los usuarios.
- `GET /api/users/{id}`: Obtener un usuario específico por ID.
- `PUT /api/users/{id}`: Actualizar un usuario existente por ID.
- `DELETE /api/users/{id}`: Eliminar un usuario existente por ID.
- `GET /api/users/{id}/followers`: Obtener los seguidores de un usuario.

### Interacciones

- `POST /api/users/{id}/follows/{followedId}`: Registrar que un usuario sigue a otro.
- `POST /api/users/{id}/likes/{productId}`: Registrar que un usuario ha dado "like" a un producto.
- `POST /api/users/{id}/purchases/{productId}`: Registrar que un usuario ha comprado un producto.
- `GET /api/users/{id}/interactions`: Obtener productos con los que un usuario ha interactuado.

#### Productos

- `POST /api/products`: Crear un nuevo producto.
- `GET /api/products`: Obtener todos los productos.
- `GET /api/products/{id}`: Obtener un producto específico por ID.
- `PUT /api/products/{id}`: Actualizar un producto por ID.

#### Recomendaciones

- `GET /api/users/{id}/recommendations`: Obtener recomendaciones de productos basadas en compras y "likes" de usuarios con intereses similares.
- `GET /api/users/{id}/friends-recommendations`: Obtener productos recomendados en función de lo que han comprado/valorado amigos del usuario.
- `GET /api/products/{id}/related`: Obtener productos relacionados basados en patrones de compra y likes.

### Ejemplo de JSON de un usuario

```json
{
    "name": "Alice Smith",
    "email": "alicesmith@acme.com"
}
```

### Ejemplo de JSON de un producto

```json
{
    "name": "Smartphone XYZ",
    "description": "Smartphone de última generación con pantalla AMOLED",
    "price": 699.99,
    "categoryId": "1"
}
```

## Indices para rendimiento

Para mejorar el rendimiento de los endpoints, es recomendable definir los siguientes índices en Neo4j:

### Indices para la colección de productos

1. Indice en el campo `categoryId` para filtrar productos por categoría:
   ```cypher
   CREATE INDEX ON :Product(categoryId)
   ```

2. Indice en el campo `name` para buscar productos por nombre:
   ```cypher
   CREATE INDEX ON :Product(name)
   ```

### Indices para la colección de usuarios

1. Indice en el campo `email` para buscar usuarios por email:
   ```cypher
   CREATE INDEX ON :User(email)
   ```

### Indices para la colección de interacciones

1. Indice en el campo `userId` para filtrar interacciones por usuario:
   ```cypher
   CREATE INDEX ON :LIKES(userId)
   CREATE INDEX ON :PURCHASED(userId)
   ```

2. Indice en el campo `productId` para filtrar interacciones por producto:
   ```cypher
   CREATE INDEX ON :LIKES(productId)
   CREATE INDEX ON :PURCHASED(productId)
   ```
