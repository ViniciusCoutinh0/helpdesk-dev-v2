<?php

namespace App\Models\Ticket;

use App\Layer\Layer;

class Attachment extends Layer
{
    protected $table = 'TICKETS_ANEXOS';

    protected $prefix = 'TICKET_ANEXO';

    /**
     * @param int $id
     * @return null|array
     */
    public function getAttachmentById(int $id): ?array
    {
        return $this->find()->where(['TICKET_CHAMADO' => $id])->all();
    }
}
