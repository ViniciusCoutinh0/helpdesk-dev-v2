<?php

namespace App\Models\Ticket;

use App\Layer\Layer;

class Category extends Layer
{
    protected $table = 'TICKETS_CATEGORIAS';

    protected $prefix = 'TICKET_CATEGORIA';


    /**
     * @param int $id
     * @return null|object
     */
    public function joinDepartament(int $id): ?object
    {
        return $this->find('TICKETS_CATEGORIAS.NOME AS CATEGORIA_NOME, TICKETS_DEPARTAMENTOS.*')
        ->join('TICKETS_DEPARTAMENTOS', 'TICKETS_DEPARTAMENTOS.TICKET_DEPARTAMENTO', '=', ' TICKETS_CATEGORIAS.TICKET_DEPARTAMENTO')
        ->where(['TICKETS_CATEGORIAS.TICKET_CATEGORIA' => $id])
        ->first();
        // return (new Category())->find('TICKETS_CATEGORIAS.NOME AS CATEGORIA_NOME, TICKETS_DEPARTAMENTOS.*')
        // ->join([
        //     [
        //         'JOIN'  => 'INNER JOIN',
        //         'TABLE' => 'TICKETS_DEPARTAMENTOS',
        //         'ON' => 'TICKETS_DEPARTAMENTOS.TICKET_DEPARTAMENTO = TICKETS_CATEGORIAS.TICKET_DEPARTAMENTO'
        //     ]
        // ])
        // ->where(['TICKETS_CATEGORIAS.TICKET_CATEGORIA' => intval($id)])
        // ->fetch();
    }
}
