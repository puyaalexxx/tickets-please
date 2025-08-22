<?php

namespace App\Policies\V1;

use App\Models\User;
use App\Permissions\Abilities;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /*
     * Create policy for the Ticket model.
     */
    public function store(User $user): bool
    {
        return $user->tokenCan(Abilities::CreateUser);
    }

    /*
     * Update policy for the Ticket model.
     */
    public function update(User $user): bool
    {
        return $user->tokenCan(Abilities::UpdateUser);
    }

    /*
     * Replace policy for the Ticket model.
     */
    public function replace(User $user): bool
    {
        return $user->tokenCan(Abilities::ReplaceUser);
    }

    /*
     * Delete policy for the Ticket model.
     */
    public function delete(User $user): bool
    {
        return $user->tokenCan(Abilities::DeleteUser);
    }
}
