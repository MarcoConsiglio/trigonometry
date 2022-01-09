<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Builders;

use MarcoConsiglio\Trigonometry\Builders\FromDecimal;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;

/**
 * @testdox The FromDecimal builder
 */
class FromDecimalTest extends BuilderTestCase
{
    /**
     * @testdox can create a positive angle from a decimal value.
     */
    public function test_can_create_positive_angle()
    {
        $this->testAngleCreation(FromDecimal::class);
    }

    /**
     * @testdox can create a negative angle from a decimal value.
     */
    public function test_can_create_negative_angle()
    {
        $this->testAngleCreation(FromDecimal::class, negative: true);
    }

    /**
     * @testdox cannot create an angle with more than +/-360°.
     */
    public function test_exception_if_greater_than_360_degrees()
    {    
        // Assert
        $this->expectException(AngleOverflowException::class);
        $this->expectExceptionMessage("The angle can't be greather than 360°.");

        // Arrange & Act
        new FromDecimal(360.00001);
    }

    /**
     * @testdox can kill a GreaterThan mutant in the validate method.
     */
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