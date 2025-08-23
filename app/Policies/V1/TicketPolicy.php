<?php

namespace App\Policies\V1;

use App\Models\Ticket;
use App\Models\User;
use App\Permissions\Abilities;

class TicketPolicy
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
        return $user->tokenCan(Abilities::CreateTicket) || $user->tokenCan(Abilities::CreateOwnTicket);
    }

    /*
     * Update policy for the Ticket model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::UpdateTicket)) {
            return true;
        } elseif ($user->tokenCan(Abilities::UpdateOwnTicket)) {
            return $user->id === $ticket->user_id;
        }

        return false;
    }

    /*
     * Replace policy for the Ticket model.
     */
    public function replace(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::ReplaceTicket)) {
            return true;
        } elseif ($user->tokenCan(Abilities::ReplaceOwnTicket)) {
            return $user->id === $ticket->user_id;
        }
    }

    /*
     * Delete policy for the Ticket model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        if ($user->tokenCan(Abilities::DeleteTicket)) {
            return true;
        } elseif ($user->tokenCan(Abilities::DeleteOwnTicket)) {
            return $user->id === $ticket->user_id;
        }

        return false;
    }
}
