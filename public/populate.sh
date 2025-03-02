!# /bin/bash

# curl requests to populate the database using the API

# Populate users
# user structure: name, email

curl -X POST -H "Content-Type: application/json" -d '{"name": "John Doe", "email": "johndoe@acme.com"}' http://localhost:8084/api/users
curl -X POST -H "Content-Type: application/json" -d '{"name": "Jane Doe", "email": "janedoe@acme.com"}' http://localhost:8084/api/users
curl -X POST -H "Content-Type: application/json" -d '{"name": "Alice Smith", "email": "alicesmith@acme.com"}' http://localhost:8084/api/users
curl -X POST -H "Content-Type: application/json" -d '{"name": "Bob Johnson", "email": "bobjohnson@acme.com"}' http://localhost:8084/api/users
curl -X POST -H "Content-Type: application/json" -d '{"name": "Carol White", "email": "carolwhite@acme.com"}' http://localhost:8084/api/users

# Populate products
# product structure: name, description, price, categoryId

curl -X POST -H "Content-Type: application/json" -d '{"name": "Smartphone XYZ", "description": "Smartphone de última generación con pantalla AMOLED", "price": 699.99, "categoryId": "1"}' http://localhost:8084/api/products
curl -X POST -H "Content-Type: application/json" -d '{"name": "Laptop ABC", "description": "Laptop ultraligera con procesador Intel i7", "price": 999.99, "categoryId": "1"}' http://localhost:8084/api/products
curl -X POST -H "Content-Type: application/json" -d '{"name": "Auriculares Bluetooth", "description": "Auriculares inalámbricos con cancelación de ruido", "price": 199.99, "categoryId": "2"}' http://localhost:8084/api/products
curl -X POST -H "Content-Type: application/json" -d '{"name": "Smartwatch 123", "description": "Reloj inteligente con monitor de ritmo cardíaco", "price": 149.99, "categoryId": "2"}' http://localhost:8084/api/products
curl -X POST -H "Content-Type: application/json" -d '{"name": "Cámara Digital", "description": "Cámara digital con lente de 24MP y zoom óptico", "price": 499.99, "categoryId": "3"}' http://localhost:8084/api/products
curl -X POST -H "Content-Type: application/json" -d '{"name": "Tablet Pro", "description": "Tablet con pantalla de 10 pulgadas y 128GB de almacenamiento", "price": 399.99, "categoryId": "3"}' http://localhost:8084/api/products
curl -X POST -H "Content-Type: application/json" -d '{"name": "Teclado Mecánico", "description": "Teclado mecánico retroiluminado para gamers", "price": 89.99, "categoryId": "4"}' http://localhost:8084/api/products
curl -X POST -H "Content-Type: application/json" -d '{"name": "Ratón Inalámbrico", "description": "Ratón ergonómico inalámbrico con batería recargable", "price": 49.99, "categoryId": "4"}' http://localhost:8084/api/products
curl -X POST -H "Content-Type: application/json" -d '{"name": "Monitor 4K", "description": "Monitor 4K UHD de 27 pulgadas", "price": 299.99, "categoryId": "5"}' http://localhost:8084/api/products
curl -X POST -H "Content-Type: application/json" -d '{"name": "Impresora Multifunción", "description": "Impresora multifunción con escáner y copiadora", "price": 129.99, "categoryId": "5"}' http://localhost:8084/api/products

# Register follows

curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/0/follows/1
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/0/follows/2
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/1/follows/3
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/2/follows/4
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/3/follows/0

# Register likes

curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/0/likes/5
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/0/likes/6
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/1/likes/7
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/1/likes/8
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/2/likes/9
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/2/likes/10
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/3/likes/11
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/3/likes/12
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/4/likes/13
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/4/likes/14
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/2/likes/5
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/2/likes/6

# Register purchases

curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/0/purchases/5
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/0/purchases/6
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/1/purchases/7
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/1/purchases/8
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/2/purchases/9
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/2/purchases/10
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/3/purchases/11
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/3/purchases/12
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/4/purchases/13
curl -X POST -H "Content-Type: application/json" http://localhost:8084/api/users/4/purchases/14