<?php
declare(strict_types=1);


namespace App\Services;

use App\Exceptions\TicketNotFoundException;
use App\Models\Ticket;

class TicketService
{
    /**
     * @throws TicketNotFoundException
     */
    public function findTicketForUserOrFail(int $ticketId, int $userId): Ticket
    {
        //we need to check id the author id matches the user_id of the ticket
        /*$ticket = Ticket::where('id', $ticket_id)
            ->where('user_id', $author_id)
            ->first();*/
        //use this instead of the above one to throw a not authorized exception first
        $ticket = Ticket::find($ticketId);

        if (!$ticket) {
            throw new TicketNotFoundException("Ticket ID $ticketId not found.");
        }

        if ($ticket->user_id != $userId) {
            throw new TicketNotFoundException("Ticket ID $ticketId does not belong to user ID $userId.");
        }

        return $ticket;
    }
}
