<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;

class UserController extends Controller
{
    public function index(){
        $users = User::orderByDesc('id')->paginate(3);
        return view('users.index', ['users' => $users]);
    }

    public function create(){
        return view('users.create');        
    }

    public function store(UserRequest $request){

        try {
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]); 
        
        return redirect()->route('user.create')->with('success', 'Usuário cadastrado com sucesso!');
    } catch (Exception $e) {
        return back()->withInput()->with('error', 'Usuário não cadastrado!');
    }
}
}
