<?php

namespace App\Http\Controllers;

use App\Core\Upload;
use App\Common\View;
use App\Models\User\User;
use App\Traits\Verify;

class AccountController extends User
{
    use Verify;

    /** @var $view View */
    private $view;

    public function __construct($router)
    {
        $this->view = new View();
        $this->view->addData([
            'router' => $router,
            'user' => (Verify::isUserId() ? (new User())->findBy((Session()->USER_ID))->fetch() : null)
        ]);
    }

    public function viewAccount(array $data): void
    {
        echo $this->view->render('account', [
            'data' => $data,
            'logged' => Verify::isLogged(),
        ]);
    }

    public function viewChangePassword(array $data, string $message = null): void
    {
        echo $this->view->render('account/changePassword', [
            'data' => $data,
            'logged' => Verify::isLogged(),
            'message' => $message
        ]);
    }

    public function changePassword(array $request): void
    {
        if (mb_strlen($request['newPassword']) <= 3) {
            $message = 'Sua nova senha deve conter mais de 4 caracteres.';
            $this->viewChangePassword($request, $message);
            return;
        }

        if ($request['newPassword'] != $request['confirmPassword']) {
            $message = 'Confirmação da senha não confere com sua nova senha.';
            $this->viewChangePassword($request, $message);
            return;
        }

        $change = $this->changePass((int) Session()->USER_ID, [
            'oldPassword' => strip_tags(trim($request['oldPassword'])),
            'newPassword' => strip_tags(trim($request['newPassword']))
        ]);

        if (!$change) {
            $message = 'Não foi possivel alterar a senha, por favor tente novamente.';
            $this->viewChangePassword($request, $message);
            return;
        }

        $message = 'Senha alterada com Sucesso!';
        $this->viewChangePassword($request, $message);
    }

    public function viewChangeAvatar(array $data, string $message = null): void
    {
        echo $this->view->render('account/changeAvatar', [
            'data' => $data,
            'logged' => Verify::isLogged(),
            'message' => $message
        ]);
    }

    public function changeAvatar(array $data): void
    {
        if ($_FILES['newImage']['error'] == UPLOAD_ERR_NO_FILE) {
            $message = 'Nenhuma imagem encontrada para ser enviada.';
        } elseif (empty($_FILES['newImage']['type'])) {
            $message = 'Tipo de arquivo não permitido.';
        } else {
            try {
                $upload = new Upload('image');
                $url = $upload->path('upload/avatar')
                    ->size(120)
                    ->user(Session()->USERNAME)
                    ->image($_FILES['newImage']);

                $change = (new User())->changeImage(Session()->USER_ID, $url);
                if ($change == 'invalid_user') {
                    $message = 'Falha ao buscar o usuário solicitado, tente novamente.';
                }

                $message = 'Avatar alterado com sucesso.';
            } catch (\Exception $exception) {
                $message = $exception->getMessage();
            }
        }
        $this->viewChangeAvatar($data, $message);
    }
}
