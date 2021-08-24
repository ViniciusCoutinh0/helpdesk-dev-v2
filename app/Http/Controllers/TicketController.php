<?php

namespace App\Http\Controllers;

use App\Common\View;
use App\Common\Upload;
use App\Common\Message;
use App\Services\Handler;
use App\Models\Entity\User;
use App\Models\Ticket\Ticket;
use App\Models\Sector\Sector;
use App\Models\Ticket\Answer;
use App\Models\Ticket\SubCategory;
use App\Models\Ticket\Attachment;
use App\Models\Ticket\Category;
use App\Models\Ticket\Departament;

class TicketController extends Ticket
{
    /**
     * @var \App\Common\View $view
    */
    private $view;

    /**
     * @var \App\Common\Message $message
    */
    private $message;

    public function __construct()
    {
        $this->message = new Message();
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
            'title' => trim(input()->post('title')->getValue()),
            'worlds' => trim(input()->post('words')->getValue()),
            'message' => trim(clearEmoji(input()->post('message')->getValue())),
            'subcategory' => null
        ];

        if (input()->exists('section')) {
            $required['section'] = input()->post('section')->getValue();
            $required['user_id'] = (int) Session()->USER_ID;
        }

        if (input()->exists('section_user')) {
            $index = explode(':', input()->post('section_user')->getValue());
            $required['section'] = $index[0];
            $required['user_id'] = (int) $index[1];
        }

        if (input()->exists('subcategory')) {
            $required['subcategory'] = (int) input()->post('subcategory')->getValue();
        }

        if (in_array('', $required)) {
            $this->message->error('Existem campos obrigátorios em branco, por favor preencha todos os campos.');
            $this->viewStore();
            return;
        }

        $required = array_map('clearHtml', $required);

        $subcategory = (new SubCategory())->getSubCategoryById($required['subcategory']);
        $category = (new Category())->getCategoryBySubCategory($subcategory);
        $departament = (new Departament())->getDepartmentByCategoryId($category);

        $user = (new User())->getUserById($required['user_id']);

        if (!$user) {
            redirect(url('app.home'));
            return;
        }

        $data = [
            'username' => mb_strtolower($user->Username),
            'computer' => 'Não Informado Pelo Cliente.',
            'employee_name' => 'Não Identificado(a).',
            'employee_number' => 0,
            'description' => mb_convert_case($subcategory->DESCRICAO, MB_CASE_TITLE, 'utf-8'),
            'departament' => mb_convert_case($departament->NOME, MB_CASE_TITLE, 'utf-8'),
            'category' => mb_convert_case($category->NOME, MB_CASE_TITLE, 'utf-8'),
            'subcategory' => mb_convert_case($subcategory->NOME, MB_CASE_TITLE, 'utf-8'),
            'folder_id' => (int) $departament->FOLDER_ID,
            'responsible_id' => (int) $subcategory->USUARIO,
            'responsible' => (int) $subcategory->USUARIO_ARTIA,
            'estimated_effort' => floatval($subcategory->ESFORCO),
            'on_duty' => 'N',
            'estimated_end' => date('Y-m-d H:i', strtotime('+' . (int) $subcategory->PRAZO_ESTIMADO . ' day'))
        ];

        if (input()->exists('computer')) {
            $data['computer'] = clearHtml(input()->post('computer')->getValue());
        }

        if (input()->exists('employee')) {
            $explode = explode(' ', clearHtml(input()->post('employee')->getValue()));
            $data['employee_name'] = mb_convert_case("{$explode[2]} {$explode[3]}", MB_CASE_TITLE, 'utf-8');
            $data['employee_number'] = (int) $explode[0];
        }

        if (input()->exists('on_duty')) {
            $data['on_duty'] = (input()->post('on_duty')->getValue() == 'on' ? 'S' : 'N');
        }

        $fields = (new SubCategory())->fieldsById($subcategory->TICKET_SUB_CATEGORIA);

        if ($fields) {
            foreach ($fields as $field) {
                if ($field-> ATIVO === 'S') {
                    $name = str_replace(' ', '_', mb_strtolower($field->NOME));
                    $value = input()->post($name)->getValue();

                    $data['fields'][] = [
                        'FIELD_NAME' => mb_convert_case(trim($field->DESCRICAO_CAMPO), MB_CASE_TITLE, 'utf-8'),
                        'FIELD_VALUE' => clearHtml(trim($value))
                    ];
                }
            }
        }

        $files = Upload::move(input()->file('attachment'), $this->isValid);

        if (isset($files['validation'])) {
            $this->message->error($files['validation']);
            $this->viewStore();
            return;
        }

        $merge = array_merge($required, $data);
        $create = $this->createTicket($merge, $files);

        if (!$create) {
            $this->message->error('Falha ao enviar as informações do chamado, por favor tente novamente');
            $this->viewStore();
            return;
        }

        $id_artia = Handler::createActivity($create, $merge, $files);
        $this->updateArtiaIdByTicketId($create, $id_artia);

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

        Handler::createComment($ticket->ID_ARTIA, $message, $files);
        redirect(url('ticket.show', ['id' => $id]));
    }
}
