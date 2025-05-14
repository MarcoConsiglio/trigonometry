<?php
namespace MarcoConsiglio\Trigonometry\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Faker\Factory;
use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\AngleBuilder;
use MarcoConsiglio\Trigonometry\Builders\FromAngles;
use MarcoConsiglio\Trigonometry\Builders\FromDegrees;
use MarcoConsiglio\Trigonometry\Builders\FromDecimal;
use MarcoConsiglio\Trigonometry\Builders\FromRadiant;
use MarcoConsiglio\Trigonometry\Builders\FromString;
use MarcoConsiglio\Trigonometry\Interfaces\Angle as AngleInterface;
use MarcoConsiglio\Trigonometry\Interfaces\AngleBuilder as AngleBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\AnyInvokedCount;
use ReflectionClass;

class TestCase extends PHPUnitTestCase
{
    /**
     * The faker generator.
     *
     * @var \Faker\Generator;
     */
    protected $faker;

    /**
     * This method is called before each test.
     *
     * @return void
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
            $negative = Angle::CLOCKWISE;
        } else {
            $negative = Angle::COUNTER_CLOCKWISE;
        }
        return Angle::createFromValues(abs($degrees), $minutes, $seconds, $negative);
    }

    /**
     * Returns a random angle measure (or an array with degrees, minutes and seconds values) 
     * usufull to create an angle from a specified $builder.
     *
     * @param string  $builder The builder class extending the MarcoConsiglio\Trigonometry\Builders\AngleBuilder
     *  you want to use to build the angle.
     * @param boolean $negative If you want a positive or negative angle.
     * @param int     $precision The precision if the angle is created from a decimal or radiant value.
     * @return mixed
     */
    protected function getAngleValue(string $builder, $negative = false, int $precision = 0): mixed
    {
        if (class_exists($builder) && is_subclass_of($builder, AngleBuilder::class)) {
            switch ($builder) {
                case FromDegrees::class:
                    return $this->getRandomAngleDegrees($negative);
                    break;
                case FromDecimal::class:
                    return $this->getRandomAngleDecimal($negative, $precision);
                    break;
                case FromRadiant::class:
                    return $this->getRandomAngleRadiant($negative, $precision);
                    break;
                case FromString::class:
                    return $this->getRandomAngleString($negative);
                    break;
            }
        }
        return null;
    }

    /**
     * Gets random angle degrees values.
     *
     * @param boolean $negative
     * @return array
     */
    protected function getRandomAngleDegrees($negative = false)
    {
        $degrees = $this->faker->numberBetween(0, 360);
        $minutes = $this->faker->numberBetween(0, 59);
        $seconds = $this->faker->randomFloat(1, 0, 59.9);
        return [$negative ? -$degrees : $degrees, $minutes, $seconds];
    }

    /**
     * Gets a random decimal to create an angle.
     *
     * @param boolean $negative If negative or positive number.
     * @param integer $precision The precision digits of the result.
     * @return float
     */
    protected function getRandomAngleDecimal($negative = false, int $precision = 0): float
    {
        return $negative ? 
            $this->faker->randomFloat($precision, 0, Angle::MAX_DEGREES) : 
            $this->faker->randomFloat($precision, -Angle::MAX_DEGREES, 0);
    }

    /**
     * Gets a random radiant to create an angle.
     *
     * @param boolean $negative
     * @param integer $precision
     * @return void
     */
    protected function getRandomAngleRadiant($negative = false, int $precision = 0)
    {
        return $negative ? 
            $this->faker->randomFloat($precision, 0, Angle::MAX_RADIANT) : 
            $this->faker->randomFloat($precision, -Angle::MAX_RADIANT, 0);
    }

    protected function getRandomAngleString($negative = false)
    {
        [$degrees, $minutes, $seconds] = $this->getRandomAngleDegrees($negative);
        return "{$degrees}° {$minutes}' {$seconds}\"";
    }

    /**
     * Set read-only properties with the aid of Reflection.
     *
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $angle
     * @param array                                         $values
     * @return void
     */
    protected function setAngleProperties(AngleInterface $angle, array $values)
    {
        $angle_class = new ReflectionClass($angle);
        $degrees_property = $angle_class->getProperty("degrees");
        $minutes_property = $angle_class->getProperty("minutes");
        $seconds_property = $angle_class->getProperty("seconds");
        $sign_property    = $angle_class->getProperty("direction");
        $degrees_property->setValue($angle, $values[0]);       
        $minutes_property->setValue($angle, $values[1]);       
        $seconds_property->setValue($angle, $values[2]);  
        if (isset($values[3])) {
             $sign_property->setValue($angle, $values[3]);
        }
    }

    protected function setAngleBuilderProperties(AngleBuilderInterface $builder, mixed $values)
    {
        $builder_class = new ReflectionClass($builder);
        if (is_subclass_of($builder, FromDegrees::class)) {
            $degrees_property = $builder_class->getProperty("degrees");
            $minutes_property = $builder_class->getProperty("minutes");
            $seconds_property = $builder_class->getProperty("seconds");
            $sign_property    = $builder_class->getProperty("sign");
            $degrees_property->setValue($builder, $values[0]);       
            $minutes_property->setValue($builder, $values[1]);       
            $seconds_property->setValue($builder, $values[2]);  
            if (isset($values[3])) {
                 $sign_property->setValue($builder, $values[3]);
            }
        }
        if (is_subclass_of($builder, FromDecimal::class)) {
            $decimal_property = $builder_class->getProperty("decimal");
            $decimal_property->setValue($builder, $values);
        }
        if (is_subclass_of($builder, FromRadiant::class)) {
            $radiant_property = $builder_class->getProperty("radiant");
            $radiant_property->setValue($builder, $values);
        }
        if (is_subclass_of($builder, FromString::class)) {
            $measure_property = $builder_class->getProperty("measure");
            $parsing_status_property = $builder_class->getProperty("parsing_status");
            $measure_property->setValue($builder, $values[0]);
            $parsing_status_property->setValue($builder, $values[1]);
        }
        if (is_subclass_of($builder, FromAngles::class)) {
            $first_angle_property = $builder_class->getProperty("first_angle");
            $second_angle_property = $builder_class->getProperty("second_angle");
            $first_angle_property->setValue($builder, $values[0]);
            $second_angle_property->setValue($builder, $values[1]);
        }
    }

    /**
     * Constructs a mocked Angle.
     *
     * @param array $mocked_methods
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockedAngle(
        array $mocked_methods = [], 
        bool $original_constructor = false, 
        mixed $constructor_arguments = []
    ): MockObject
    {
        $angle = $this->getMockBuilder(Angle::class)
            ->onlyMethods($mocked_methods)
            ->disableOriginalConstructor();
            if ($original_constructor) {
                $angle->enableOriginalConstructor()
                        ->setConstructorArgs(
                            is_array($constructor_arguments) ? $constructor_arguments : [$constructor_arguments]
                        );
            }
        return $angle->getMock();
    }

    /**
     * Alias of any method.
     *
     * @return \PHPUnit\Framework\MockObject\Rule\AnyInvokedCount
     */
    public function anyTime(): AnyInvokedCount
    {
        return $this->any();
    }
}