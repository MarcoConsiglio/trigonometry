<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Exceptions;

use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;
use MarcoConsiglio\Trigonometry\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox("The AngleOverflowException")]
#[CoversClass(AngleOverflowException::class)]
class AngleOverflowExceptionTest extends TestCase
{
    #[TestDox("has a message which explain you're trying to create an angle with more than 360°.")]
    public function test_angle_overflow_exception()
    {
        // Arrange
        $message = "Oh my God! Something went wrong!";

        // Act
        $exception = new AngleOverflowException($message);
        $actual_message = $exception->getMessage();

        // Assert
        $this->assertIsString($actual_message);
        $this->assertEquals($message, $actual_message);
    }
}