<?php

namespace App\Http\Controllers;

use App\Common\View;
use App\Common\Message;
use App\Models\Entity\User;
use App\Models\Sector\Sector;
use App\Models\Ticket\Ticket;
use App\Core\Mail;
use App\Models\Rules\Rules;
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

        $user = (new User())->getUserById((int) Session()->USER_ID);
        $sector = (new Sector())->getSectorByUser($user);
        $this->message = new Message();

        $this->view->addData(compact('user', 'sector'));
    }

    public function listUsers(): void
    {
        $listAll = (new User())->getAllUser();

        echo $this->view->render('admin/listingUser', compact('listAll'));
    }

    public function listSections(): void
    {
        $sectors = (new Sector())->getAllSectors();

        echo $this->view->render('admin/listingSector', compact('sectors'));
    }

    public function viewCreateUser(): void
    {
        $sectors = (new Sector())->getAllSectors();
        $message = $this->message;

        echo $this->view->render('admin/adduser', compact('sectors', 'message'));
    }

    public function createUser(): void
    {
        $required = input()->all();
        array_shift($required);

        $required = array_map('clearHtml', $required);

        if (in_array('', $required)) {
            $this->message->error('Existem campos em branco, por favor preencha todos os campos');
            $this->viewCreateUser();
            return;
        }

        $username = $this->find()->where(['Username' => $required['username']])->count();
        $email = $this->find()->where(['Email' => $required['email']])->count();

        if ($username) {
            $this->message->error('Username já cadastrado no sistema');
            $this->viewCreateUser();
            return;
        }

        if ($email) {
            $this->message->error('Endereço de E-mail já cadastrado no sistema');
            $this->viewCreateUser();
            return;
        }

        if (!filter_var($required['email'], FILTER_VALIDATE_EMAIL)) {
            $this->message->error('Endereço de E-mail formato inválido');
            $this->viewCreateUser();
            return;
        }

        $create = $this->createUserByData($required);

        if (!$create) {
            $this->message->error('Não foi possivel criar o novo usuário');
            $this->viewCreateUser();
            return;
        }

        $this->message->success('Novo usuário ' . $required['username'] . ' criado com sucesso');
        clearCache($required);
        $this->viewCreateUser();
    }

    public function viewCreateReport(array $data = []): void
    {
        $message = $this->message;
        echo $this->view->render('admin/report', compact('data', 'message'));
    }


    public function createReport(): void
    {
        $required = input()->all();
        array_shift($required);
        $required = array_map('clearHtml', $required);

        if (in_array('', $required)) {
            $this->message->error('Existem campos em branco, por favor preencha todos os campos');
            $this->viewCreateReport();
            return;
        }

        $data = (new Ticket())->getAllTicketsByBetween($required['first_day'], $required['last_day']);

        if (!$data) {
            $this->message->error('Nenhum chamado encontrado entre as datas de ' . date('d/m', strtotime($required['first_day'])) . ' à ' . date('d/m', strtotime($required['last_day'])));
            $this->viewCreateReport();
            return;
        }

        $this->message->success('Arquivo gerado com sucesso! Total de chamados no periódo selecionado: ' . count($data));
        $this->viewCreateReport($data);
    }

    public function viewUpdateUser(int $id): void
    {
        $currentId = (new User())->getUserById($id);
        $sectors = (new Sector())->getAllSectors();
        $message = $this->message;

        echo $this->view->render('admin/updateUser', compact('currentId', 'sectors', 'message'));
    }

    public function updateUser(int $id): void
    {
        $required = [
            'email' => input()->post('email')->getValue(),
            'sector' => input()->post('sector')->getValue()
        ];

        if (in_array('', $required)) {
            $this->message->error('Existem campos obrigatórios em branco por favor preencha todos os campos');
            $this->viewUpdateUser($id);
            return;
        }

        if (!filter_var($required['email'], FILTER_VALIDATE_EMAIL)) {
            $this->message->error('Endereço de E-mail formato inválido');
            $this->viewUpdateUser($id);
            return;
        }

        $required['user_id'] = (int) $id;
        $required['name'] = clearHtml(input()->post('name')->getValue());
        $required['username'] = clearHtml(input()->post('username')->getValue());
        $required['password'] = clearHtml(input()->post('password')->getValue());

        if (!empty($required['password'])) {
            ob_start();
            include __DIR__ . '/../../../storage/layout/resetpassword.html';
            $view = ob_get_clean();
            $view = str_replace(['{username}', '{newpassword}'], [mb_strtoupper($required['username']), $required['password']], $view);

            $mail = new Mail();
            $mail->bootstrap('Sua nova senha em HelpDesk Promofarma', $view, $required['email'], $required['name']);
            $mail->send();
            ob_end_flush();
        }

        $update = $this->updateUserByData($required);

        if (!$update) {
            $this->message->error('Não foi possivel editar o usuário, por favor tente novamente');
            $this->viewUpdateUser($id);
            return;
        }

        $this->message->success('Usuário editado com sucesso');
        $this->viewUpdateUser($id);
    }

    public function outputReport(string $first, string $last): void
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Description: File Transfer');

        $data = (new Ticket())->getAllTicketsByBetween($first, $last);

        if (!count($data)) {
            return;
        }

        $csv = Writer::createFromString('');
        $csv->setDelimiter(';');
        $header = [
            'Protocolo', 'ID Artia', 'Departamento', 'Categoria', 'Sub Categoria',
            'Atendente/Responsavel', 'Cliente/Solicitante', utf8_decode('Inicialização'),
            utf8_decode('Data de Criação'), utf8_decode('Data de Finalização'), utf8_decode('Horas de Abertura até a Finalização'),
            utf8_decode('Horas da Abertura até a Primeira Iteração'), utf8_decode('Plantão')
        ];

        $csv->insertOne($header);

        $lines = [];
        foreach ($data as $ticket) {
            $startup = new \DateTime($ticket->INICIALIZACAO);
            $completion = new \DateTime($ticket->FINALIZACAO_ARTIA);
            $firstCommit = new \DateTime($ticket->ATUALIZACAO);

            $intervals = [
                'finish' => $startup->diff($completion),
                'commit' => $startup->diff($firstCommit)
            ];

            $lines[] = [
                $ticket->TICKET_CHAMADO, $ticket->ID_ARTIA, utf8_decode($ticket->DEPARTAMENTO),
                utf8_decode($ticket->CATEGORIA), utf8_decode($ticket->SUB_CATEGORIA), mb_convert_case($ticket->USUARIO_PROC, MB_CASE_TITLE, 'UTF-8'),
                $ticket->USUARIO, date('d/m/Y H:i:s', strtotime($ticket->INICIALIZACAO)), date('d/m/Y H:i:s', strtotime($ticket->INICIALIZACAO)),
                ($ticket->FINALIZACAO_ARTIA ? date('d/m/Y H:i:s', strtotime($ticket->FINALIZACAO_ARTIA)) : ''),
                (!is_null($ticket->FINALIZACAO_ARTIA) ? $intervals['finish']->h . ':' . $intervals['finish']->i . ':' . $intervals['finish']->s : ''),
                (!is_null($ticket->ATUALIZACAO) ? $intervals['commit']->h . ':' . $intervals['commit']->i . ':' . $intervals['commit']->s : ''),
                ($ticket->PLANTAO === 'S' ? 'SIM' : utf8_decode('NÃO'))
            ];
        }

        $csv->insertAll($lines);
        $csv->output('Relatório_Chamados.csv');
        die();
    }

    public function viewCreateSector(): void
    {
        $message = $this->message;

        echo $this->view->render('admin/addSector', compact('message'));
    }

    public function createSector(): void
    { 
        $required['rule_read']   = 'N';
        $required['rule_create'] = 'N';
        $required['rule_update'] = 'N';
        $required['rule_delete'] = 'N';

        foreach($required as $key => $value) {
            if(input()->exists($key)) {
                $required[$key] = (input()->post($key)->getValue() === 'on' ? 'S' : 'N');
            }
        }

        $required['name'] = mb_convert_case(input()->post('name')->getValue(), MB_CASE_TITLE, 'UTF-8');
        $required = array_map('clearHtml', $required);
        
        if(in_array('', $required)) {
            $this->message->error('Existem campos em branco, por favor preencha todos os campos');
            $this->viewCreateSector();
            return;
        }

        $sector = (new Sector())->find()->where(['Name' => $required['name']])->count();

        if($sector) {
            $this->message->error('Nome de setor já cadastrado');
            $this->viewCreateSector();
            return;
        }

        $create = (new Sector())->store($required);

        if(!$create) {
            $this->message->error('Não foi possivel criar um novo setor');
            $this->viewCreateSector();
            return;
        }

        $this->message->success('Novo setor cadastrado com sucesso');
        $this->viewCreateSector();
    }

    public function viewUpdateSector(int $id): void
    {
        $currentSector = (new Sector())->findBy($id)->first();
        $rule = (new Rules())->getRulesBySector($currentSector);

        if(!$currentSector) {
            redirect(url('app.home'));
            return;
        }

        $message = $this->message;
        echo $this->view->render('admin/updateSector', compact('currentSector', 'rule', 'message'));
    }

    public function updateSector(int $id): void
    {
        $required['id'] = $id;
        $required['name'] = mb_convert_case(input()->post('name')->getValue(), MB_CASE_TITLE, 'UTF-8');
        $checkbox = $this->checkbox(['rule_read', 'rule_create', 'rule_update', 'rule_delete']);
            
        if(empty($required)) {
            $this->message->error('Existem campos em branco, por favor preencha todos os campos');
            $this->viewUpdateSector($id);
            return;
        }

        $update = (new Sector())->updateByParam(array_merge($checkbox, $required));

        if(!$update) {
            $this->message->error('Existem campos em branco, por favor preencha todos os campos');
            $this->viewUpdateSector($id);
            return;
        }   

        $this->message->success('Setor alterado com sucesso');
        $this->viewUpdateSector($id);

    }

    private function checkbox(array $data): array
    {
        $modified = [];
        foreach($data as $key) {
            if (input()->exists($key)) {
                $modified[$key] = (input()->post($key)->getValue() === 'on' ? 'S' : 'N');
            } else {
                $modified[$key] = 'N';
            }
        }
        return $modified;   
    }
}
