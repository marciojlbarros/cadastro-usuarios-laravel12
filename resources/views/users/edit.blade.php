@extends('layouts.admin')

@section('content')

<div class="content">
    <div class="content-title">
        <h1 class="page-title">Editar Usuários</h1>
        <span>
            <a href="{{ route('user.index') }}" class="btn-primary">Listar</a>
            <a href="{{ route('user.show', ['user' => $user->id]) }}" class="btn-primary">Visualizar</a>
        </span>
    </div>

    <x-alert />

    <form action="{{ route('user.update', ['user' => $user->id])}}" method="POST" class="form-container">
        @csrf
        @method('PUT')

        <div class="mb-1">
            <label for="name" class="form-label">Nome:</label>
            <input type="text" name="name" placeholder="Nome" class="form-input" value="{{ old('name', $user->name) }}">
            <br><br>
        </div>

        <div class="mb-1">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" placeholder="Email" class="form-input"
                value="{{ old('email', $user->email) }}"><br><br>
        </div>

        <button type="submit" class="btn-warning">Salvar</button>
    </form>
</div>
@endsection