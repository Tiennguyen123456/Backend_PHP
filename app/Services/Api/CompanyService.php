<?php
namespace App\Services\Api;

use App\Models\Company;
use App\Repositories\Company\CompanyRepository;
use App\Services\BaseService;

class CompanyService extends BaseService
{
    public function __construct()
    {
        $this->repo = new CompanyRepository();
    }

    public function store()
    {
        $attrs = [
            'name'              => $this->attributes['name'],
            'contact_email'     => $this->attributes['contact_email'] ?? null,
            'contact_phone'     => $this->attributes['contact_phone'] ?? null,
            'website'           => $this->attributes['website'] ?? null,
            'address'           => $this->attributes['address'] ?? null,
            'city'              => $this->attributes['city'] ?? null,
            'is_default'        => $this->attributes['is_default'] ?? false,
            'limited_users'     => $this->attributes['limited_users'] ?? null,
            'limited_events'    => $this->attributes['limited_events'] ?? null,
            'limited_campaigns' => $this->attributes['limited_campaigns'] ?? null,
            'status'            => $this->attributes['status'] ?? null,
        ];

        if (!isset($this->attributes['id'])) {
            $attrMores = [
                'code'          => $this->attributes['code'],
                'parent_id'     => $this->attributes['parent_id'] ?? null,
                'created_by'    => auth()->user()->id,
                'updated_by'    => auth()->user()->id
            ];
        } else {
            $attrMores = [
                'id'            => $this->attributes['id'],
                'updated_by'    => auth()->user()->id,
            ];
        }

        return $this->storeAs($attrs, $attrMores);
    }

    public function assignCompany()
    {
        $id = $this->attributes['id'];
        $company = $this->find($id);

        if ($company) {
            $parentId = isset($this->attributes['company_id']) ? $this->attributes['company_id'] : null;

            return $this->storeAs([], [
                'id'            => $id,
                'parent_id'     => $parentId
            ]);
        }

        return null;
    }
}
