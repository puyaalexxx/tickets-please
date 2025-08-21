<?php

namespace App\Http\Controllers\Api\V1;

use App\ApiResponses;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApiController extends Controller
{
    use ApiResponses, AuthorizesRequests;

    /**
     * Determine if the request should include the author relationship.
     */
    protected function include(string $relationship): bool
    {
        $param = request()->get('include');

        if (!isset($param)) {
            return false;
        }

        $includeValues = explode(',', strtolower($param));

        return in_array(strtolower($relationship), $includeValues);
    }

    public function isAble(string $ability, $targetModel): Response
    {
        return $this->authorize($ability, [$targetModel, $this->policyClass]);
    }
}
