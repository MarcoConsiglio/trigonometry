<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\FromAngles;
use MarcoConsiglio\Trigonometry\Builders\FromDecimal;
use MarcoConsiglio\Trigonometry\Builders\SumBuilder;
use MarcoConsiglio\Trigonometry\Interfaces\Angle as AngleInterface;
use MarcoConsiglio\Trigonometry\Operations\Sum;
use MarcoConsiglio\Trigonometry\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;

#[TestDox("A sum operation")]
#[CoversClass(Sum::class)]
#[CoversClass(SumBuilder::class)]
class SumTest extends TestCase
{
    #[TestDox("can be performed with a SumBuilder.")]
    public function test_can_sum_two_angle()
    {
        // Arrange
        $values = $this->getRandomAngleDegrees($this->faker->boolean());
        $values[3] = $values[0] < 0 ? Angle::COUNTER_CLOCKWISE : Angle::CLOCKWISE;
        $values[0] = abs($values[0]);
        $builder = $this->getMockBuilder(FromAngles::class)
            ->onlyMethods(["fetchData"])
            ->disableOriginalConstructor()
            ->getMock();
        $builder->expects($this->once())->method("fetchData")->willReturn($values);

        // Act
        /** @var \MarcoConsiglio\Trigonometry\Builders\FromAngles $builder */
        $sum = new Sum($builder);

        // Assert
        $this->assertInstanceOf(Sum::class, $sum, "The sum must be a Sum class.");
        $this->assertInstanceOf(Angle::class, $sum, "The sum must extend the Angle class.");
    }
}