<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\AngleBuilder;
use MarcoConsiglio\Trigonometry\Tests\TestCase;
use MarcoConsiglio\Trigonometry\Builders\FromDecimal;
use MarcoConsiglio\Trigonometry\Builders\FromDegrees;
use MarcoConsiglio\Trigonometry\Builders\FromRadiant;
use MarcoConsiglio\Trigonometry\Builders\FromString;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;

class BuilderTestCase extends TestCase
{
    protected function getExcessValues(string $builder_class, bool $negative = false): mixed
    {
        if (!$negative) {
            if ($builder_class == FromDegrees::class) {
                return [
                    $this->faker->numberBetween(361, 999),
                    $this->faker->numberBetween(61, 100),
                    $this->faker->numberBetween(61, 100)
                ];
            }
            if ($builder_class == FromDecimal::class) {
                return $this->faker->randomFloat(1, 360.1, 999.0);
            }
            if ($builder_class == FromRadiant::class) {
                return $this->faker->randomFloat(1, Angle::MAX_RADIANT + 0.1, 10);
            }
            if ($builder_class == FromString::class) {
                [$degrees, $minutes, $seconds] = $this->getExcessValues(FromDegrees::class);
                return "{$degrees}° {$minutes}' {$seconds}\"";
            }
        } else {
            if ($builder_class == FromDegrees::class) {
                return [
                    $this->faker->numberBetween(-999, -361),
                    $this->faker->numberBetween(61, 100),
                    $this->faker->numberBetween(61, 100)
                ];
            }
            if ($builder_class == FromDecimal::class) {
                return $this->faker->randomFloat(1, -999, -360.1);
            }
            if ($builder_class == FromRadiant::class) {
                return $this->faker->randomFloat(1, -(Angle::MAX_RADIANT + 0.1), -10);
            }
            if ($builder_class == FromString::class) {
                [$degrees, $minutes, $seconds] = $this->getExcessValues(FromDegrees::class, false);
                return "{$degrees}° {$minutes}' {$seconds}\"";
            }
        }
        return null;
    }

    /**
     * Test the AngleOverflowException is thrown if values exceed the round angle.
     *
     * @param boolean $negative Specifies to test a positive or negative overflow.
     * @param string  $builder The class builder to test.
     * @return void
     */
    protected function testAngleCreationException(string $builder, string $exception, bool $negative = false)
    {
        if (class_exists($builder) && is_subclass_of($builder, AngleBuilder::class)) {
            $value = $this->getExcessValues($builder, $negative);
            $this->expectException($exception);
            switch ($builder) {
                case FromDegrees::class:
                    Angle::createFromValues($value[0], $value[1], $value[2]);
                    break;
                case FromDecimal::class:
                    Angle::createFromDecimal($value);
                    break;
                case FromRadiant::class:
                    Angle::createFromRadiant($value);
                    break;
                case FromString::class:
                    Angle::createFromString($value);
                    break;
            }         
        }
    }
}