<?php

namespace App\Exceptions;

use Exception;

class TicketNotFoundException extends Exception
{
    /**
     * Constructor with optional custom message and error details.
     */
    public function __construct(string $message = 'Ticket not found')
    {
        parent::__construct($message);
    }
}
