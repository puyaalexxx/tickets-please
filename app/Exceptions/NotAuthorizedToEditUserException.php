<?php

namespace App\Exceptions;

class NotAuthorizedToEditUserException extends NotAuthorizedToEditException
{
    /**
     * Constructor with optional custom message and error details.
     */
    public function __construct(string $message = 'You are not authorized to edit this user.')
    {
        parent::__construct($message);
    }
}
