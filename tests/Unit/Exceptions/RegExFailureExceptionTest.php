<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Exceptions;

use MarcoConsiglio\Trigonometry\Exceptions\RegExFailureException;
use MarcoConsiglio\Trigonometry\Tests\TestCase;

class RegExFailureExceptionTest extends TestCase
{
    public function test_regex_failure_exception()
    {
        // Arrange
        $message = "Oh my God! Something is wrong!";
        // Act
        $exception = new RegExFailureException($message);

        // Assert
        $this->assertEquals($message, $exception->getMessage());
    }
}