<div>
    <h2>Cadastrar Usuários</h2>

    @if(@session('success'))
    <p style="color: green">{{ session('success') }}</p>
    @endif

    @if(@session('error'))
    <p style="color: red">{{ session('error') }}</p>
    @endif

    <form action="{{ route('user.store')}}" method="POST">
        @csrf
        <label for="name">Nome:</label>
        <input type="text" name="name" placeholder="Nome" value="{{ old('name')}}" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" placeholder="Email" value="{{ old('email')}}" required><br><br>

        <label for="password">Senha:</label>
        <input type="password" name="password" placeholder="Senha com no mínimo 6 caracteres" value="{{ old('senha')}}"
            required><br><br>

        <button type="submit">Cadastrar</button>
    </form>

</div>