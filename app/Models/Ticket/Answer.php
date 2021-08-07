<?php

namespace App\Models\Ticket;

use App\Layer\Layer;

class Answer extends Layer
{
    protected $table = 'TICKETS_RESPOSTAS';

    protected $prefix = 'TICKET_RESPOSTA';

    /**
     * @param int $id
     * @return null|array
     */
    public function getTicketResponses(int $id): ?array
    {
        return $this->find('TICKETS_RESPOSTAS.*, TICKETS_ANEXOS_RESPOSTAS.TICKET_RESPOSTA, TICKETS_ANEXOS_RESPOSTAS.ENDERECO, Framework_Users.Avatar, Framework_Sectors.Name')
        ->left('TICKETS_ANEXOS_RESPOSTAS', 'TICKETS_ANEXOS_RESPOSTAS.TICKET_RESPOSTA', '=', 'TICKETS_RESPOSTAS.TICKET_RESPOSTA')
        ->left('Framework_Users', 'Framework.Username', '=', 'TICKETS_RESPOSTAS.USUARIO')
        ->left('Framework_Sectors', 'Framework_Sectors.Framework_Sector', '=', 'Framework_Users.Framework_Sector')
        ->where(['TICKETS_RESPOSTAS.TICKET_CHAMADO' => $id])
        ->all();
        /*
            return (new Answer())
            ->find('TICKETS_RESPOSTAS.*, TICKETS_ANEXOS_RESPOSTAS.TICKET_RESPOSTA, TICKETS_ANEXOS_RESPOSTAS.ENDERECO, Framework_Users.Avatar, Framework_Sectors.Name')
            ->join([
                [
                    'JOIN' => 'LEFT JOIN',
                    'TABLE' => 'TICKETS_ANEXOS_RESPOSTAS',
                    'ON' => 'TICKETS_ANEXOS_RESPOSTAS.TICKET_RESPOSTA = TICKETS_RESPOSTAS.TICKET_RESPOSTA'
                ],
                [
                    'JOIN' => 'LEFT JOIN',
                    'TABLE' => 'Framework_Users',
                    'ON' => 'Framework_Users.Username = TICKETS_RESPOSTAS.USUARIO'
                ],
                [
                    'JOIN' => 'LEFT JOIN',
                    'TABLE' => 'Framework_Sectors',
                    'ON' => 'Framework_Sectors.Framework_Sector = Framework_Users.Framework_Sector'
                ]
            ])
            ->where(['TICKETS_RESPOSTAS.TICKET_CHAMADO' => intval($id)])
            ->fetch(true);
        */
    }
}
