<?php 

use App\Artia\Api;
use App\Artia\Token\Token;
use App\Models\Ticket\Ticket;
use App\Models\Entity\Entity;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__ . '/../vendor/autoload.php';

$dot = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dot->load();

Token::loadCacheFile();

$tickets = (new Ticket())->getAllTicketsByResponsableId();

if (!$tickets) {
    echo '|Nenhum Ticket encontrado|';
    return;
}

$api = new Api();

echo "|ID_ARTIA|ESTADO" . PHP_EOL;

$log = new Logger('Helpdesk');
$log->pushHandler(new StreamHandler(__DIR__ . '/../storage/logs/responsable.txt', Logger::INFO));

$count = 0;
$update = 0;

foreach ($tickets as $ticket) {
    $api->requireds([
        'id' => (int) $ticket->ID_ARTIA,
        'accountId' => (int) env('CONFIG_API_ACCOUNT_ID'),
        'folderId' => (int) $ticket->ID_FOLDER
    ])->showActivity();

    $response = $api->response();
    
    if (!isset($response->errors)) {
        $responsible = trim($response->data->showActivity->responsible->id);
        $user = (new Entity())->find()->where(['USUARIO_ARTIA' => $responsible])->first();
        
        echo '|' . $ticket->ID_ARTIA . '|Verificando informações do Chamado' . PHP_EOL;

        if ($ticket->USUARIO_ARTIA != $responsible) {
            echo '|' . $ticket->ID_ARTIA .'|Responsável transferido de: ' . $ticket->USUARIO_ARTIA . ' para: ' . $responsible . PHP_EOL;
            $log->info($ticket->ID_ARTIA . ': Novo responsável ' . $responsible); 

            $findBy = (new Ticket())->findBy($ticket->TICKET_CHAMADO)->first();
            $findBy->RESPONSAVEL_ARTIA = (int) $user->COD_PROCFIT;
            $findBy->save();
            $update++;
        }  
        $count++; 
    }
}

echo "|Chamados Verificados: \e[32m{$count}\e[0m" . PHP_EOL;
echo "|Atualização de Estado: \e[34m{$update}\e[0m" . PHP_EOL;
echo "|Data de Execução: " . date('d/m/Y à\s H:i:s') . PHP_EOL;