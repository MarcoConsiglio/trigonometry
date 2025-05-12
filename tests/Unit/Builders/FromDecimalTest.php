<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\FromDecimal;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;

#[TestDox("The FromDecimal builder")]
#[CoversClass(FromDecimal::class)]
#[UsesClass(Angle::class)]
#[UsesClass(AngleOverflowException::class)]
class FromDecimalTest extends BuilderTestCase
{
    #[TestDox("can create a positive angle from a decimal value.")]
    public function test_can_create_positive_angle()
    {
        $this->testAngleCreation(FromDecimal::class);
    }

    #[TestDox("can create a negative angle from a decimal value.")]
    public function test_can_create_negative_angle()
    {
        $this->testAngleCreation(FromDecimal::class, negative: true);
    }

    #[TestDox("cannot create an angle with more than +/-360°.")]
    public function test_exception_if_greater_than_360_degrees()
    {    
        // Assert
        $this->expectException(AngleOverflowException::class);
        $this->expectExceptionMessage("The angle can't be greather than 360°.");

        // Arrange & Act
        new FromDecimal(360.00001);
    }

    #[TestDox("can create an angle of exact 360°.")]
    public function test_missing_exception_if_equal_to_360_degrees()
    {
        // Arrange & Act
        new FromDecimal(360);
        
        // Assert missing exception
        $this->addToAssertionCount(1);
    }

    /**
     * Returns the FromDecimal builder class.
     * @return string
     */
    protected function getBuilderClass(): string
    {
        return FromDecimal::class;
    }
}