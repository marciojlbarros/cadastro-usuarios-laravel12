@extends('layouts.admin')

@section('content')

<div class="content">
    <div class="content-title">
        <h1 class="page-title">Bem-vindo!</h1>
        <a href="#" class="btn-primary">Listar</a>
    </div>

    <a href="{{ route('user.create') }}" class="btn-success">Cadastrar</a>
</div>



@endsection