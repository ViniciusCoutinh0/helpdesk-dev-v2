<?php

namespace App\Traits;

trait Verify
{

    /**
     * @param array $fields
     * @return bool
    */
    public static function validationFields(array $fields): bool
    {
        if (in_array('', $fields)) {
            return false;
        }
        return true;
    }
}
