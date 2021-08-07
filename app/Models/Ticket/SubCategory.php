<?php

namespace App\Models\Ticket;

use App\Layer\Layer;

class SubCategory extends Layer
{
    protected $table = 'TICKETS_SUB_CATEGORIAS';

    protected $prefix = 'TICKET_SUB_CATEGORIA';

    /**
     * @param int $id
     * @return null|array
     */
    public function fieldsById(int $id): ?array
    {
        return $this->find()
        ->join('TICKETS_CAMPOS_PERSONALIZADOS', 'TICKETS_CAMPOS_PERSONALIZADOS.TICKET_SUB_CATEGORIA', '=', 'TICKETS_SUB_CATEGORIAS.TICKET_SUB_CATEGORIA')
        ->where(['TICKETS_SUB_CATEGORIAS.TICKET_SUB_CATEGORIA' => $id])
        ->all();
    }
}
