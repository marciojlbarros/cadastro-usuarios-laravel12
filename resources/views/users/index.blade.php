@extends('layouts.admin')

@section('content')

<div class="content">
    <div class="content-title">
        <h1 class="page-title">Lista de Usuários</h1>
        <span>
            <a href="{{ route('user.create') }}" class="btn-success">Cadastrar</a>
            <a href="{{ url('generate-pdf-user') . (request()->getQueryString() ? '?' . request()->getQueryString() : '') }}"
                class="btn-warning">Gerar
                PDF</a>
            <a href="{{ url('generate-csv-user') . (request()->getQueryString() ? '?' . request()->getQueryString() : '') }}"
                class="btn-warning">Gerar
                CSV</a>
        </span>
    </div>

    <x-alert />

    <form class="pb-3 grid xl:grid-cols-5 md:grid-cols-2 gap-2 items-end">
        <input type="text" name="name" placeholder="Digite o Nome" value="{{ $name }}" class="form-input">

        <input type="text" name="email" placeholder="Digite o E-mail" value="{{ $email }}" class="form-input">

        <input type="datetime-local" name="start_date" value="{{ $start_date }}" class="form-input">

        <input type="datetime-local" name="end_date" value="{{ $end_date }}" class="form-input">

        <div class="flex gap-1">
            <button type="submit" class="btn-primary">
                <span>Pesquisar</span>
            </button>
            <a href="{{ route('user.index') }}" class="btn-warning">
                <span>Limpar</span>
            </a>
        </div>

    </form>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr class="table-header">
                    <th class="table-header">ID</th>
                    <th class="table-header">Nome</th>
                    <th class="table-header">Email</th>
                    <th class="table-header center">Ações</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse ($users as $user)
                <tr class="table-row">
                    <td class="table-cel">{{ $user->id }}</td>
                    <td class="table-cel">{{ $user->name }}</td>
                    <td class="table-cel">{{ $user->email }}</td>
                    <td class="table-actions">
                        <a href="{{ route('user.show', ['user' => $user->id]) }}" class="btn-primary">Visualizar</a>
                        <a href="{{ route('user.edit', ['user' => $user->id]) }}" class="btn-warning">Editar</a>
                        <form id="form-delete-{{ $user->id }}"
                            action="{{ route('user.destroy', ['user' => $user->id]) }}" method="POST">
                            @csrf
                            @method('delete')
                            <button type="button" class="btn-danger"
                                onclick="confirmDelete({{ $user->id }})">Apagar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <div class="alert-error">Nenhum registro encontrado</div>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">
        {{ $users->links() }}
    </div>
</div>
@endsection