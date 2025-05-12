<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\FromDecimal;
use MarcoConsiglio\Trigonometry\Builders\FromRadiant;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;

#[TestDox("The FromRadiant builder")]
#[CoversClass(FromRadiant::class)]
#[UsesClass(Angle::class)]
#[UsesClass(AngleOverflowException::class)]
#[UsesClass(FromDecimal::class)]
class FromRadiantTest extends BuilderTestCase
{
    #[TestDox("can create a positive angle from a radiant value.")]
    public function test_can_create_positive_angle()
    {
        $this->testAngleCreation(FromRadiant::class);
    }

    #[TestDox("can create a negative angle from a radiant value.")]
    public function test_can_create_negative_angle()
    {
        $this->testAngleCreation(FromRadiant::class, negative: true);
    }

    #[TestDox("cannot create an angle with more than +/-360°.")]
    public function test_exception_if_more_than_360_degrees()
    {
        // Assert
        $this->expectException(AngleOverflowException::class);
        $this->expectExceptionMessage("The angle can't be greater than 360°.");

        // Arrange & Act
        new FromRadiant(Angle::MAX_RADIANT + 0.00001);
    }

    #[TestDox("can create an angle of exact 360°.")]
    public function test_missing_exception_if_equal_360_degrees()
    {
        // Arrange & Act
        new FromRadiant(Angle::MAX_RADIANT);

        // Assert
        $this->expectNotToPerformAssertions();
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