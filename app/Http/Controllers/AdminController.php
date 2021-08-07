<?php

namespace App\Http\Controllers;

use App\Core\Mail;
use App\Common\View;
use App\Models\Admin\Admin;
use App\Models\Rules\Rules;
use App\Models\Sector\Sector;
use App\Models\User\User;
use App\Traits\Verify;
use League\Csv\Writer;

class AdminController extends Admin
{
    use Verify;

    /** @var $view App\Common\View */
    private $view;

    public function __construct($router)
    {
        $this->view = new View();
        $this->view->addData([
            'router' => $router,
            'logged' => Verify::isLogged(),
            'user' =>  (Verify::isUserId() ? (new User())->findBy((Session()->USER_ID))->fetch() : null)
        ]);
    }

    /**
     * Lista todos os usuários cadastrados no banco de dados.
     *
     * @param array $data
     * @return void
     */
    public function listUsers(array $data): void
    {
        echo $this->view->render('admin/listingUser', [
            'data' => $data,
            'users' => (new Admin())->listAllUsers()
        ]);
    }

    /**
     * Lista todos os setores cadastrados no banco de dados.
     *
     * @param array $data
     * @return void
     */
    public function listSector(array $data): void
    {
        echo $this->view->render('admin/listingSector', [
            'data' => $data,
            'sectors' => (new Admin())->listAllSectors()
        ]);
    }

    /**
     * Renderiza a página de Adicionar usuário.
     *
     * @param array $data
     * @param string $message
     * @param mixed $cache
     * @return void
     */
    public function viewAddUser(array $data, string $message = null): void
    {
        echo $this->view->render('admin/addUser', [
            'data' => $data,
            'sectors' => (new Sector())->getAllSector(),
            'message' => $message
        ]);
    }

    /**
     * @param array $request
     * @return void
     */
    public function addUser(array $request): void
    {
        $required = [
            'name'      => htmlentities(strip_tags($request['name']), ENT_QUOTES, 'UTF-8'),
            'username'  => htmlentities(strip_tags($request['username']), ENT_QUOTES, 'UTF-8'),
            'password'  => htmlentities(strip_tags($request['password']), ENT_QUOTES, 'UTF-8'),
            'email'     => htmlentities(strip_tags($request['email']), ENT_QUOTES, 'UTF-8'),
            'sector'    => (int) $request['sector']
        ];

        if (!Verify::validationFields($required)) {
            $message = 'Existem campos em branco, por favor preencha todos os campos.';
            $this->viewAddSector($required, $message);
            return;
        }

        if ($_REQUEST && !csrfVerifry($_REQUEST)) {
            $message = 'Requisição inválida por favor tente novamente.';
            $this->viewAddUser([], $message);
            return;
        }

        if (filter_var($required['username'], FILTER_VALIDATE_EMAIL)) {
            $message = 'Nome de usuário não pode ser um endereço de e-mail.';
            $this->viewAddUser($required, $message);
            return;
        }

        if (mb_strlen($required['password']) <= 3) {
            $message = 'Senha deve conter mais de 4 caracteres.';
            $this->viewAddUser($required, $message);
            return;
        }

        $create = $this->registerUser($required);

        if (!$create) {
            $message = 'Nome de Usuário/Email já cadastrado no sistema.';
            $this->viewAddUser($required, $message);
            return;
        }

        $message = 'Usuário ' . $required['username'] . ' cadastrado com sucesso!';
        $this->viewAddUser([], $message);
    }

    /**
     * Renderiza página para Editar um usuário.
     *
     * @param array $data
     * @param string $message
     * @return void
     */
    public function viewUpdateUser(array $data, string $message = null): void
    {
        echo $this->view->render('admin/updateUser', [
            'data' => filter_var_array($data, FILTER_SANITIZE_SPECIAL_CHARS),
            'load' => (new User())->findBy(intval($data['user_id']))->fetch(),
            'sectors' => (new Sector())->getAllSector(),
            'message' => $message
        ]);
    }

