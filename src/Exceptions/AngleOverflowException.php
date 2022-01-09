<?php
namespace MarcoConsiglio\Trigonometry\Exceptions;

use Exception;

/**
 * This exception is thrown when the client code 
 * tries to create an angle that exceeds 360°.
 */
class AngleOverflowException extends Exception
{
    /**
     * Default constructor.
     * @return void
     */
    public function __construct(string $message)
    {
        parent::__construct($message, 0, $this->getPrevious());
    }
}