<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\UserPdfMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index(Request $request){
        // $users = User::orderByDesc('id')->paginate(3);
        $users = User::when(
            $request->filled('name'),
            fn($query) => $query->whereLike('name', '%'. $request->name . '%')
        )
        ->when(
            $request->filled('email'),
            fn($query) => $query->whereLike('email', '%'. $request->email . '%')
        )
        ->when(
            $request->filled('start_date'),
            fn($query) => $query->where('created_at', '>=', Carbon::parse($request->start_date))
        )
        ->when(
            $request->filled('end_date'),
            fn($query) => $query->where('created_at', '<=', Carbon::parse($request->end_date))
        )
        ->orderByDesc('id')
        ->paginate(10)
        ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'name' => $request->name,
            'email' => $request->email,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
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

    public function generatePdf(User $user){
        try {
            
            $pdf = Pdf::loadView('users.generate-pdf', ['user' => $user])
            ->setPaper('a4', 'portrait');

            //Definir o caminho temporário para salvar o PDF
            $pdfPath = storage_path("app/public/$user->name.pdf");

            //Salvar o PDF localmente
            $pdf->save($pdfPath);

            //Enviar e-mail com o PDF anexado        
            Mail::to($user->email)->send(new UserPdfMail($pdfPath, $user));

            //Remover o arquivo após o envio do e-mail
            if(file_exists($pdfPath)){
                unlink($pdfPath);
            }

            //Redirecionar o usuário, enviar a mensagem de sucesso
            return redirect()->route('user.show', ['user' => $user->id])->with('success', 'E-mail enviado com sucesso!');

        } catch (Exception $e) {
            //Redirecionar o usuário, enviar a mensagem de erro
            return redirect()->route('user.show', ['user' => $user->id])->with('error', 'E-mail não enviado!');
        }
    }

    public function generatePdfUsers(Request $request)
    {
        try {
        $users = User::when(
            $request->filled('name'),
            fn($query) => $query->whereLike('name', '%'. $request->name . '%')
        )
        ->when(
            $request->filled('email'),
            fn($query) => $query->whereLike('email', '%'. $request->email . '%')
        )
        ->when(
            $request->filled('start_date'),
            fn($query) => $query->where('created_at', '>=', Carbon::parse($request->start_date))
        )
        ->when(
            $request->filled('end_date'),
            fn($query) => $query->where('created_at', '<=', Carbon::parse($request->end_date))
        )
        ->orderByDesc('name')
        ->get();

        //Somar total de usuários
        $totalUsers = $users->count('id');

        $numberRecordsAllowed = 500;
        if ($totalUsers > $numberRecordsAllowed) {
            return redirect()->route('user.index', [
                'email' => $request->email,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ])->with('error', "Limite de registros ultrapassado! Apenas $numberRecordsAllowed registros!");
        }

        //Carregar a strimg com o HTML/conteúdo e determinar a orientação e o tamanho do arquivo
        $pdf = Pdf::loadView('users.generate-pdf-users', [ 'users' => $users])
        ->setPaper('a4','portrait');

        //Fazer o download do PDF
        return $pdf->download('list_users.pdf');

        } catch (Exception $e) {
            //Redirecionar o usuário, enviar a mensagem de erro
            return redirect()->route('user.index')->with('error', 'PDF não gerado!');
        }
    }

    public function generateCsvUsers(Request $request)
    {
        //Recupera os resultados do banco de dados
        //$users = User::orderByDesc('id')->get();

        $users = User::when(
            $request->filled('name'),
            fn($query) => $query->whereLike('name', '%'. $request->name . '%')
        )
        ->when(
            $request->filled('email'),
            fn($query) => $query->whereLike('email', '%'. $request->email . '%')
        )
        ->when(
            $request->filled('start_date'),
            fn($query) => $query->where('created_at', '>=', Carbon::parse($request->start_date))
        )
        ->when(
            $request->filled('end_date'),
            fn($query) => $query->where('created_at', '<=', Carbon::parse($request->end_date))
        )
        ->orderByDesc('name')
        ->get();

        //Somar total de usuários
        $totalRecords = $users->count('id');
        //Verificar se a quantidade de registros ultrapassa o limite para gerar o CSV
        $numberRecordsAllowed = 2;

        if($totalRecords > $numberRecordsAllowed){
            return redirect()->route('user.index', [
                'name' => $request->name,
                'email' => $request->email,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ])->with('error', "Limite de registros ultrapassado! Apenas $numberRecordsAllowed registros!");

        }       

        //Criar um arquivo temporário CSV
        $csvFileName = tempnam(sys_get_temp_dir(), 'csv_' . Str::ulid());

        //Abre o arquivo CSV para escrita
        $openFile = fopen($csvFileName, 'w');

        //Criar o cabecalho do Excel
        $header = ['id', 'Nome', 'E-mail', 'Data de Cadastro'];

        //Escreve o cabeçalho no arquivo CSV
        fputcsv($openFile, $header, ';');

        //Criar o array com os dados da linha do Excel
        foreach ($users as $user) {
            $userArray = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i:s'),
            ];

            //Escreve a linha no arquivo CSV
            fputcsv($openFile, $userArray, ';');

        }

        //Fecha o arquivo CSV
        fclose($openFile);

        //Envia o arquivo CSV para o browser
        return response()->download($csvFileName, 'lista_users_' . Str::ulid() . '.csv');
    }

}
