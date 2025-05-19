<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ImportCsvUserController extends Controller
{
    public function importCsvUsers(Request $request){
        try{
            //Validar o arquivo
            $request->validate([
                'file' => 'required|mimes:csv,txt|max:2048',
            ], [
                'file.required' => 'O campo Arquivo CSV é obrigatório!',
                'file.mimes' => 'O campo Arquivo CSV deve ser do tipo CSV ou TXT!',
                'file.max' => 'O tamanho do Arquivo excede :max Mb.',
            ]);

            //criar um array com os dados da coluna no banco de dados
            $headers = [
                'name',
                'email',
                'password',
            ];
            //Receber o arquivo, ler os dados e converter a string em array
            $fileData = array_map('str_getcsv', file($request->file('file')));

            // Definir o separador de colunas
            $separator = ';';

            //Criar array para armazenar os valores que serão inseridos no banco de dados
            $arrayValues = [];

            //Criar array para armazenar os emails duplicados encontrados
            $duplicatedEmails = [];

            //Contador de registros cadastrados
            $numberRegisterRecords = 0;
            
            //Percorrer cada linha do arquivo CSV
            foreach($fileData as $row){

                //Separar os valores da linha utilizando o separador
                $values = explode($separator, $row[0]);

                //Verificar se a quantidade de colunas do arquivo CSV é diferente da quantidade de colunas do banco de dados
                if(count($values) != count($headers)){
                    continue;
                }

                //Combinar os valores da linha com os nomes das colunas
                $userData = array_combine($headers, $values);

                //Verificar se o email ja foi cadastrado
                $emailExists = User::where('email', $userData['email'])->exists();

                if($emailExists){
                    $duplicatedEmails[] = $userData['email'];
                    continue;
                }

                //Inserir os dados no array de valores para serem inseridos no banco de dados
                $arrayValues[] = [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make(Str::random(7), ['rounds' => 12]),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                //Incrementar o Contador de registros cadastrados
                $numberRegisterRecords++;
            }

            //Verificar se o email já é cadastrado, retornar a mensagem de erro
            if(!empty($duplicatedEmails)){
                return back()->withInput()->with('error', 'Dados nao importados. Existe emails cadastrados: <br>'. implode(', ', $duplicatedEmails));
            }

            //Inserir os dados no banco de dados
            User::insert($arrayValues);

            //Redirecionar o usuário, enviar a mensagem de sucesso
            return back()->withInput()->with('success', 'Dados importados com sucesso! Quantidade de registros cadastrados: '.$numberRegisterRecords);

        } catch (Exception $e) {
            //Redirecionar o usuário, enviar a mensagem de erro
            return back()->withInput()->with('error', 'Dados nao importados!');
        }
    }
}
