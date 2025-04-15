@extends('layouts.admin')

@section('content')

<div class="content">
    <div class="content-title">
        <h1 class="page-title">Detalhes do Usuários</h1>
        <span class="flex space-x-1">
            <a href="{{ route('user.index') }}" class="btn-primary">Listar</a>
            <a href="{{ route('user.edit', ['user' => $user->id]) }}" class="btn-warning">Editar</a>
            <a href="{{ route('user.edit-password', ['user' => $user->id]) }}" class="btn-warning">Editar Senha</a>

            <form id="form-delete-{{ $user->id }}" action="{{ route('user.destroy', ['user' => $user->id]) }}"
                method="POST">
                @csrf
                @method('delete')
                <button type="button" class="btn-danger" onclick="confirmDelete({{ $user->id }})">Apagar</button>
            </form>
        </span>
    </div>

    <x-alert />

    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Informações do Usuário</h2>
        <div class="text-gray-700">
            <div class="md-2">
                <span class="font-semibold">ID:</span>
                <span>{{ $user->id }}</span>
            </div>
            <div class="md-2">
                <span class="font-semibold">Nome:</span>
                <span>{{ $user->name }}</span>
            </div>
            <div class="md-2">
                <span class="font-semibold">E-mail:</span>
                <span>{{ $user->email }}</span>
            </div>
            <div class="md-2">
                <span class="font-semibold">Criado em:</span>
                <span>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</span>
            </div>
            <div class="md-2">
                <span class="font-semibold">Editado em:</span>
                <span>{{ \Carbon\Carbon::parse($user->updated_at)->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>

</div>
@endsection