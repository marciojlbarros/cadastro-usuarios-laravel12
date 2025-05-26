<?php

namespace App\Jobs;

use App\Models\User;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class ImportCsvJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $filePath)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //Definir o caminho do arquivo CSV
        $filePath = storage_path('app/private/' . $this->filePath);

        //Verificar se o arquivo existe
        if(!file_exists($filePath)){
            return;
        }
        //Cria uma instancia do leitor de CSV a partir do caminho do arquivo
        $csv = Reader::createFromPath($filePath, 'r');

        //Definir o delimitador do CSV
        $csv->setDelimiter(';');

        //indica que a primeira linha contém cabeçalho
        $csv->setHeaderOffset(0);

        //Processa o arquivo CSV e retorna os registros como uma coleção
        $records = (new Statement())->process($csv);

        //Inicializa um array para armazenar os dados que serão inseridos em lote
        $batchInsert = [];

        //Itera pelos registros e adiciona-os ao array de lote
        foreach ($records as $record) {
            //Obtem o valor da coluna 'email', ou null se não existir
            $email = $record['email'] ?? null;
            
            //Obtem o valor da coluna 'name', ou null se não existir
            $name = $record['name'] ?? null;

            //Valida se o e-mail e o nome foram obtidos corretamente
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;                
            }

            //Verifica se já existe um registro com o mesmo e-mail
            if (User::where('email', $email)->exists()) {
                continue;
            }

            //Adiciona os dados do novo usuario no array de lote
            $batchInsert[] = [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(7), ['rounds' => 12]),
            ];

            //Se já existe 50 registros prontos, faz a inserção em lote no banco de dados
            if (count($batchInsert) >= 50) {
               //Insere os registros no banco de dados
                User::insert($batchInsert);
                //Limpa o array de lote
                $batchInsert = [];
            }
        }

            //Apos o loop, insere os registos restantes que ficaram abaixo de 50
            if (!empty($batchInsert)) {
               //Insere os registros no banco de dados
                User::insert($batchInsert);
                //Limpa o array de lote
                $batchInsert = [];
            }
    }
}
