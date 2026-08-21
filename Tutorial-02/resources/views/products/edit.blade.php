<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar producto</title>
</head>
<body>

    <h1>Editar producto</h1>

    <form action="/products/{{ $product->id }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="name">Nombre:</label>
            <br>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ $product->name }}"
                required
            >
        </div>

        <br>

        <div>
            <label for="description">Descripción:</label>
            <br>
            <textarea
                id="description"
                name="description"
                rows="5"
                cols="50"
                required
            >{{ $product->description }}</textarea>
        </div>

        <br>

        <div>
            <label for="price">Precio:</label>
            <br>
            <input
                type="number"
                id="price"
                name="price"
                value="{{ $product->price }}"
                step="0.01"
                required
            >
        </div>

        <br>

        <button type="submit">Actualizar producto</button>
    </form>

    <br>

    <a href="/products/{{ $product->id }}">Cancelar</a>

</body>
</html>