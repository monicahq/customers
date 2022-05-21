<?php

namespace App\Exceptions;

use RuntimeException;

class PastDueLicenceException extends RuntimeException
{
    /**
     * Create a new exception instance.
     *
     * @param  string  $message
     * @return void
     */
    public function __construct($message = 'Licence key has expired.')
    {
        parent::__construct($message);
    }
}
