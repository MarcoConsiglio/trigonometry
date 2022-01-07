<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Builders;

use MarcoConsiglio\Trigonometry\Angle;
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
    public function test_cannot_create_with_excess_degrees()
    {
        // Arrange
        /** @var \MarcoConsiglio\Trigonometry\Builders\FromDecimal */
        $builder = $this->getMockedAngleBuilder();
        $this->setAngleBuilderProperties($builder, 360.00001);

        // Assert
        $this->expectException(AngleOverflowException::class);
        $this->expectExceptionMessage("The angle can't be greather than 360°.");
        $builder->checkOverflow();
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