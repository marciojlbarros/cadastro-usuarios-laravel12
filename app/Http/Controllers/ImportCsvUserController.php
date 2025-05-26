<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Jobs\ImportCsvJob;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ImportCsvUserController extends Controller
{
    public function importCsvUsers(Request $request){
                    //Validar o arquivo
            $request->validate([
                'file' => 'required|mimes:csv,txt|max:8192', // 8Mb
            ], [
                'file.required' => 'O campo Arquivo CSV é obrigatório!',
                'file.mimes' => 'O campo Arquivo CSV deve ser do tipo CSV ou TXT!',
                'file.max' => 'O tamanho do Arquivo excede :max Mb.',
            ]);

            try{
                //Gerar um nome de arquivo baseado na data e hora atual
                $fileName = 'import-' . now()->format('Y-m-d-H-i-s') . '.csv';

                //Receber o arquvo e mover para pasta de uploads
                $path =$request->file('file')->storeAs('uploads', $fileName);

                //Despachar o job para importar os dados
                ImportCsvJob::dispatch($path);

                 //Redirecionar o usuário, enviar a mensagem de sucesso
                return back()->withInput()->with('success', 'Dados estão sendo importados!');

           
            } catch (Exception $e) {
            //Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Dados nao importados!');
        }
    }
}
