<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Get current company from session
     */
    protected function getCurrentCompany()
    {
        return session('current_company');
    }

    /**
     * Get current company ID
     */
    protected function getCurrentCompanyId()
    {
        $company = $this->getCurrentCompany();
        return $company ? $company->id : null;
    }

    /**
     * Check if user can perform action based on company limits
     */
    protected function checkCompanyLimits(string $action): bool
    {
        $company = $this->getCurrentCompany();

        if (!$company) {
            return false;
        }

        switch ($action) {
            case 'create_invoice':
                return $company->canCreateInvoice();

            case 'create_client':
                return $company->canCreateClient();

            case 'create_user':
                return $company->canCreateUser();

            default:
                return true;
        }
    }

    /**
     * Apply tenant scope to query
     */
    protected function applyTenantScope($query)
    {
        $companyId = $this->getCurrentCompanyId();

        if ($companyId) {
            return $query->where('company_id', $companyId);
        }

        return $query;
    }

    /**
     * Set company_id for new records
     */
    protected function setCompanyId(array &$data): void
    {
        $companyId = $this->getCurrentCompanyId();

        if ($companyId && !isset($data['company_id'])) {
            $data['company_id'] = $companyId;
        }
    }
}

