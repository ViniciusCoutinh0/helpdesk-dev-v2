<?php

namespace App\Models\Ticket;

use App\Layer\Layer;

class AttachmentAnswer extends Layer
{
    protected $table = 'TICKETS_ANEXOS_RESPOSTAS';

    protected $prefix = 'TICKET_ANEXO_RESPOSTA';
}
