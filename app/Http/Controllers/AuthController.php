<?php

namespace App\Http\Controllers;

use App\Common\View;
use App\Models\Entity\User;
use App\Traits\Verify;

class AuthController extends User
{
    use Verify;

    /** @var \App\Common\View */
    private $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function viewLogin(array $data, string $message = null): void
    {
        echo $this->view->render('home', [
            'data' => $data,
            'message' => $message
        ]);
    }

    public function signIn(): void
    {
        $required = [
            'username' => htmlentities(strip_tags(input()->find('username')->getValue()), ENT_QUOTES, 'UTF-8'),
            'password' => htmlentities(strip_tags(input()->find('password')->getValue()), ENT_QUOTES, 'UTF-8')
        ];

        if (!Verify::validationFields($required)) {
            $message = 'Existem campos em branco por favor preencha todos os campos.';
            $this->viewLogin($required, $message);
            return;
        }

        $user = $this->getUserByUsername($required['username']);

        if (!$user) {
            $message = 'Usuário/Senha inválido ou não cadastrado.';
            $this->viewLogin($required, $message);
            return;
        }

        if (!password_verify(trim($required['password']), $user->Password)) {
            $message = 'Usuário/Senha inválido ou não cadastrado.';
            $this->viewLogin($required, $message);
            return;
        }

        if ($user->State === 'N') {
            $message = 'Usuário inativo por favor entre contato com Suporte T.i.';
            $this->viewLogin($required, $message);
            return;
        }

        Session()
        ->set('USERNAME', trim($required['username']))
        ->set('USER_ID', (int) $user->Framework_User);

        $user->Status = 1;
        $user->save();

        // if ($user->Pending_Password === 'S') {
        //     redirect("/account/user/{$user->Framework_User}/change/password", 200);
        //     return;
        // }

        redirect(url('app.home'), 200);
    }

    public function signOut(): void
    {
        $user = $this->getUserByUsername(Session()->USERNAME);

        if (!$user || !Session()->has('USERNAME')) {
            redirect("/helpdesk-dev/");
            return;
        }

        Session()->unset('USERNAME')->unset('USER_ID')->destroy();

        $user->Status = 2;
        $user->save();

        redirect(url('app.home'), 200);
    }
}
