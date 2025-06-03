<?php

namespace App\Jobs;

use App\Models\User;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Support\Str;
use App\Mail\WelcomeUserMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

        // Variavel para receber o tempo progressivo do envio do e-mail
        $delaySeconds = 0;

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

            //Gerar senha temporária
            $password = Str::random(7);

            //Criar o usuário
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            // Enviar o e-mail de boas-vindas
            // Não usar o send para grande quantidade de usuários.
            Mail::to($email)->send(new WelcomeUserMail($user, $password));

            // Para grande quantidade de usuários, utilizar o queue.
            //Mail::to($email)->queue(new WelcomeUserMail($user, $password));

            // Para grande quantidade de usuários, usar o later para agendar o
            // envio a cada 10 segundo, com o objetivo de distribuir a carga de envio de muitos e-mails.
            //Mail::to($email)->later(now()->addSeconds($delaySeconds), new WelcomeUserMail($user, $password));

            //Incrementa o tempo de envio do e-mail
            //$delaySeconds += 10;

        }
    }
}
