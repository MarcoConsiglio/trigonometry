<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;
use MarcoConsiglio\Trigonometry\Builders\FromString;
use MarcoConsiglio\Trigonometry\Exceptions\NoMatchException;

/**
 * @testdox The FromString builder
 */
class FromStringTest extends BuilderTestCase
{
    /**
     * @testdox can create a positive angle from a string value.
     */
    public function test_can_create_positive_angle()
    {
        $this->testAngleCreation(FromString::class);
    }

    /**
     * @testdox can create a negative angle from a string value.
     */
    public function test_can_create_negative_angle()
    {
        $this->testAngleCreation(FromString::class, negative: true);
    }
    
    /**
     * @testdox cannot create an angle with more than 360°.
     */
    public function test_exception_if_more_than_360_degrees()
    {
        // Arrange
        $angle_string = "361° 0' 0\"";

        // Assert
        $this->expectException(AngleOverflowException::class);
        $this->expectExceptionMessage("The angle degrees can't be greater than 360°.");

        // Act
        new FromString($angle_string);
    }

    /**
     * @testdox cannot create an angle with more than 59'.
     */
    public function test_exception_if_more_than_59_minutes()
    {
        // Arrange
        $angle_string = "0° 60' 0\"";

        // Assert
        $this->expectException(NoMatchException::class);
        $this->expectExceptionMessage("Can't recognize the string $angle_string.");

        // Act
        new FromString($angle_string);
    }

    /**
     * @testdox cannot create an angle with more than 60".
     */
    public function test_exception_if_more_than_60_seconds()
    {
        // Arrange
        $angle_string = "0° 0' 60\"";

        // Assert
        $this->expectException(NoMatchException::class);
        $this->expectExceptionMessage("Can't recognize the string $angle_string");

        // Act
        new FromString($angle_string);
    }

    /**
     * Returns the FromString builder class.
     * @return string
     */
    protected function getBuilderClass(): string
    {
        return FromString::class;
    }
}