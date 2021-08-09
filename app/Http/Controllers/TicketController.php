<?php

namespace App\Http\Controllers;

use App\Common\Upload;
use App\Common\View;
use App\Models\Entity\User;
use App\Models\Ticket\Ticket;
use App\Models\Sector\Sector;
use App\Models\Ticket\Answer;
use App\Models\Ticket\SubCategory;
use App\Models\Ticket\Attachment;

class TicketController extends Ticket
{
    /**
     * @var \App\Common\View
    */
    private $view;

    /**
     * @var string
    */
    private $message;

    public function __construct()
    {
        $this->view = new View();
    }

    public function show(int $id): void
    {
        $user = (new User())->getUserById((int) Session()->USER_ID);
        $ticket = (new Ticket())->getTicketById($id);
        $commits = (new Answer())->getTicketResponses($ticket);
        $attachments = (new Attachment())->getAttachmentById($ticket);

        echo $this->view->render('ticket', [
            'user' => $user,
            'ticket' => $ticket,
            'commits' => $commits,
            'attachments' => $attachments,
            'message' => $this->message
        ]);
    }

    public function viewStore(): void
    {
        $user = (new User())->getUserById((int) Session()->USER_ID);

        echo $this->view->render('create', [
            'user' => $user,
            'sector' => (new Sector())->getSectorByUser($user),
            'sectors' => (new Sector())->getAllSectorsAndUser(),
            'message' => $this->message,
        ]);
    }

    public function store(): void
    {
        $required = [
            'words' => input()->post('words')->getValue(),
            'title' => input()->post('title')->getValue(),
            'section' => (input()->exists('section') ? input()->post('section')->getValue() : null),
            'user_id' => Session()->USER_ID,
            'message' => input()->post('message')->getValue()
        ];

        if (input()->exists('section_user')) {
            $explode = explode(':', input()->post('section_user')->getValue());
            $required['section'] = $explode[0];
            $required['user_id'] = $explode[1];
        }

        $required = array_map('clearHtml', $required);

        if (in_array('', $required)) {
            $this->message = 'Existem campos obrigatórios em branco, por favor preencha todos os campos.';
            $this->viewStore();
            return;
        }

        $subcategoryId = input()->post('subcategory')->getValue();

        if (empty($subcategoryId)) {
            $this->message = 'Selecione um assunto para continuar.';
            $this->viewStore();
            return;
        }

        $user = (new User())->getUserById((int) $required['user_id']);

        if (!$user) {
            redirect($_ENV['CONFIG_APP_PATH']);
            return;
        }

        $data = [
            'username' => $user->Username,
            'computer' => 'Não informado pelo Cliente',
            'on_duty' => 'N',
            'subcategory' => (int) $subcategoryId,
            'employee_name' => 'Não identificado(a)',
            'employee_number' => 0,
        ];

        if (input()->exists('computer')) {
            $data['computer'] = input()->post('computer')->getValue();
        }

        if (input()->exists('employee')) {
            $str = explode(' ', input()->post('employee')->getValue());
            $data['employee_number'] = (int) $str[0];
            $data['employee_name'] = "{$str[2]} {$str[3]}";
        }

        if (input()->exists('on_duty')) {
            $data['on_duty'] = (input()->post('on_duty') == 'on' ? 'S' : 'N');
        }

        $fields = (new SubCategory())->fieldsById((int) $subcategoryId);

        if ($fields) {
            foreach ($fields as $field) {
                $name = str_replace(' ', '_', mb_strtolower($field->NOME));

                if (input()->exists($name)) {
                    $value = input()->post($name)->getValue();
                }

                $data['fields'][] = [
                    'FIELD_NAME' => mb_convert_case($field->DESCRICAO_CAMPO, MB_CASE_TITLE, 'UTF-8'),
                    'FIELD_VALUE' => clearHtml($value)
                ];
            }
        }

        $files = Upload::move(input()->file('attachment'), $this->isValid);

        if (isset($files['validation'])) {
            $this->message = $files['validation'];
            $this->viewStore();
            return;
        }

        $merge = array_merge($required, $data);

        $create = $this->createTicket($merge, $files);
        if (!$create) {
            $this->message = 'Falha ao enviar as informações do chamado por favor tente novamente';
            $this->viewStore();
            return;
        }

        redirect(url('ticket.show', ['id' => $create]));
    }

    public function commitStore(int $id): void
    {
        $message = clearHtml(input()->post('message')->getValue());

        if (empty($message)) {
            $this->message = 'Existem campos em branco por favor preencha todos os campos';
            $this->show($id);
            return;
        }

        $ticket = (new Ticket())->getTicketById($id);

        if (!$ticket) {
            redirect(url('app.home'));
            return;
        }

        $files = Upload::move(input()->file('files'), $this->isValid);

        if (isset($files['validation'])) {
            $this->message = $files['validation'];
            $this->show($id);
            return;
        }

        $create = (new Answer())->createCommit($ticket, $message, $files);

        if (!$create) {
            $this->message = 'Falha ao enviar a resposta, por favor tente novamente';
            $this->show($id);
            return;
        }

        redirect(url('ticket.show', ['id' => $id]));
    }
}
