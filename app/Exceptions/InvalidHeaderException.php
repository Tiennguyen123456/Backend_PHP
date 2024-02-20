<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponser;
use App\Enums\MessageCodeEnum;

class InvalidHeaderException extends Exception
{
    use ApiResponser;

    protected $msgError = ['header' => "Headers is not correct, please check it again."];

    public function render()
    {
        return $this->responseError($this->msgError, MessageCodeEnum::INVALID_HEADER);
    }
}
