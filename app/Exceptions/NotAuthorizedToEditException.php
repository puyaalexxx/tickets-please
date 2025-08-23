<?php

namespace App\Exceptions;

use Exception;

class NotAuthorizedToEditException extends Exception
{
    /**
     * Constructor with optional custom message and error details.
     */
    public function __construct(string $message = 'You are not authorized to edit this resource.')
    {
        parent::__construct($message);
    }
}
