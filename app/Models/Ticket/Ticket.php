<?php

namespace App\Models\Ticket;

use App\Artia\Api;
use App\Layer\Instance\Db;
use App\Layer\Layer;
use App\Models\Entity\User;

class Ticket extends Layer
{
    /**
     * Table name in Database
     *
     * @var string
    */
    protected $table = 'TICKETS_CHAMADOS';

    /**
     * Primary Key
     *
     * @var string
    */
    protected $prefix = 'TICKET_CHAMADO';

    /**
     * Files valid for upload
     *
     * @var array
    */
    protected $isValid = [
        'image/jpeg',
        'image/png',
        'application/pdf',
        'application/vnd.ms-excel',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];


    /**
     * @param App\Models\Entity\User $user
     * @param mixed $top
     * @param int $state
     * @return null|array
    */
    public function getTicketsByUsernameAndState(User $user, $top = 8, int $state = 1): ?array
    {
        $limit = ($top ? "TOP {$top} *" : "*");
        return $this->find($limit)->where(['USUARIO' => $user->Username, 'ESTADO' => $state])->all();
    }

    /**
     * @param int $id
     * @return null|object
    */
    public function getTicketById(int $id): ?object
    {
        return $this->findBy($id, 'TICKETS_CHAMADOS.*, Framework_Users.*, USUARIOS.NOME AS PROC_NOME')
        ->join('USUARIOS', 'USUARIOS.USUARIO', '=', 'TICKETS_CHAMADOS.RESPONSAVEL_ARTIA')
        ->join('Framework_Users', 'Framework_Users.Username', '=', 'TICKETS_CHAMADOS.USUARIO')
        ->first();
    }

    /**
     * @param array $data
     * @param array $files
     * @return null|int
    */
    public function createTicket(array $data, array $files = []): ?int
    {
        $subcategory = (new SubCategory())->findBy((int) $data['subcategory'])->first();
        $category = (new Category())->joinDepartament((int) $subcategory->TICKET_CATEGORIA);

        $encode = [
            'MESSAGE' => html_entity_decode($data['message']),
            'DESCRIPTION' => mb_convert_case($subcategory->DESCRICAO, MB_CASE_TITLE, 'UTF-8')
        ];

        if (isset($data['fields'])) {
            $encode['FIELDS'] = $data['fields'];
        }

        $column = (new Ticket());
        $column->USUARIO = $data['username'];
        $column->SETOR = $data['section'];
        $column->TITULO = html_entity_decode($data['title']);
        $column->MENSAGEM = json_encode($encode, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $column->NUMERO_BALCONISTA = $data['employee_number'];
        $column->NOME_BALCONISTA = $data['employee_name'];
        $column->COMPUTADOR = $data['computer'];
        $column->DEPARTAMENTO = mb_convert_case($category->NOME, MB_CASE_TITLE, 'UTF-8');
        $column->CATEGORIA =  mb_convert_case(trim($category->CATEGORIA_NOME), MB_CASE_TITLE, 'UTF-8');
        $column->SUB_CATEGORIA = mb_convert_case(trim($subcategory->NOME), MB_CASE_TITLE, 'UTF-8');
        $column->ID_ARTIA = 0; // UPDATE AFTER
        $column->ID_FOLDER = (int) $category->FOLDER_ID;
        $column->RESPONSAVEL_ARTIA = (int) $subcategory->USUARIO;
        $column->ESFORCO_ARTIA = floatval($subcategory->ESFORCO);
        $column->PLANTAO = $data['on_duty'];
        $column->CHAMADO_RAPIDO = 'D';
        $column->PRAZO_ARTIA = date('Y-m-d H:i:s', strtotime('+' . (int) $subcategory->PRAZO_ESTIMADO . ' day'));

        if ($column->save()) {
            $id = Db::getInstance()->lastInsertId();

            if (count($files)) {
                $attachment = (new Attachment());

                foreach ($files['files'] as $file) {
                    $attachment->TICKET_CHAMADO = $id;
                    $attachment->USUARIO = $data['username'];
                    $attachment->ENDERECO = $file['file_path'];
                    $attachment->save();
                }
            }

            return $id;
        }
        return null;
    }

    public function getAllTicketsByBetween(string $first, string $last): ?array
    {
        return $this->find('TICKETS_CHAMADOS.*, USUARIOS.NOME USUARIO_PROC')
        ->join('USUARIOS', 'USUARIOS.USUARIO', '=', 'TICKETS_CHAMADOS.RESPONSAVEL_ARTIA')
        ->orWhere('CONVERT(DATE, TICKETS_CHAMADOS.INICIALIZACAO)', 'BETWEEN', "'{$first}' AND '{$last}'")->all();
    }
}
