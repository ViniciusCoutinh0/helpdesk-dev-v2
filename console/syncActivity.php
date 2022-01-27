<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Artia\Api;
use App\Artia\Token\Token;
use App\Models\Ticket\Attachment;
use App\Models\Ticket\Ticket;
use App\Models\Entity\Entity;

$dot = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dot->load();

Token::loadCacheFile();

$tickets = (new Ticket())->find()->where(['ID_ARTIA' => 0])->all();

if (!$tickets) {
    echo "|SINCRONIZAÇÃO DE CHAMADOS ID_ARTIA = 0" . PHP_EOL;
    echo "|Nenhum chamado fora de sincronização.";
    return;
}


echo "|ID_ARTIA|RESPONSAVEL" . PHP_EOL;

foreach ($tickets as $ticket) {
    $decode = json_decode($ticket->MENSAGEM);

    $description = "Detalhes do Chamado: " . htmlspecialchars_decode($ticket->TITULO) . " \r\n";
    $description .= html_entity_decode($decode->DESCRIPTION) . "\r\n";
    $description .= "SETOR: " . ($ticket->SETOR) . " - ";
    $description .= "USUÁRIO HELPDESK: " . mb_convert_case($ticket->USUARIO, MB_CASE_TITLE, 'UTF-8') . "\r\n";
    $description .= "\r\nMENSAGEM: \r\n";
    $description .= htmlspecialchars_decode($decode->MESSAGE) . "\r\n \r\n";

    if (isset($decode->FIELDS)) {
        $description .= "\r\nINFORMAÇÕES COMPLEMENTARES*: \r\n";
        foreach ($decode->FIELDS as $field) {
            $description .= mb_strtoupper($field->FIELD_NAME) . ": " . mb_convert_case($field->FIELD_VALUE, MB_CASE_TITLE, 'UTF-8') . "\r\n";
        }
    }

    $attachments = (new Attachment())->getAttachmentById($ticket);

    if ($attachments) {
        $description .= "LINK ANEXO(S) ENVIADO(S) PELO USUÁRIO: \r\n";

        foreach ($attachments as $attachment) {
            $description .= defaultUrl() . $attachment->ENDERECO . "\r\n";
        }
    }

    $entity = (new Entity())->getUserByNumber($ticket->RESPONSAVEL_ARTIA);

    $startup = new \DateTime($ticket->INICIALIZACAO);
    $term = new \DateTime($ticket->PRAZO_ARTIA);
    $diff = $startup->diff($term);

    $api = new Api();
    $api->requireds([
        'title' => "[#{$ticket->TICKET_CHAMADO}] (Re-Criado) " . htmlspecialchars_decode($ticket->SETOR) . " " . mb_strtoupper($ticket->USUARIO) . ": " . htmlspecialchars_decode($ticket->TITULO),
        'accountId' => (int) env('CONFIG_API_ACCOUNT_ID'),
        'folderId' =>  (int) $ticket->ID_FOLDER,
        'description' => filter_var($description, FILTER_SANITIZE_SPECIAL_CHARS),
        'responsibleId' => (int) $entity->USUARIO_ARTIA,
        'estimatedEnd' => date('Y-m-d H:i', strtotime('+' . $diff->days . ' days')),
        'estimatedEffort' => (float) $ticket->ESFORCO_ARTIA,
        'categoryText' => "Chamado Integrado via API",
        'actualStart' => date('Y-m-d', strtotime($ticket->INICIALIZACAO)),
        'timeEstimatedStart' => date('H:i', strtotime($ticket->INICIALIZACAO))
    ])->createActivity();


    $response = $api->response();

    if (isset($response->errors)) {
        echo $response->errors[0]->message;
        return;
    }

    $id_artia = $response->data->createActivity->id;

    $ticket->TICKET_CHAMADO = $ticket->TICKET_CHAMADO;
    $ticket->ID_ARTIA = $id_artia;
    $ticket->save();

    echo "|" . $id_artia . "|" . $entity->NOME;
}
