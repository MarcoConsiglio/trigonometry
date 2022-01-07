<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\FromRadiant;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;

/**
 * @testdox The FromRadiant builder
 */
class FromRadiantTest extends BuilderTestCase
{
    /**
     * @testdox can create a positive angle from a radiant value.
     */
    public function test_can_create_positive_angle()
    {
        $this->testAngleCreation(FromRadiant::class);
    }

    /**
     * @testdox can create a negative angle from a radiant value.
     */
    public function test_can_create_negative_angle()
    {
        $this->testAngleCreation(FromRadiant::class, negative: true);
    }

    /**
     * @testdox cannot create an angle with more than +/-360°.
     */
    public function test_exception_if_more_than_360_degrees()
    {
        // Assert
        $this->expectException(AngleOverflowException::class);
        $this->expectExceptionMessage("The angle can't be greater than 360°.");

        // Arrange & Act
        new FromRadiant(Angle::MAX_RADIANT + 0.00001);
    }

    /**
     * @testdox can kill a GreaterThan mutant in the validate method.
     */
    public function test_missing_exception_if_equal_360_degrees()
    {
        // Arrange & Act
        new FromRadiant(Angle::MAX_RADIANT);

        // Assert
        $this->addToAssertionCount(1);
    }

    /**
     * Returns the FromRadiant builder class.
     * @return string
     */
    protected function getBuilderClass(): string
    {
        return FromRadiant::class;
    }
}