<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cadastro de Usuários - Laravel 12</title>

</head>

<body>
    <h1>Cadastro de Usuários</h1>
    <a href="{{ route('user.create') }}">Cadastrar</a>
</body>

</html>