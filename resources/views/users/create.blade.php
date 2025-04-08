@extends('layouts.admin')

@section('content')

<div class="content">
    <div class="content-title">
        <h1 class="page-title">Cadastrar Usuários</h1>
        <a href="#" class="btn-primary">Listar</a>
    </div>

    <x-alert />

    <form action="{{ route('user.store')}}" method="POST" class="form-container">
        @csrf
        <div class="mb-1">
            <label for="name" class="form-label">Nome:</label>
            <input type="text" name="name" placeholder="Nome" class="form-input" value="{{ old('name')}}"
                required><br><br>
        </div>

        <div class="mb-1">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" placeholder="Email" class="form-input" value="{{ old('email')}}"
                required><br><br>
        </div>

        <div class="mb-1">
            <label for="password" class="form-label">Senha:</label>
            <input type="password" name="password" class="form-input" placeholder="Senha com no mínimo 6 caracteres"
                value="{{ old('senha')}}" required><br><br>
        </div>

        <button type="submit" class="btn-success">Cadastrar</button>
    </form>
</div>
@endsection