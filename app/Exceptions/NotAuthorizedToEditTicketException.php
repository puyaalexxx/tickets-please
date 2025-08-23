<?php

namespace App\Exceptions;

class NotAuthorizedToEditTicketException extends NotAuthorizedToEditException
{
    /**
     * Constructor with optional custom message and error details.
     */
    public function __construct(string $message = 'You are not authorized to edit this ticket.')
    {
        parent::__construct($message);
    }
}
