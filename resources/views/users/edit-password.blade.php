@extends('layouts.admin')

@section('content')

<div class="content">
    <div class="content-title">
        <h1 class="page-title">Editar Senha do Usuários</h1>
        <span>
            <a href="{{ route('user.index') }}" class="btn-primary">Listar</a>
            <a href="{{ route('user.show', ['user' => $user->id]) }}" class="btn-primary">Visualizar</a>
        </span>
    </div>

    <x-alert />

    <form action="{{ route('user.update-password', ['user' => $user->id])}}" method="POST" class="form-container">
        @csrf
        @method('PUT')

        <div class="mb-1">
            <label for="password" class="form-label">Senha:</label>
            <input type="password" name="password" placeholder="Senha com no mínimo 6 caracteres" class="form-input"
                value="{{ old('password') }}"><br><br>
        </div>

        <button type="submit" class="btn-warning">Salvar</button>
    </form>
</div>
@endsection