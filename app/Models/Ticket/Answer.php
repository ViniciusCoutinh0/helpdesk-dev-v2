<?php

namespace App\Models\Ticket;

use App\Layer\Layer;

class Answer extends Layer
{
    /**
     * Table name in Database
     *
     * @var string
    */
    protected $table = 'TICKETS_RESPOSTAS';

    /**
     * Primary Key
     *
     * @var string
    */
    protected $prefix = 'TICKET_RESPOSTA';

    /**
     * @param App\Models\Ticket $ticket
     * @return null|array
     */
    public function getTicketResponses(Ticket $ticket): ?array
    {
        return $this->find('TICKETS_RESPOSTAS.*, TICKETS_ANEXOS_RESPOSTAS.TICKET_RESPOSTA, TICKETS_ANEXOS_RESPOSTAS.ENDERECO, Framework_Users.Avatar, Framework_Sectors.Name')
        ->left('TICKETS_ANEXOS_RESPOSTAS', 'TICKETS_ANEXOS_RESPOSTAS.TICKET_RESPOSTA', '=', 'TICKETS_RESPOSTAS.TICKET_RESPOSTA')
        ->left('Framework_Users', 'Framework_Users.Username', '=', 'TICKETS_RESPOSTAS.USUARIO')
        ->left('Framework_Sectors', 'Framework_Sectors.Framework_Sector', '=', 'Framework_Users.Framework_Sector')
        ->where(['TICKETS_RESPOSTAS.TICKET_CHAMADO' => $ticket->TICKET_CHAMADO])
        ->all();
    }
}
