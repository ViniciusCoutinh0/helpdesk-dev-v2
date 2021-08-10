<?php

namespace App\Models\Sector;

use App\Layer\Layer;
use App\Models\Entity\User;
use App\Models\Rules\Rules;

class Sector extends Layer
{
    /**
     * Table Name in Database.
     *
     * @var string
    */
    protected $table = 'Framework_Sectors';

    /**
     * Primary Key in table.
     *
     * @var string
    */
    protected $prefix = 'Framework_Sector';


    public function getSectorByUser(User $user): ?object
    {
        return $this->find()->where(['Framework_Sector' => $user->Framework_Sector])->first();
    }

    public function getSectorById(int $id): ?object
    {
        return $this->findBy($id)->first();
    }

    public function getAllSectors(): ?array
    {
        return $this->find()->all();
    }

    public function getAllSectorsAndUser(): ?array
    {
        return $this->find('Framework_Users.Framework_User, Framework_Users.Username, Framework_Sectors.Name Sector, Framework_Sectors.Framework_Sector')
        ->join('Framework_Users', 'Framework_Users.Framework_Sector', '=', 'Framework_Sectors.Framework_Sector')
        ->orderBy('Framework_Sectors.Framework_Sector')
        ->all();
    }
}
