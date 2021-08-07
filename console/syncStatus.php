<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Artia\Api;
use App\Artia\Token\Token;
use App\Models\Ticket\Ticket;
use Dotenv\Exception\InvalidPathException;
use Dotenv\Exception\InvalidFileException;

$header = '|--------------------------------------------------------------------------' . PHP_EOL;
$header .= '| Sincronização de Chamados/Atividades' . PHP_EOL;
$header .= '|--------------------------------------------------------------------------' . PHP_EOL;
$header .= '|' . PHP_EOL;
$header .= '| 1. Remove chamados não integrado com Artia.' . PHP_EOL;
$header .= '| 2. Atualiza estado dos chamados com base no estado da atividade.' . PHP_EOL;
$header .= '| Executando:' . PHP_EOL;

echo $header;

try {
    $env = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../');
    $env->load();

    Token::revalited();

    $tickets = (new Ticket())->getAllTickets();
    $api = new Api();

    if (!$tickets) {
        $state .= 'Nenhuma ticket encontrado no momento.';
        return;
    }

    $fp = fopen(__DIR__ . '/../storage/syncActivity.txt', 'a+');

    foreach ($tickets as $ticket) {
        // Remove chamados com sem id no artia.
        if ((int) $ticket->ID_ARTIA == 0) {
            $destroy = (new Ticket())->findBy((int) $ticket->TICKET_CHAMADO)->fetch();
            $destroy->destroy();
            $state .= '| Deletando chamados sem integração com Artia.' . PHP_EOL;
        }

        $api->required([
            'callback' => 'showActivity',
            'id' => (int) $ticket->ID_ARTIA,
            'folderId' => (int) $ticket->ID_FOLDER
        ])->build()->send();


        if ($ticket->ESTADO == 2 && trim($api->getResponse()->data->showActivity->customStatus->statusName) == 'Pendente') {
            $ticket->ESTADO = 1;
            $ticket->FINALIZACAO_ARTIA = null;
            $ticket->REABERTURA = date('Y-m-d H:i:s');
            $ticket->save();
            $state .= '| ' . $ticket->ID_ARTIA . ' alterado o estado para "Pendente" ' . date('d-m-Y à\s H:i:s') . PHP_EOL;
        }

        if ($ticket->ESTADO == 2 && trim($api->getResponse()->data->showActivity->customStatus->statusName) == 'Em Andamento') {
            $ticket->ESTADO = 1;
            $ticket->FINALIZACAO_ARTIA = null;
            $ticket->REABERTURA = date('Y-m-d H:i:s');
            $ticket->save();
            $state .= '| ' . $ticket->ID_ARTIA . ' alterado o estado para "Em Andamento" ' . date('d-m-Y à\s H:i:s') . PHP_EOL;
        }

        if ($ticket->ESTADO == 1 && trim($api->getResponse()->data->showActivity->customStatus->statusName) == 'Encerrado') {
            $date = $api->getResponse()->data->showActivity->actualEnd;
            $time = $api->getResponse()->data->showActivity->timeActualEnd;

            $ticket->ESTADO = 2;
            $ticket->FINALIZACAO_ARTIA = dateFormat($date . ' ' . $time)->format('Y-m-d H:i:s');
            $ticket->save();
            $state .= '| ' . $ticket->ID_ARTIA . ' alterado o estado para "Encerrado" ' . date('d-m-Y à\s H:i:s') . PHP_EOL;
        }
    }

    fwrite($fp, $state);
    fclose($fp);

    echo $state;
} catch (InvalidPathException $exception) {
    echo $exception->getMessage();
    exit();
} catch (InvalidFileException $exception) {
    echo $exception->getMessage();
    exit();
}
