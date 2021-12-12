<?php
namespace MarcoConsiglio\Trigonometry\Exceptions;

use Exception;

class RegExFailureException extends Exception
{
    public function __construct(string $failure_message)
    {
        parent::__construct($failure_message, 0, $this->getPrevious());
    }
}