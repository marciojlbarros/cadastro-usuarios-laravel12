<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Usuários</title>
</head>

<body>
    <p>Olá, {{ $user->name }}</p>
    <p>Seja bem-vindo ao nosso sistema.</p>

    <p>Seu login: <strong>{{ $user->email }}</strong></p>
    <p>Seu senha: <strong>{{ $password }}</strong></p>

    <p>por favor, altere sua senha após o primeiro login.</p>
</body>

</html>