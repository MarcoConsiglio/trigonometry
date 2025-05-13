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
use MarcoConsiglio\Trigonometry\Tests\Traits\WithFailureMessage;
use PHPUnit\Framework\MockObject\MockObject;

abstract class BuilderTestCase extends TestCase
{
    use WithFailureMessage;

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
     * Expects an $exception with a $message.
     *
     * @param string $exception
     * @param string $message
     * @return void
     */
    protected function expectExceptionWithMessage(string $exception, string $message): void
    {
        $this->expectException($exception);
        $this->expectExceptionMessage($message);
    }

    /**
     * Test the Angle creation with a specified AngleBuilder. This is a Parameterized Test.
     *
     * @param mixed   $value   The value used to create the angle.
     * @param string  $builder The builder that extends AngleBuilder.
     * @param boolean $negative Specifies if to test a negative angle.
     * @param int     $precision The precision if the angle is created from a decimal or radiant value.
     * @return void
     */
    protected function testAngleCreation(string $builder, bool $negative = false, int $precision = 0)
    {
        if(class_exists($builder) && is_subclass_of($builder, AngleBuilder::class)) {
            // Arrange
            $value = $this->getAngleValue($builder, $negative, $precision);
            
            // Act
            switch ($builder) {
                case FromDegrees::class:
                    $angle = Angle::createFromValues($value[0], $value[1], $value[2]);
                    break;
                case FromDecimal::class:
                    $angle = Angle::createFromDecimal($value);
                    break;
                case FromRadiant::class:
                    $angle = Angle::createFromRadiant($value);
                    break;
                case FromString::class:
                    $angle = Angle::createFromString($value);
                    break;
            }

            // Assert
            $this->assertAngle($builder, $value, $angle);
        }
    }

    /**
     * Assert that an $object property $name equals $expected_value and its type is $expected_type.
     *
     * @param string $expected_type The type you expect from the property.
     * @param string $name  The name of the property.
     * @param object $object    The object to test.
     * @param mixed  $expected_value The value you expect from the property.
     * @return void
     */
    public function assertProperty(string $expected_type, string $name, object $object, mixed $expected_value)
    {
        $this->assertEquals($expected_value, $object->$name, $this->getterFail($name));
        if (class_exists($expected_type)) {
            $this->assertInstanceOf($expected_type, $object, $this->typeFail($name));
        }
        if ($expected_type == "int") {
            $this->assertIsInt($object->$name, $this->typeFail($name));
        }
        if ($expected_type == "float") {
            $this->assertIsFloat($object->$name, $this->typeFail($name));
        }
        if ($expected_type == "string") {
            $this->assertIsString($object->$name, $this->typeFail($name));
        }
        if ($expected_type == "boolean") {
            $this->assertIsBool($object->$name, $this->typeFail($name));
        }
    }

    /**
     * Assert that $angle has the $expected_values.
     *
     * @param array                              $values
     * @param \MarcoConsiglio\Trigonometry\Angle $angle
     * @return void
     */
    public function assertAngleDegrees(array $expected_values, Angle $angle)
    {
        $this->assertProperty("int", "degrees", $angle, $expected_values[0]);
        $this->assertProperty("int", "minutes", $angle, $expected_values[1]);
        $this->assertProperty("float", "seconds", $angle, $expected_values[2]);
    }

    /**
     * Assert that $angle->toDecimal() equals $expected_values.
     *
     * @param float                              $expected_value The decimal value you expect from the $angle.
     * @param \MarcoConsiglio\Trigonometry\Angle $angle The angle to test.
     * @return void
     */
    public function assertAngleDecimal(float $expected_value, Angle $angle)
    {
        $expected = round($expected_value, 1, PHP_ROUND_HALF_DOWN);
        $actual = round($angle->toDecimal(), 1, PHP_ROUND_HALF_DOWN);
        $this->assertEquals($expected, $actual, $this->methodFail(Angle::class."::toDecimal"));
    }

    /**
     * Assert that $angle->toRadiant() equals $expected_values.
     *
     * @param float                              $expected_value The radiant value you expect from the $angle.
     * @param \MarcoConsiglio\Trigonometry\Angle $angle The angle to test.
     * @return void
     */
    public function assertAngleRadiant(float $expected_value, Angle $angle)
    {
        $expected = round($expected_value, 1, PHP_ROUND_HALF_DOWN);
        $actual = round($angle->toRadiant(), 1, PHP_ROUND_HALF_DOWN);
        $this->assertEquals($expected, $actual, $this->methodFail(Angle::class."::toRadiant"));
    }

    /**
     * Assert that $angle->__toString() equals $expected_value.
     *
     * @param string                             $expected_value The string value you expect from the $angle.
     * @param \MarcoConsiglio\Trigonometry\Angle $angle The angle to test.
     * @return void
     */
    public function assertAngleString(string $expected_value, Angle $angle)
    {
        $this->assertEquals($expected_value, $angle->__toString(), $this->methodFail(Angle::class."::__toString"));
    }

    /**
     * Assert that an $angle equals the $expected_value(s).
     *
     * @param string                             $builder
     * @param mixed                              $expected_value
     * @param \MarcoConsiglio\Trigonometry\Angle $angle
     * @return void
     */
    public function assertAngle(string $builder, mixed $expected_value, Angle $angle)
    {
        switch ($builder) {
            case FromDegrees::class:
                $this->assertAngleDegrees($expected_value, $angle);
                break;
            case FromDecimal::class:
                $this->assertAngleDecimal($expected_value, $angle);
                break;
            case FromRadiant::class:
                $this->assertAngleRadiant($expected_value, $angle);
                break;
            case FromString::class:
                $this->assertAngleString($expected_value, $angle);
                break;
        }
    }

    /**
     * Constructs a mocked AngleBuilder based on the getBuilderClass method.
     *
     * @param array $mocked_methods The methods you want to hide or mock.
     * @param boolean $original_constructor Wheater you want to enable the original class constructor or a mocked one.
     * @param mixed $constructor_arguments If $oroginal_constructor = true pass here the constructor arguments.
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockedAngleBuilder(array $mocked_methods = [], $original_constructor = false, mixed $constructor_arguments = []): MockObject
    {
        $builder = $this->getMockBuilder($this->getBuilderClass())
            ->onlyMethods($mocked_methods)
            ->disableOriginalConstructor();
        if ($original_constructor) {
            $builder->enableOriginalConstructor()
                    ->setConstructorArgs(is_array($constructor_arguments) ? $constructor_arguments : [$constructor_arguments]);
        }
        return $builder->getMock();
    }

    /**
     * Implemented in a concrete BuilderTestCase, returns the
     * concrete AngleBuilder to test.
     * 
     * @return string
     */
    protected abstract function getBuilderClass(): string;
}