<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\FromDegrees;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;

#[TestDox("The FromDegrees builder")]
#[CoversClass(FromDegrees::class)]
#[UsesClass(AngleOverflowException::class)]
#[UsesClass(Angle::class)]
class FromDegreesTest extends BuilderTestCase
{
    #[TestDox("can create an angle from a degrees values.")]
    public function test_can_create_an_angle()
    {
        $this->testAngleCreation(FromDegrees::class);
    }

    #[TestDox("cannot build an angle with more than 360°.")]
    public function test_exception_if_more_than_360_degrees()
    {
        // Assert
        $this->expectException(AngleOverflowException::class);
        $this->expectExceptionMessage("The angle degrees can't be greater than 360°.");
        
        // Arrange & Act
        new FromDegrees(361, 0, 0);
    }

    #[TestDox("cannot build an angle with more than 59'.")]
    public function test_exception_if_more_than_59_minutes()
    {
        // Assert
        $this->expectException(AngleOverflowException::class);
        $this->expectExceptionMessage("The angle minutes can't be greater than 59'.");

        // Arrange & Act
        new FromDegrees(0, 60, 0);
    }

    #[TestDox("cannot build an angle with 60\" or more.")]
    public function test_exception_if_equal_or_more_than_60_seconds()
    {
        // Arrange
        /** @var \MarcoConsiglio\Trigonometry\Builders\FromDegrees */
        
        // Assert
        $this->expectException(AngleOverflowException::class);
        $this->expectExceptionMessage("The angle seconds can't be greater than or equal to 60\".");
        
        // Act
        new FromDegrees(0, 0, 60);
    }
    
    #[TestDox("can create an angle of exact 360°.")]
    public function test_missing_exception_if_equal_to_360_degrees()
    {
        // Arrange & Act
        new FromDegrees(360, 0, 0);
        
        // Assert missing exception
        $this->addToAssertionCount(1);
    }

    /**
     * Returns the FromDegrees builder class.
     * @return string
     */
    protected function getBuilderClass(): string
    {
        return FromDegrees::class;
    }
}