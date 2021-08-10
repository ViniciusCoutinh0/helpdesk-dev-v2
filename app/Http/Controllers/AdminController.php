<?php

namespace App\Http\Controllers;

use App\Common\View;
use App\Common\Message;
use App\Models\Entity\User;
use App\Models\Sector\Sector;
use App\Models\Ticket\Ticket;
use League\Csv\Writer;

class AdminController extends User
{
    /**
     * @var App\Common\View
    */
    private $view;

    /**
     * @var App\Common\Message
    */
    private $message;

    public function __construct()
    {
        $this->view = new View();
        $this->message = new Message();
    }

    public function viewCreateReport(int $id, array $data = []): void
    {
        $user = $this->getUserById((int) $id);
        $sector = (new Sector())->getSectorByUser($user);
        $message = $this->message;

        echo $this->view->render('admin/report', compact('user', 'sector', 'message', 'data'));
    }

    public function createReport(int $id): void
    {
        $required = [
            'first' => input()->post('first_day')->getValue(),
            'last'  => input()->post('last_day')->getValue()
        ];

        $required = array_map('clearHtml', $required);

        if (in_array('', $required)) {
            $this->message->error('Existem campos em branco, por favor preencha todos os campos');
            $this->viewCreateReport($id);
            return;
        }

        $data = (new Ticket())->getAllTicketsByBetween($required['first'], $required['last']);

        if (!$data) {
            $this->message->error('Nenhum chamado encontrado entre as datas de ' . date('d/m', strtotime($required['first'])) . ' à ' . date('d/m', strtotime($required['last'])));
            $this->viewCreateReport($id);
            return;
        }

        $this->message->success('Arquivo gerado com sucesso! Total de chamados no periódo selecionado: ' . count($data));
        $this->viewCreateReport($id, $data);
    }

    public function outputReport(string $first, string $last): void
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Description: File Transfer');

        $data = (new Ticket())->getAllTicketsByBetween($first, $last);

        if(!count($data)) {
            return;
        }

        $csv = Writer::createFromString('');
        $csv->setDelimiter(';');
        $header = [
            'Protocolo', 'ID Artia', 'Departamento', 'Categoria', 'Sub Categoria',
            'Atendente/Responsavel', 'Cliente/Solicitante', 'Inicialização',
            'Data de Criação', 'Data de Finalização', 'Horas de Abertura até a Finalização',
            'Horas da Abertura até a Primeira Iteração', 'Plantão'
        ];
        
        $csv->insertOne($header);

        $lines = [];
        foreach($data as $ticket) {
            $startup = new \DateTime($ticket->INICIALIZACAO);
            $completion = new \DateTime($ticket->FINALIZACAO_ARTIA);
            $firstCommit = new \DateTime($ticket->ATUALIZACAO);

            $intervals = [
                'finish' => $startup->diff($completion),
                'commit' => $startup->diff($firstCommit)
            ];

            $lines[] = [
                $ticket->TICKET_CHAMADO, $ticket->ID_ARTIA, $ticket->DEPARTAMENTO,
                $ticket->CATEGORIA, $ticket->SUB_CATEGORIA, mb_convert_case($ticket->USUARIO_PROC, MB_CASE_TITLE, 'UTF-8'),
                $ticket->USUARIO, date('d/m/Y H:i:s', strtotime($ticket->INICIALIZACAO)), date('d/m/Y H:i:s', strtotime($ticket->INICIALIZACAO)),
                ($ticket->FINALIZACAO_ARTIA ? date('d/m/Y H:i:s', strtotime($ticket->FINALIZACAO_ARTIA)) : ''), 
                (!is_null($ticket->FINALIZACAO_ARTIA) ? $intervals['finish']->h . ':' . $intervals['finish']->i . ':' . $intervals['finish']->s : ''),
                (!is_null($ticket->ATUALIZACAO) ? $intervals['commit']->h . ':' . $intervals['commit']->i . ':' . $intervals['commit']->s : ''),
                ($ticket->PLANTAO === 'S' ? 'SIM' : 'NÃO')
            ];

        }
        $csv->insertAll($lines);
        $csv->output('Relatório_Chamados.csv');
        die();
    }
}
