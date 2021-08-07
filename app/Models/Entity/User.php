<?php

namespace App\Models\Entity;

use App\Layer\Layer;

class User extends Layer
{
    /**
     * Table Name in Database.
     *
     * @var string
    */
    protected $table = 'Framework_Users';

    /**
     * Primary Key in table.
     *
     * @var string
    */
    protected $prefix = 'Framework_User';

    /**
     * Hide fields setted by array.
     *
     * @var array
    */
    //protected $hidden = ['Password'];

    public function getUserById(int $id): ?object
    {
        return $this->findBy($id)->first();
    }

    public function getUserByUsername(string $username): ?object
    {
        return $this->find()->where(['Username' => $username])->first();
    }
}
