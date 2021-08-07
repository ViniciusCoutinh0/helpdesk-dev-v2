<?php

namespace App\Http\Controllers;

use App\Common\View;
use App\Models\Entity\User;
use App\Models\Ticket\Ticket;

class AppController extends User
{
    private $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function home(): void
    {
        if (Session()->has('USER_ID') === false) {
            echo $this->view->render('home');
            return;
        }

        $user = (new User())->getUserById((int) Session()->USER_ID);
        $open = (new Ticket())->getTicketsByUsernameAndState($user);
        $closed = (new Ticket())->getTicketsByUsernameAndState($user, 2);

        echo $this->view->render('home', compact('user', 'open', 'closed'));
    }
}