    /**
     * Edita um usuário por completo.
     *
     * @param array $data
     * @return void
     */
    public function updateDataUser(array $request): void
    {
        $required = [
            'name'       => htmlentities(strip_tags($request['name']), ENT_QUOTES, 'UTF-8'),
            'username'   => htmlentities(strip_tags($request['username']), ENT_QUOTES, 'UTF-8'),
            'email'      => htmlentities(strip_tags($request['email']), ENT_QUOTES, 'UTF-8'),
            'sector'     => (int) $request['sector'],
            'state'      => htmlentities(strip_tags($request['state']), ENT_QUOTES, 'UTF-8'),
            'user_id'    => (int)$request['user_id']
        ];

        if (!Verify::validationFields($required)) {
            $message = 'Existem campos em branco por favor preencha todos os campos.';
            $this->viewUpdateUser($request, $message);
            return;
        }

        if (!filter_var($required['email'], FILTER_VALIDATE_EMAIL)) {
            $message = 'Endereço de e-mail inválido.';
            $this->viewUpdateUser($request, $message);
            return;
        }

        if ($_REQUEST && !csrfVerifry($_REQUEST)) {
            $message = 'Requisição inválida por favor tente novamente.';
            $this->viewUpdateUser($request, $message);
            return;
        }

        if (!empty($request['password'])) {
            $newRequired = $required + [
                'password' => htmlentities(strip_tags($request['password']), ENT_QUOTES, 'UTF-8')
            ];

            ob_start();
            include __DIR__ . '/../../../storage/layout/resetpassword.html';
            $view = ob_get_clean();
            $mail = new Mail();
            $replace = str_replace(['{username}', '{newpassword}'], [mb_convert_case($required['username'], MB_CASE_TITLE, 'UTF-8'), $newRequired['password']], $view);
            $mail->bootstrap('Sua nova senha - Helpdesk Promofarma', $replace, $required['email'], mb_convert_case($required['username'], MB_CASE_TITLE, 'UTF-8'));
            $mail->send();
            ob_end_flush();
        }

        $update = $this->updateUser(($newRequired ?? $required), (new User()));

        if (!$update) {
            $message = 'Não foi possível atualizar o usuário, por favor tente novamente.';
            $this->viewUpdateUser($request, $message);
            return;
        }

        $message = 'O Usuário ' . $required['username'] . ' atualizado com sucesso!';
        $this->viewUpdateUser($required, $message);
    }

    /**
     * Renderiza a página para adicionar Setor.
     *
     * @param array $data
     * @param string $message
     * @param mixed $cache
     * @return void
     */
    public function viewAddSector(array $data, string $message = null): void
    {
        echo $this->view->render('admin/addSector', [
            'data' => $data,
            'message' => $message
        ]);
    }

    /**
     * Adiciona um Setor no banco de dados.
     *
     * @param array $data
     * @return void
     */
    public function addSector(array $request): void
    {
        $required = [
            'sector' => htmlentities(strip_tags(mb_convert_case($request['sector'], MB_CASE_TITLE)), ENT_QUOTES, 'UTF-8'),
            'create' => (isset($request['create']) == 'on' ? 'S' : 'N'),
            'read'   => (isset($request['read']) == 'on' ? 'S' : 'N'),
            'update' => (isset($request['update']) == 'on' ? 'S' : 'N'),
            'delete' => (isset($request['delete']) == 'on' ? 'S' : 'N')
        ];

        if (!Verify::validationFields($required)) {
            $message = 'Existem campos em branco por favor preencha todos os campos.';
            $this->viewAddSector($required, $message);
            return;
        }

        if ($_REQUEST && !csrfVerifry($_REQUEST)) {
            $message = 'Requisição inválida por favor tente novamente.';
            $this->viewAddSector($required, $message);
            return;
        }

        $create = $this->registrySector($required);

        if (!$create) {
            $message = 'Nome do setor já cadastrado no sistema.';
            $this->viewAddSector($required, $message);
            return;
        }

        $message = 'Setor ' . $required['sector'] . ' cadastrado com sucesso!';
        $this->viewAddSector($required, $message);
    }

    /**
     * Renderiza a página de atualizar um Setor.
     *
     * @param array $data
     * @param string $message
     * @return void
     */
    public function viewUpdateSector(array $data, string $message = null): void
    {
        echo $this->view->render('admin/updateSector', [
            'data' => $data,
            'load' => (new Admin())->getSectorJoinRules($data['sector_id']),
            'message' => $message
        ]);
    }

    /**
     * Atualiza um setor no banco de dados.
     *
     * @param array $data
     * @return void
     */
    public function updateDataSector(array $request): void
    {
        $required = [
            'sector' => htmlentities(strip_tags(mb_convert_case($request['sector'], MB_CASE_TITLE)), ENT_QUOTES, 'UTF-8'),
            'create' => (isset($request['create']) == 'on' ? 'S' : 'N'),
            'read'   => (isset($request['read']) == 'on' ? 'S' : 'N'),
            'update' => (isset($request['update']) == 'on' ? 'S' : 'N'),
            'delete' => (isset($request['delete']) == 'on' ? 'S' : 'N'),
            'sector_id' => (int) $request['sector_id']
        ];

        if (!Verify::validationFields($required)) {
            $message = 'Existem campos em branco por favor preencha todos os campos.';
            $this->viewUpdateSector($required, $message);
            return;
        }

        if ($_REQUEST && !csrfVerifry($_REQUEST)) {
            $message = 'Requisição inválida por favor tente novamente.';
            $this->viewUpdateSector($required, $message);
            return;
        }

        $update = $this->updateSector($required, (new Sector()), (new Rules()));

        if (!$update) {
            $message = 'Não foi possível atualizado o setor por favor tente novamente.';
            $this->viewUpdateSector($required, $message);
            return;
        }

        $message = 'O Setor ' . $required['sector'] . ' atualizado com sucesso!';
        $this->viewUpdateSector($required, $message);
    }

