<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Exceptions;

use MarcoConsiglio\Trigonometry\Exceptions\RegExFailureException;
use MarcoConsiglio\Trigonometry\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox("The RegExFailureException")]
#[CoversClass(RegExFailureException::class)]
class RegExFailureExceptionTest extends TestCase
{
    #[TestDox("has a message which explain the regular expression failure.")]
    public function test_regex_failure_exception()
    {
        // Arrange
        $message = "Oh my God! Something went wrong!";
        
        // Act
        $exception = new RegExFailureException($message);

        // Assert
        $this->assertEquals($message, $exception->getMessage());
    }
}