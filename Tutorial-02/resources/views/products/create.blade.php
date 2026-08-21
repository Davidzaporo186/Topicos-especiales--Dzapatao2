<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear producto</title>
</head>
<body>

    <h1>Crear producto</h1>

    <form action="/products" method="POST">
        @csrf

        <div>
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name">
        </div>

        <br>

        <div>
            <label for="description">Descripción:</label>
            <textarea id="description" name="description"></textarea>
        </div>

        <br>

        <div>
            <label for="price">Precio:</label>
            <input type="number" id="price" name="price" step="0.01">
        </div>

        <br>

        <button type="submit">Guardar producto</button>
    </form>

</body>
</html>