    /**
     * Renderiza a página de Relatorios de Chamados
     *
     * @param array $data
     * @param string $message
     * @param array $results
     * @return void
    */
    public function reportTickets(array $data, string $message = null, $results = []): void
    {
        echo $this->view->render('admin/report', [
            'data' => $data,
            'message' => $message,
            'tickets' => $results
        ]);
    }

    /**
     * Retorna as informações pesquisa com lista.
     *
     * @param array $data
     * @param string $message
     * @return void
    */
    public function reportTicketsSend(array $data, string $message = null): void
    {
        $postFields = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        if ($postFields) {
            $listing = (new Admin())->getTicketsByPeriod($postFields['first_day'], $postFields['last_day']);

            if (!$listing) {
                $message = 'Nenhum chamado encontrado no periodo de ' . dateFormat($postFields['first_day'])->format('d-m-Y') . ' à ' . dateFormat($postFields['last_day'])->format('d-m-Y');
            }

            $this->reportTickets($data, $message, $listing);
        }
    }

    public function generateCsv(array $data): void
    {
        if (!empty($data)) {
            $tickets = (new Admin())->getTicketsByPeriod($data['first'], $data['last']);
            if ($tickets) {
                $write = Writer::createFromString('');
                $write->setDelimiter(';');
                $write->insertOne([
                    'Protocolo',
                    'ID Artia',
                    'Departamento',
                    'Categoria',
                    'Sub Categoria',
                    'Atendente',
                    'Cliente',
                    utf8_decode('Inicialização'),
                    utf8_decode('Data de Criação'),
                    utf8_decode('Data de Finalização'),
                    utf8_decode('Horas de Abertura Até Finalização'),
                    utf8_decode('Horas de Abertura Até Primeira Resposta'),
                    utf8_decode('Plantão'),
                    utf8_decode('Chamado Rápido')
                ]);

                foreach ($tickets as $ticket) {
                    $init = dateFormat($ticket->INICIALIZACAO);
                    $finish = dateFormat($ticket->FINALIZACAO_ARTIA);
                    $timeCommit = dateFormat($ticket->ATUALIZACAO);
                    $interval = [
                        'diff_Commit' => $init->diff($finish),
                        'diff_Finish' => $init->diff($timeCommit)
                    ];

                    $line[] = [
                        $ticket->TICKET_CHAMADO,
                        $ticket->ID_ARTIA,
                        utf8_decode($ticket->DEPARTAMENTO),
                        utf8_decode($ticket->CATEGORIA),
                        utf8_decode($ticket->SUB_CATEGORIA),
                        $ticket->PROCFIT_USUARIO,
                        mb_convert_case($ticket->USUARIO, MB_CASE_TITLE, 'UTF-8'),
                        dateFormat($ticket->INICIALIZACAO)->format('d/m/Y H:i:s'),
                        dateFormat($ticket->INICIALIZACAO)->format('d/m/Y H:i:s'),
                        ($ticket->FINALIZACAO_ARTIA ? dateFormat($ticket->FINALIZACAO_ARTIA)->format('d/m/Y H:i:s') : ''),
                        (!is_null($ticket->FINALIZACAO_ARTIA) ? $interval['diff_Commit']->h . ':' . $interval['diff_Commit']->i . ':' . $interval['diff_Commit']->s  : ''),
                        (!is_null($ticket->ATUALIZACAO) ? $interval['diff_Finish']->h . ':' . $interval['diff_Finish']->i . ':' . $interval['diff_Finish']->s : ''),
                        ($ticket->PLANTAO == 'S' ? 'Sim' : utf8_decode('Não')),
                        ($ticket->CHAMADO_RAPIDO == 'S' ? 'Sim' : utf8_decode('Não'))
                    ];
                }

                $write->insertAll($line);
                $write->output('chamados_' . dateFormat()->format('d-m-Y') . '.csv');
            }
        }
    }
}
