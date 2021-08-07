<?php

namespace App\Models\Entity;

use App\Layer\Layer;

class Seller extends Layer
{
    protected $table = 'VENDEDORES';

    protected $prefix = 'VENDEDOR';

    /**
     * @param int $entity
     * @return null|object
     */
    public function sellerByNumber(int $entity): ?object
    {
        return $this->findBy($entity)->first();
        // return (new Seller())->findBy(intval($number))->fetch();
    }
}
