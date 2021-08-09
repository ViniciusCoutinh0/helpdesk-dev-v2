<?php

namespace App\Http\Controllers;

use App\Common\View;
use App\Common\Message;
use App\Models\Entity\User;
use App\Models\Sector\Sector;
use App\Models\Ticket\Ticket;

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

    public function viewCreateReport(int $id): void
    {
        $user = $this->getUserById((int) $id);
        $sector = (new Sector())->getSectorByUser($user);
        echo $this->view->render('admin/report', [
            'user' => $user,
            'sector' => $sector,
            'message' => $this->message
        ]);
    }

    public function createReport(int $id): void
    {
        $required = [
            'first_day' => input()->post('first_day')->getValue(),
            'last_day' => input()->post('last_day')->getValue()
        ];

        if (in_array('', $required)) {
            $this->message->error('Existem campos em branco, por favor preencha todos os campos');
            $this->viewCreateReport($id);
            return;
        }


        $between = (new Ticket())->getAllTicketsByBetween($required['first_day'], $required['last_day']);

        if (!$between) {
            $this->message->error('Nenhum chamado encontrado entre as data de ' . $required['first_day'] . '/' . $required['last_day']);
            $this->viewCreateReport($id);
            return;
        }

        $this->message->success('Arquivo gerado com sucesso');
        $this->viewCreateReport($id);
    }
}
