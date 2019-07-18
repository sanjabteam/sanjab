<?php

namespace Sanjab\Exceptions;

use Exception;

class CrudTypeNotAllowed extends Exception
{
    public function __toString()
    {
        return "Allowed values as type are 'create' & 'edit'. type is case sensetive.";
    }
}
