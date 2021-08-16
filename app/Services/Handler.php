<?php

namespace App\Services;

use App\Artia\Api;
use Monolog\Logger;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\Answer;
use Monolog\Handler\StreamHandler;

class Handler
{
    /**
     * @param int $id
     * @param array $data
     * @param array $files
     * @return int
    */
    public static function createActivity(int $id, array $data, array $files = []): int
    {
        $logger = new Logger('Helpdesk');
        $logger->pushHandler(new StreamHandler(__DIR__ . env('CONFIG_PATH_LOG') . '/createActivity.txt', Logger::WARNING));

        $description = "Detalhes do Chamado: " . htmlspecialchars_decode($data['title']) . " \r\n";
        $description .= html_entity_decode($data['description']) . "\r\n";
        $description .= "SETOR: {$data['section']} - ";
        $description .= "USUÁRIO HELPDESK: " . mb_convert_case($data['username'], MB_CASE_TITLE, 'UTF-8') . "\r\n";
        $description .= "\r\nMENSAGEM: \r\n";
        $description .= htmlspecialchars_decode($data['message']) . "\r\n \r\n";

        if (count($files)) {
            $description .= "LINK ANEXO(S) ENVIADO(S) PELO USUÁRIO: \r\n";

            foreach ($files['files'] as $file) {
                $description .= defaultUrl() . $file['file_path'] . "\r\n";
            }
        }

        if (isset($data['fields'])) {
            $description .= "\r\nINFORMAÇÕES COMPLEMENTARES*: \r\n";

            foreach ($data['fields'] as $field) {
                $description .= mb_strtoupper($field['FIELD_NAME']) . ": " . mb_convert_case($field['FIELD_VALUE'], MB_CASE_TITLE, 'UTF-8') . "\r\n";
            }
        }

        if ($data['section'] === 'Lojas') {
            $description .= "SOLICITANTE: \r\n";
            $description .= $data['employee_name'] . "\r\n";
            $description .= "ACESSO REMOTO: \r\n";
            $description .= $data['computer'] . "\r\n";
        }

        $api = new Api();
        $api->requireds([
            'title' => "[#$id] " . htmlspecialchars_decode($data['section']) . " " . mb_strtoupper($data['username']) . ": " . htmlspecialchars_decode($data['title']),
            'accountId' => (int) env('CONFIG_API_ACCOUNT_ID'),
            'folderId' =>  (int) $data['folder_id'],
            'description' => filter_var($description, FILTER_SANITIZE_SPECIAL_CHARS),
            'responsibleId' => $data['responsible'],
            'estimatedEnd' => $data['estimated_end'],
            'estimatedEffort' => $data['estimated_effort'],
            'categoryText' => "Chamado Integrado via API",
            'actualStart' => date('Y-m-d'),
            'timeEstimatedStart' => date('H:i')
        ])->createActivity();

        $activity = $api->response();

        if (isset($activity->errors)) {
            $logger->error($activity->errors[0]->message, [
                'id' => $id,
                'data' => $data,
                'object' => $activity
            ]);
        }

        $logger->info('Success Full', ['data' => $data]);
        return (int) $activity->data->createActivity->id;
    }

    /**
     * @param int $id
     * @param string $message
     * @param array $files
     * @return object
    */
    public static function createComment(int $id, string $message, array $files = []): object
    {
        $logger = new Logger('Helpdesk');
        $logger->pushHandler(new StreamHandler(__DIR__ . env('CONFIG_PATH_LOG') . '/createComment.txt', Logger::WARNING));

        $comment = "Integrado \r\n";
        $comment .= "*" . html_entity_decode(trim($message)) . "\r\n \r\n";

        if (count($files)) {
            foreach ($files['files'] as $file) {
                $comment .= defaultUrl() . $file['file_path'] . "\r\n";
            }
        }

        $create = new Api();
        $create->requireds([
            'id' => $id,
            'accountId' => (int) env('CONFIG_API_ACCOUNT_ID'),
            'object' => 'activity',
            'content' => filter_var($comment, FILTER_SANITIZE_SPECIAL_CHARS),
            /* 'createBy' => 'noreply@promofarma.com.br', */
            /* 'users' => ['196653']*/
        ])->createComment();

        $response = $create->response();

        if (isset($response->errors)) {
            $logger->error($response->errors[0]->message, [
                'id' => $id,
                'object' => $response
            ]);
        }

        return $response;
    }

    /**
     * @param int $id
     * @return void
    */
    public static function listingCommentsNotViewed(int $id): void
    {
        $ticket = (new Ticket())->findBy($id)->first();

        $logger = new Logger('Helpdesk');
        $logger->pushHandler(new StreamHandler(__DIR__ . env('CONFIG_PATH_LOG') . '/listingCommentsNotViewed.txt', Logger::WARNING));

        $list = new Api();
        $list->requireds([
            'ids' => [(int) $ticket->ID_ARTIA],
            'accountId' => (int) env('CONFIG_API_ACCOUNT_ID'),
            'type' => 'Activity',
            'viewed' => false
        ])->listingCommentsNotViewed();

        $response = $list->response();

        if (isset($response->errors)) {
            $logger->error($response->errors[0]->message, [
                'id' => $id,
                'object'  => $response
            ]);
        }

        $answer = (new Answer());
        if (count($response->data->listingCommentsNotViewed)) {
            foreach ($response->data->listingCommentsNotViewed as $commit) {
                $identify = explode(' ', $commit->content);
                if (!in_array('Integrado', $identify)) {
                    $answer->TICKET_CHAMADO = (int) $ticket->TICKET_CHAMADO;
                    $answer->USUARIO = mb_convert_case($commit->author->name, MB_CASE_TITLE, 'UTF-8');
                    $answer->SETOR = 'Atendente (Via Artia)';
                    $answer->COMENTARIO = str_replace(';', '</br>', html_entity_decode($commit->content));
                    $answer->save();
                }
            }

            if (is_null($ticket->ATUALIZACAO)) {
                $ticket->ATUALIZACAO = date('Y-m-d H:i:s');
                $ticket->save();
            }

            $logger->info('Sucess Full', [
                'id' => $id,
                'object' => $response
            ]);
        }
    }
}
