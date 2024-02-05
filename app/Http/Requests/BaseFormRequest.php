<?php

namespace App\Http\Requests;

use App\Rules\TableHasColumn;
use App\Rules\TableHasId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Traits\ApiResponser;

class BaseFormRequest extends FormRequest
{
    use ApiResponser;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $msgErrors = (new ValidationException($validator))->errors();

            throw new HttpResponseException(
                $this->responseError($msgErrors, 422, 'VALIDATION_ERROR'),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        parent::failedValidation($validator);
    }

    protected function tableHasId($tableName)
    {
        return new TableHasId($tableName);
    }

    protected function tableHasColumn($tableName)
    {
        return new TableHasColumn($tableName);
    }
}
