<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Builders;

use MarcoConsiglio\Trigonometry\Builders\AngleBuilder;
use MarcoConsiglio\Trigonometry\Builders\SumBuilder;
use MarcoConsiglio\Trigonometry\Builders\FromAngles;
use MarcoConsiglio\Trigonometry\Interfaces\Angle;
use ReflectionClass;

/**
 * @testdox The FromAngles builder
 */
class FromAnglesTest extends BuilderTestCase
{
    /**
     * @testdox can sums two angles.
     */
    public function test_can_sum_two_angle()
    {
        // Arrange
        $first_angle = $this->getMockedAngle(
            mocked_methods: ["toDecimal"]
        );
        $second_angle = $this->getMockedAngle(
            mocked_methods: ["toDecimal"]
        );
        $first_decimal = $this->faker->randomFloat(1, -180, 180);
        $second_decimal = $this->faker->randomFloat(1, -180, 180);
        $first_angle->expects($this->anyTime())->method("toDecimal")->willReturn($first_decimal);
        $second_angle->expects($this->anyTime())->method("toDecimal")->willReturn($second_decimal);
        $builder = $this->getMockedAngleBuilder(
            mocked_methods: [],
            original_constructor: true,
            constructor_arguments: [$first_angle, $second_angle]
        );

        // Act
        /** @var \MarcoConsiglio\Trigonometry\Builders\FromAngles $builder */
        $result = $builder->fetchData();
        
        // Assert
        $result = [
            $result[0] * $result[3],
            $result[1],
            $result[2]
        ];
        $degrees = $result[0];
        $failure_message = "{$first_decimal}° + {$second_decimal}° = {$result[0]}° {$result[1]}' {$result[2]}\"";
        $this->assertTrue((new ReflectionClass(SumBuilder::class))->isAbstract(), "The SumBuilder class must be abstract.");
        $this->assertInstanceOf(SumBuilder::class, $builder, "The FromAngles builder must extends the SumBuilder abstract class.");
        $this->assertInstanceOf(AngleBuilder::class, $builder, "The FromAngles builder must extends the AngleBuilder abstract class.");
        $this->assertLessThanOrEqual(360, $degrees, $failure_message);
        $this->assertGreaterThanOrEqual(-360, $degrees, $failure_message);
    }

    /**
     * @testdox corrects positive excess if the sum is greater than 360°.
     */
    public function test_correct_positive_excess()
    {
        // Arrange
        $first_angle = $this->getMockedAngle(
            mocked_methods: ["toDecimal"]
        );
        $second_angle = $this->getMockedAngle(
            mocked_methods: ["toDecimal"]
        );
        $first_decimal = $this->faker->randomFloat(1, 180, 360);
        $second_decimal = $this->faker->randomFloat(1, 180, 360);
        $first_angle->expects($this->anyTime())->method("toDecimal")->willReturn($first_decimal);
        $second_angle->expects($this->anyTime())->method("toDecimal")->willReturn($second_decimal);
        $builder = $this->getMockedAngleBuilder(
            mocked_methods: []
        );
        /** @var \MarcoConsiglio\Trigonometry\Builders\FromAngles $builder */
        $this->setAngleBuilderProperties($builder, [$first_angle, $second_angle]);

        // Act
        /** @var \MarcoConsiglio\Trigonometry\Builders\FromAngles $builder */
        $result = $builder->fetchData();

        // Assert
        $result = [
            $result[0] * $result[3],
            $result[1],
            $result[2]
        ];
        $degrees = $result[0];
        $failure_message = "{$first_decimal}° + {$second_decimal}° = {$result[0]}° {$result[1]}' {$result[2]}\"";
        $this->assertLessThanOrEqual(360, $degrees, $failure_message);
        $this->assertGreaterThanOrEqual(-360, $degrees, $failure_message);
    }

    /**
     * @testdox corrects negative excess if the sum is less than -360°.
     */
    public function test_correct_negative_excess()
    {
        // Arrange
        $first_angle = $this->getMockedAngle(
            mocked_methods: ["toDecimal"]
        );
        $second_angle = $this->getMockedAngle(
            mocked_methods: ["toDecimal"]
        );
        $first_decimal = $this->faker->randomFloat(1, -360, -180);
        $second_decimal = $this->faker->randomFloat(1, -360, -180);
        $first_angle->expects($this->anyTime())->method("toDecimal")->willReturn($first_decimal);
        $second_angle->expects($this->anyTime())->method("toDecimal")->willReturn($second_decimal);
        $builder = $this->getMockedAngleBuilder(
            mocked_methods: []
        );
        /** @var \MarcoConsiglio\Trigonometry\Builders\FromAngles $builder */
        $this->setAngleBuilderProperties($builder, [$first_angle, $second_angle]);

        // Act
        /** @var \MarcoConsiglio\Trigonometry\Builders\FromAngles $builder */
        $result = $builder->fetchData();

        // Assert
        $result = [
            $result[0] * $result[3],
            $result[1],
            $result[2]
        ];
        $degrees = $result[0];
        $failure_message = "{$first_decimal}° + {$second_decimal}° = {$result[0]}° {$result[1]}' {$result[2]}\"";
        $this->assertLessThanOrEqual(360, $degrees, $failure_message);
        $this->assertGreaterThanOrEqual(-360, $degrees, $failure_message);      
    }

    /**
     * Returns the FromAngles builder class.
     * @return string
     */
    protected function getBuilderClass(): string
    {
        return FromAngles::class;
    }
}