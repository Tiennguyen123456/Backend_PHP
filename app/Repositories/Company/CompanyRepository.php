<?php
namespace App\Repositories\Company;

use App\Repositories\Repository;

class CompanyRepository extends Repository implements CompanyRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Company::class;
    }

    public function addFilterCompanyQuery($query)
    {
        $user = $this->user();

        if (!$user->is_admin) {
            $query = $query->where('id', $user->company_id);
        }

        return $query;
    }
}
