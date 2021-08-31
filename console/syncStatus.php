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

$tickets = (new Ticket())->find()->all();

if (!$tickets) {
    echo '|Nenhum Ticket encontrado|';
    return;
}

$api = new Api();

$log = new Logger('Helpdesk');
$log->pushHandler(new StreamHandler(__DIR__ . '/../storage/logs/status.txt', Logger::INFO));

echo "|ID_ARTIA|ESTADO" . PHP_EOL;

$count = 0;
$update = 0;

foreach ($tickets as $ticket) {
    if ($ticket->ESTADO == 1) {
        $api->requireds([
          'id' => (int) $ticket->ID_ARTIA,
          'accountId' => (int) env('CONFIG_API_ACCOUNT_ID'),
          'folderId' => (int) $ticket->ID_FOLDER
        ])->showActivity();

        $response = $api->response();
        $status = trim($response->data->showActivity->customStatus->statusName);
        
        echo "|" . $ticket->ID_ARTIA . "|" . $status . PHP_EOL;
        
        if ($status === 'Encerrado') {
            $date = $response->data->showActivity->actualEnd;
            $time = str_replace(' ', '', $response->data->showActivity->timeActualEnd);

            $responsible = trim($response->data->showActivity->responsible->id);
            $user = (new Entity())->find()->where(['USUARIO_ARTIA' => $responsible])->first();

            $ticket->ESTADO = 2;

            if($ticket->RESPONSAVEL_ARTIA != $user->COD_PROCFIT) {
                echo '|'. $ticket->ID_ARTIA . '|' . 'De ' . $ticket->RESPONSAVEL_ARTIA .  ' Transferido para: ' . $user->COD_PROCFIT . PHP_EOL; 
                $ticket->RESPONSAVEL_ARTIA = (int) $user->COD_PROCFIT;
            }

            $ticket->FINALIZACAO_ARTIA = date('Y-m-d H:i:s', strtotime($date . ' ' . $time));
            $ticket->save();

            $log->info($ticket->ID_ARTIA . ': Atividade Alterada Para "Encerrado"');
            $update++;
        }
        $count++;
    }
}

echo "|Chamados Verificados: \e[32m{$count}\e[0m" . PHP_EOL;
echo "|Atualização de Estado: \e[34m{$update}\e[0m" . PHP_EOL;
echo "|Data de Execução: " . date('d/m/Y à\s H:i:s') . PHP_EOL;
