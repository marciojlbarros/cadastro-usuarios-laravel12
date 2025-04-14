@extends('layouts.admin')

@section('content')

<div class="content">
    <div class="content-title">
        <h1 class="page-title">Bem-vindo!</h1>
        <a href="{{ route('user.index') }}" class="btn-primary">Listar</a>
    </div>
</div>

@endsection