<?php
namespace MarcoConsiglio\Trigonometry\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Faker\Factory;
use MarcoConsiglio\Trigonometry\Angle;

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
    protected function randomAngle(int $sign = null): \MarcoConsiglio\Trigonometry\Angle
    {
        $degrees = $this->faker->numberBetween(0, 360);
        if ($degrees == 360) {
            $minutes = 0;
            $seconds = 0;
        } else {
            $minutes = $this->faker->numberBetween(0, 59);
            $seconds = $this->faker->randomFloat(1, 0, 59.9);
        }
        if ($sign === null) {
            $sign = $this->faker->randomElement([Angle::CLOCKWISE, Angle::COUNTER_CLOCKWISE]);
        }
        return Angle::createFromValues($degrees, $minutes, $seconds, $sign);
    }
}