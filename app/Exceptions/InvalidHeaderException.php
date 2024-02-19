<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponser;

class InvalidHeaderException extends Exception
{
    use ApiResponser;

    protected $msgError = ['header' => "Headers is not correct, please check it again."];

    public function render()
    {
        return $this->responseError($this->msgError, 'INVALID_HEADER');
    }
}
