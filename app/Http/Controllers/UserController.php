<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function index(){
        $users = User::orderByDesc('id')->paginate(3);
        return view('users.index', ['users' => $users]);
    }

    public function show(User $user){
        return view('users.show', ['user' => $user]);
    }

    public function create(){
        return view('users.create');        
    }

    public function store(UserRequest $request){

        try {
        $user = User::create([
            'name' => $request->name,
        'email' => $request->email,
            'password' => $request->password
        ]); 
        
        return redirect()->route('user.show', ['user' => $user->id])->with('success', 'Usuário cadastrado com sucesso!');
    } catch (Exception $e) {
        return back()->withInput()->with('error', 'Usuário não cadastrado!');
    }
}

    public function edit(User $user){
        return view('users.edit', ['user' => $user]);
    }

    public function editPassword(User $user){
        return view('users.edit-password', ['user' => $user]);
    }

    public function updatePassword(Request $request, User $user){
      $request->validate([
        'password' => 'required|min:6'
      ], [
        'password.required' => 'O campo Senha é obrigatório!',
        'password.min' => 'Senha no mínimo :min caracteres!',
      ]);

      try{
        $user->update([
          'password' => $request->password
        ]); 

        return redirect()->route('user.edit-password', ['user' => $user->id])->with('success', 'Senha atualizada com sucesso!');
      } catch (Exception $e) {
        return back()->withInput()->with('error', 'Senha não atualizada!');
      }

    }

    public function update(UserRequest $request, User $user){
        try{
            $user->update([
                'name' => $request->name,
                'email' => $request->email
            ]); 
        return redirect()->route('user.show', ['user' => $user->id])->with('success', 'Usuário atualizado com sucesso!');
            } catch (Exception $e) {
                return back()->withInput()->with('error', 'Usuário não atualizado!');
            }
    }

    public function destroy(User $user){
        try {
            $user->delete();
            return redirect()->route('user.index')->with('success', 'Usuário excluido com sucesso!');
        } catch (Exception $e) {
            return redirect()->route('user.index')->with('error', 'Usuário não excluido!');
        }
    }
}

