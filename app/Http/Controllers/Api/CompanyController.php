<?php
namespace App\Http\Controllers\Api;

use App\Enums\MessageCodeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Company\AssignCompanyRequest;
use App\Http\Requests\Api\Company\StoreRequest;
use App\Http\Resources\Company\CompanyResource;
use App\Services\Api\CompanyService;

class CompanyController extends Controller
{
    public function __construct(CompanyService $service)
    {
        $this->service = $service;
    }

    public function store(StoreRequest $request)
    {
        $this->service->attributes = $request->all();

        if ($model = $this->service->store()) {
            return $this->responseSuccess(new CompanyResource($model));
        } else {
            return $this->responseError(trans('_response.failed.400'), MessageCodeEnum::FAILED_TO_STORE);
        }
    }

    public function assignCompany(AssignCompanyRequest $request)
    {
        $this->service->attributes = $request->all();

        if ($this->service->assignCompany()) {
            return $this->responseSuccess(null, trans('_response.success.assign'));
        } else {
            return $this->responseError([
                'message' => trans('_response.failed.400')
            ], 400);
        }
    }
}
