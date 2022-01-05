<?php
namespace MarcoConsiglio\Trigonometry\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Faker\Factory;
use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\AngleBuilder;
use MarcoConsiglio\Trigonometry\Builders\FromDegrees;
use MarcoConsiglio\Trigonometry\Builders\FromDecimal;
use MarcoConsiglio\Trigonometry\Builders\FromRadiant;
use MarcoConsiglio\Trigonometry\Builders\FromString;

class TestCase extends PHPUnitTestCase
{
    /**
     * The faker generator.
     *
     * @var \Faker\Generator;
     */
    protected $faker;

    /*
        * This method is called before each test.
        */
    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    /**
     * Gets a random angle.
     *
     * @param int $sign
     * @return \MarcoConsiglio\Trigonometry\Angle
     */
    protected function getRandomAngle(bool $negative = false): \MarcoConsiglio\Trigonometry\Angle
    {
        [$degrees, $minutes, $seconds] = $this->getAngleValue(FromDegrees::class, $negative);
        if ($negative) {
            $negative = Angle::COUNTER_CLOCKWISE;
        } else {
            $negative = Angle::CLOCKWISE;
        }
        return Angle::createFromValues($degrees, $minutes, $seconds, $negative);
    }

    /**
     * Returns a random value (or an array with values) usufull to create an angle
     * from $builder.
     *
     * @param string  $builder The builder class you want to use to build the angle.
     * @param boolean $negative If you want a positive or negative angle.
     * @return mixed
     */
    protected function getAngleValue(string $builder, $negative = false): mixed
    {
        if (class_exists($builder) && is_subclass_of($builder, AngleBuilder::class)) {
            switch ($builder) {
                case FromDegrees::class:
                    return $this->getRandomAngleDegrees($negative);
                    break;
                case FromDecimal::class:
                    return $this->getRandomAngleDecimal($negative);
                    break;
                case FromRadiant::class:
                    return $this->getRandomAngleRadiant($negative);
                    break;
                case FromString::class:
                    return $this->getRandomAngleString($negative);
                    break;
            }
        }
        return null;
    }

    protected function getRandomAngleDegrees($negative = false)
    {
        $degrees = $this->faker->numberBetween(0, 360);
        $minutes = $this->faker->numberBetween(0, 60);
        $seconds = $this->faker->randomFloat(1, 0, 60);
        if ($seconds == 60) {
            $minutes++;
        }
        if ($minutes == 60) {
            $degrees++;
        }
        if ($degrees >= 360) {
            $minutes = 0;
            $seconds = 0;
        }
        return [$negative ? -$degrees : $degrees, $minutes, $seconds];
    }

    protected function getRandomAngleDecimal($negative = false)
    {
        return $negative ? 
            $this->faker->randomFloat(15, 0, Angle::MAX_DEGREES) : 
            $this->faker->randomFloat(15, -Angle::MAX_DEGREES, 0);
    }

    protected function getRandomAngleRadiant($negative = false)
    {
        return $negative ? 
            $this->faker->randomFloat(15, 0, Angle::MAX_RADIANT) : 
            $this->faker->randomFloat(15, -Angle::MAX_RADIANT, 0);
    }

    protected function getRandomAngleString($negative = false)
    {
        [$degrees, $minutes, $seconds] = $this->getRandomAngleDegrees($negative);
        return "{$degrees}° {$minutes}' {$seconds}\"";
    }
}