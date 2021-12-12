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
     */
    public function __construct()
    {
        parent::__construct("The angle can't be major than 360°.", 0, $this->getPrevious());
    }
}