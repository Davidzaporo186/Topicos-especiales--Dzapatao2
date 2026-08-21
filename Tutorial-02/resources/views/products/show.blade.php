<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }}</title>
</head>
<body>

    <h1>{{ $product->name }}</h1>

    <p>{{ $product->description }}</p>

    <p>Precio: ${{ $product->price }}</p>

    <h2>Comentarios</h2>

    @forelse ($product->comments as $comment)
        <div>
            <p>{{ $comment->content }}</p>
        </div>
        <hr>
    @empty
        <p>No hay comentarios.</p>
    @endforelse

    <h2>Agregar comentario</h2>

    <form action="/products/{{ $product->id }}/comments" method="POST">
        @csrf

        <div>
            <label for="content">Comentario:</label>
            <br>
            <textarea
                id="content"
                name="content"
                rows="5"
                cols="50"
                required
            ></textarea>
        </div>

        <br>

        <button type="submit">Guardar comentario</button>
    </form>

    <br>
    <form action="/products/{{ $product->id }}" method="POST">
    @csrf
    @method('DELETE')

    <button type="submit">
        Eliminar producto
    </button>
</form>

<br>

    <a href="/products">Volver a productos</a>

</body>
</html>