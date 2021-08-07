<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Artia\Api;
use App\Artia\Token\Token;
use App\Models\Ticket\Departament;
use App\Models\Ticket\Ticket;
use Dotenv\Exception\InvalidPathException;
use Dotenv\Exception\InvalidFileException;

$header = '|--------------------------------------------------------------------------' . PHP_EOL;
$header .= '| Deleta chamados que não está presente no Artia' . PHP_EOL;
$header .= '|--------------------------------------------------------------------------' . PHP_EOL;
$header .= '|' . PHP_EOL;
$header .= '| Executando:' . PHP_EOL;

echo $header;

try {
    $env = \Dotenv\Dotenv::createUnsafeImmutable(__DIR__ . '/../');
    $env->load();

    $api = new Api();

    Token::revalited();

    #Pastas do Artia.
    $folders = (new Departament())->find('FOLDER_ID')->fetch(true);
    #Chamados
    $tickets = (new Ticket())->find()->fetch(true);

    if (!$tickets) {
        $state = 'Nenhuma ticket encontrado no momento.';
        return;
    }

    foreach ($folders as $folder) {
        $api->required([
            'callback' => 'listingActivities',
            'folderId' => (int) $folder->FOLDER_ID
        ])
        ->build()
        ->send();

        #Lista dos id's do Artia
        foreach ($api->getResponse()->data->listingActivities as $activity) {
            $isArtia[] = (int) $activity->id;
        }
    }

    $fp = fopen(__DIR__ . '/../storage/syncDeleted.txt', 'a+');

    if (count($isArtia)) {
        foreach ($tickets as $ticket) {
            if (!in_array((int) $ticket->ID_ARTIA, $isArtia)) {
                $state = '| Deletando chamado ' . $ticket->ID_ARTIA . PHP_EOL;
                fwrite($fp, 'Chamado/Ativadade delatado ' . $ticket->ID_ARTIA . ' ' . date('d-m-Y à\s H:i:s') .  PHP_EOL);
                $find = (new Ticket())->find()->where(['ID_ARTIA' => (int) $ticket->ID_ARTIA])->fetch();
                $find->destroy();
            }
        }
    }

    fclose($fp);

    echo $state;
} catch (InvalidPathException $exception) {
    echo $exception->getMessage();
    exit();
} catch (InvalidFileException $exception) {
    echo $exception->getMessage();
    exit();
}
