<?php

namespace App\Http\Controllers;

use App\Common\Upload;
use App\Common\View;
use App\Services\Handler;
use App\Models\Entity\User;
use App\Models\Ticket\Ticket;
use App\Models\Sector\Sector;
use App\Models\Ticket\Answer;
use App\Models\Ticket\SubCategory;
use App\Models\Ticket\Attachment;
use App\Models\Ticket\Category;

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

        if (!$ticket) {
            redirect(url('app.home'));
            return;
        }

        Handler::listingCommentsNotViewed($ticket->TICKET_CHAMADO);

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
            'section' => (input()->exists('section') ? html_entity_decode(input()->post('section')->getValue()) : null),
            'user_id' => Session()->USER_ID,
            'message' => input()->post('message')->getValue()
        ];

        if (input()->exists('section_user')) {
            $explode = explode(':', input()->post('section_user')->getValue());
            $required['section'] = html_entity_decode($explode[0]);
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

        $subcategory = (new SubCategory())->findBy($subcategoryId)->first();
        $category = (new Category())->joinDepartament((int) $subcategory->TICKET_CATEGORIA);

        $user = (new User())->getUserById((int) $required['user_id']);

        if (!$user) {
            redirect(url('app.home'));
            return;
        }

        $data = [
            'username' => $user->Username,
            'computer' => 'Não informado pelo Cliente',
            'employee_name' => 'Não identificado(a)',
            'employee_number' => 0,
            'description' => mb_convert_case($subcategory->DESCRICAO, MB_CASE_TITLE, 'UTF-8'),
            'departament' => mb_convert_case($category->NOME, MB_CASE_TITLE, 'UTF-8'),
            'category' => mb_convert_case(trim($category->CATEGORIA_NOME), MB_CASE_TITLE, 'UTF-8'),
            'subcategory' => mb_convert_case(trim($subcategory->NOME), MB_CASE_TITLE, 'UTF-8'),
            'folder_id' => (int) $category->FOLDER_ID,
            'responsible_id' => (int) $subcategory->USUARIO,
            'responsible' => (int) $subcategory->USUARIO_ARTIA,
            'estimated_effort' => floatval($subcategory->ESFORCO),
            'on_duty' => 'N',
            'estimated_end' => date('Y-m-d H:i', strtotime('+' . (int) $subcategory->PRAZO_ESTIMADO . ' day'))
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
                    'FIELD_NAME' => mb_convert_case(trim($field->DESCRICAO_CAMPO), MB_CASE_TITLE, 'UTF-8'),
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

        $data = array_merge($required, $data);

        $id = $this->createTicket($data, $files);

        $activity = Handler::createActivity($id, $data, $files);

        if (!$id) {
            $this->message = 'Falha ao enviar as informações do chamado por favor tente novamente';
            $this->viewStore();
            return;
        }

        $update = (new Ticket())->findBy($id)->first();
        $update->TICKET_CHAMADO = $id;
        $update->ID_ARTIA = $activity;
        $update->save();

        redirect(url('ticket.show', ['id' => $id]));
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

        Handler::createComment($ticket->ID_ARTIA, $message, $files);
        redirect(url('ticket.show', ['id' => $id]));
    }
}
