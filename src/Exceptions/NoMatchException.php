<?php
namespace MarcoConsiglio\Trigonometry\Exceptions;

use Exception;

/**
 * This exception is thrown when non string angle is matched.
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