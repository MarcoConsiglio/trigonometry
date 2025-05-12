<?php
namespace MarcoConsiglio\Trigonometry\Exceptions;

use Exception;

/**
 * This exception is thrown when a bad format string angle is matched,
 * for example 0°0'123", instead of 0°2'3".
 */
class NoMatchException extends Exception
{
    /**
     * Construct the exception.
     *
     * @param string $angle The string provoking the exception.
     * @return void
     */
    public function __construct(string $angle)
    {
        parent::__construct("$angle does not match an angle measure.", 0, $this->getPrevious());
    }
}