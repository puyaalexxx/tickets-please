<?php

namespace App\Http\Controllers\Api\V1;

use App\ApiResponses;
use App\Exceptions\NotAuthorizedToEditException;
use App\Exceptions\NotAuthorizedToEditTicketException;
use App\Exceptions\NotAuthorizedToEditUserException;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
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

    /**
     * @throws NotAuthorizedToEditException
     * @throws AuthorizationException
     */
    public function isAble(string $ability, $targetModel, $type = 'ticket'): bool
    {
        try {
            return (bool)$this->authorize($ability, [$targetModel, $this->policyClass]);
        } catch (AuthorizationException $e) {
            if ($type == 'ticket') {
                throw new NotAuthorizedToEditTicketException('You are not authorized to ' . $ability . ' this ticket.');
            } else if ($type == 'user') {
                //only managers can edit, replace, update, delete a user
                throw new NotAuthorizedToEditUserException('You are not authorized to ' . $ability . ' this user.');
            }

            throw $e;
        }
    }
}
