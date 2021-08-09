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
     * @param int $id
     * @return null|object
    */
    public function getUserById(int $id): ?object
    {
        return $this->findBy($id)->first();
    }

    /**
     * @param string $username
     * @return null|object
    */
    public function getUserByUsername(string $username): ?object
    {
        return $this->find()->where(['Username' => $username])->first();
    }

    /**
     * @param int $id
     * @param string $password
     * @return bool
    */
    public function updatePasswordByUserId(int $id, string $password): bool
    {
        $user = $this->findBy($id)->first();
        $user->Framework_User = $id;
        $user->Password = password_hash($password, PASSWORD_DEFAULT);
        $user->Pending_Password = 'S';
        return $user->save();
    }
}
