<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Exceptions;

use MarcoConsiglio\Trigonometry\Exceptions\NoMatchException;
use MarcoConsiglio\Trigonometry\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox("A NoMatchException")]
#[CoversClass(NoMatchException::class)]
class NoMatchExceptionTest extends TestCase
{
    #[TestDox("tell you're trying to create an angle from a bad formatted angle string.")]
    public function test_no_match_exception()
    {
        // Arrange
        $message = " does not match an angle measure.";
        $angle = "361°";
        
        // Act
        $exception = new NoMatchException($angle);
        $actual_message = $exception->getMessage();

        // Assert
        $this->assertIsString($actual_message);
        $this->assertEquals($angle.$message, $actual_message);
    }
}