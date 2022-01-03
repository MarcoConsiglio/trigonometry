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
     * @param string $subject_string The subject string provoking the exception.
     */
    public function __construct(string $subject_string)
    {
        parent::__construct("The string '$subject_string' does not match an angle measure.", 0, $this->getPrevious());
    }
}