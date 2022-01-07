<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\FromDegrees;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;

/**
 * @testdox The FromDegrees builder
 */
class FromDegreesTest extends BuilderTestCase
{
    /**
     * @testdox can create an angle from a degrees values.
     */
    public function test_can_create_an_angle()
    {
        $this->testAngleCreation(FromDegrees::class);
    }

    /**
     * @testdox cannot build an angle with more than 360°.
     */
    public function test_exception_if_more_than_360_degrees()
    {
        // Arrange
        /** @var \MarcoConsiglio\Trigonometry\Builders\FromDegrees */
        $builder = $this->getMockedAngleBuilder();
        $this->setAngleBuilderProperties($builder, [361, 0, 0]);

        // Act & Assert
        $this->expectException(AngleOverflowException::class);
        $this->expectExceptionMessage("The angle degrees can't be greater than 360°.");
        $builder->checkOverflow();
    }
    /**
     * @testdox cannot build an angle with more than 59'.
     */
    public function test_exception_if_more_than_59_minutes()
    {
        // Arrange
        /** @var \MarcoConsiglio\Trigonometry\Builders\FromDegrees */
        $builder = $this->getMockedAngleBuilder();
        $this->setAngleBuilderProperties($builder, [0, 60, 0]);

        // Assert
        $this->expectException(AngleOverflowException::class);
        $this->expectExceptionMessage("The angle minutes can't be greater than 59'.");

        // Act
        $builder->checkOverflow();
    }

    /**
     * @testdox cannot build an angle with 60" or more.
     */
    public function test_exception_if_equal_or_more_than_60_seconds()
    {
        // Arrange
        /** @var \MarcoConsiglio\Trigonometry\Builders\FromDegrees */
        $builder = $this->getMockedAngleBuilder();
        $this->setAngleBuilderProperties($builder, [0, 0, 60]);
        
        // Assert
        $this->expectException(AngleOverflowException::class);
        $this->expectExceptionMessage("The angle seconds can't be greater than or equal to 60\".");
        
        // Act
        $builder->checkOverflow();
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