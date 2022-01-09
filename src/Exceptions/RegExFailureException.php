<?php
namespace MarcoConsiglio\Trigonometry\Exceptions;

use Exception;

/**
 * This exception is thrown when the angle regex fails to find a string angle.
 */
class RegExFailureException extends Exception
{
    /**
     * Constructs the exception.
     *
     * @param string $failure_message
     * @return void
     */
    public function __construct(string $failure_message = "")
    {
        parent::__construct($failure_message, 0, $this->getPrevious());
    }
}