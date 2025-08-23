<?php

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    /**
     * Constructor with optional custom message and error details.
     */
    public function __construct(string $message = 'User not found')
    {
        parent::__construct($message);
    }
}
