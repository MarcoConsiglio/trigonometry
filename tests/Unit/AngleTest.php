<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit;

use InvalidArgumentException;
use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;
use MarcoConsiglio\Trigonometry\Tests\TestCase;
use MarcoConsiglio\Trigonometry\Exceptions\NoMatchException;
use Laracasts\TestDummy\Factory;
use ReflectionClass;

/**
 * @testdox An angle
 */
class AngleTest extends TestCase
{
    /**
     * Degrees being tested.
     *
     * @var int
     */
    protected int $degrees;

    /**
     * Minutes being tested.
     *
     * @var int
     */
    protected int $minutes;

    /**
     * Seconds being tested.
     *
     * @var int
     */
    protected float $seconds;

    /**
     * How would be a textual representation of an angle.
     *
     * @var string
     */
    protected string $expected_string;

    /**
     * The angle being tested.
     *
     * @var \MarcoConsiglio\Trigonometry\Angle
     */
    protected Angle $angle;

    /*
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->angle = $this->randomAngle();
        $this->degrees = $this->faker->numberBetween(0, 360);
        if ($this->degrees == 360) {
            $this->minutes = 0;
            $this->seconds = 0;
        } else {
            $this->minutes = $this->faker->numberBetween(0, 59);
            $this->seconds = $this->faker->randomFloat(1, 0, 59.9);
        }
        $this->radiant = $this->faker->randomFloat(0, Angle::MAX_RADIANT, 8);
        $this->expected_string = $this->degrees."° ".$this->minutes."' ".$this->seconds."\"";
        try {
            $this->angle = Angle::createFromValues($this->degrees, $this->minutes, $this->seconds);
        } catch (AngleOverflowException $e){
            $this->markTestSkipped($e->getMessage());
        }
    }

    /**
     * @testdox can have degrees, minutes and seconds.
     */
    public function test_can_obtain_values()
    {
        /**
         * Simple array.
         */
        // Arrange in setUp()

        // Act
        [$degrees, $minutes, $seconds] = $this->angle->getDegrees(false);

        // Assert
        $failure_message = "Can't get angle values as a simple array.";
        $this->assertEquals($this->degrees, $degrees, $failure_message);
        $this->assertEquals($this->minutes, $minutes, $failure_message);
        $this->assertEquals($this->seconds, $seconds, $failure_message);

        /**
         * Associative array
         */
        // Arrange
        $this->setUp();

        // Act
        $values = $this->angle->getDegrees(true);
        
        // Assert
        $failure_message = "Can't get angle values as an associative array.";
        $this->assertEquals($this->degrees, $values["degrees"], $failure_message);
        $this->assertEquals($this->minutes, $values["minutes"], $failure_message);
        $this->assertEquals($this->seconds, $values["seconds"], $failure_message);
    }

    /**
     * @testdox can be printed in a textual representation.
     */
    public function test_can_print_angle()
    {
        // Arrange in setUp()

        // Act
        $actual_string = (string) $this->angle;

        // Assert
        $this->assertEquals($this->expected_string, $actual_string, "Something does not working while getting a textual representation.");
    }

    /**
     * @testdox can be casted to decimal.
     */
    public function test_cast_to_decimal()
    {
        $failure_message = "Cannot cast angle to decimal.";
        /**
         * Positive angle
         */
        // Arrange in setUp()
        $angle = Angle::createFromValues($this->degrees, $this->minutes, $this->seconds);

        // Act
        $decimal = $angle->toDecimal();

        // Assert
        $this->assertIsFloat($decimal);
        $this->assertEquals(
            $this->degrees + $this->minutes / 60 + $this->seconds / 3600, 
            $decimal, 
            $failure_message
        );

        /**
         * Negative angle
         */
        // Arrange in setUp()
        $angle = Angle::createFromValues(
            $this->degrees, 
            $this->minutes, 
            $this->seconds, 
            Angle::COUNTER_CLOCKWISE
        );

        // Act
        $decimal = $angle->toDecimal();

        // Assert
        $this->assertIsFloat($decimal);
        $this->assertEquals(-($this->degrees + $this->minutes / 60 + $this->seconds / 3600), $decimal, $failure_message);
    }

    /**
     * @testdox can be casted to radiant.
     * @depends test_cast_to_decimal
     */
    public function test_cast_to_radiant()
    {
        // Arrange in setUp()

        // Act
        $angle = Angle::createFromValues($this->degrees, $this->minutes, $this->seconds);
        $radian = $angle->toRadiant();

        // Assert
        $this->assertEquals(deg2rad($angle->toDecimal()), $radian, "Something is wrong when casting to radiant.");
    }

    /**
     * @testdox can be rotated in the opposite direction.
     */
    public function test_toggle_rotation()
    {
        $failure_message = "Cannot reverse the angle.";
        /**
         * From positive to negative.
         */
        // Arrange in setUp()

        // Act
        $this->angle->toggleDirection();

        // Assert
        $this->assertTrue($this->angle->isCounterClockwise(), $failure_message);

        /**
         * From negative to positive.
         */
        // Arrange
        $this->setUp();
        $angle = Angle::createFromValues(
            $this->degrees, 
            $this->minutes, 
            $this->seconds, 
            Angle::COUNTER_CLOCKWISE
        );

        // Act
        $angle->toggleDirection();

        // Assert
        $this->assertTrue($angle->isClockwise(), $failure_message);

    }

    /**
     * @testdox can be or not greater than another.
     */
    public function test_greater_than_comparison()
    {
        // Arrange
        $alfa = Angle::createFromDecimal(90);
        $beta = Angle::createFromDecimal(180);
        $gamma = Angle::createFromDecimal(-90);
        $delta = Angle::createFromDecimal(-180);
        $alfa_str = (string) $alfa;
        $beta_str = (string) $beta;
        $gamma_str = (string) $gamma;
        $delta_str = (string) $delta;

        // Act & Assert
        $this->assertFalse($alfa->isGreaterThan(180),       "{$alfa_str} > 180°");
        $this->assertFalse($alfa->isGreaterThan("180"),     "{$alfa_str} > '180°'");
        $this->assertFalse($alfa->isGreaterThan($beta),     "{$alfa_str} > {$beta_str}");
        $this->assertFalse($alfa->gt(180),                  "{$alfa_str} > 180°");
        $this->assertFalse($alfa->gt($beta),                "{$alfa_str} > 180°");
        $this->assertTrue($beta->isGreaterThan(90),         "{$beta_str} > 90°");
        $this->assertTrue($beta->isGreaterThan($alfa),      "{$beta_str} > {$alfa_str}");
        $this->assertTrue($gamma->isGreaterThan(-180),      "{$gamma_str} > -180°");
        $this->assertTrue($gamma->isGreaterThan($delta),    "{$gamma_str} > {$delta_str}");
        $this->assertFalse($delta->isGreaterThan(-90),      "{$delta_str} > -90°");
        $this->assertFalse($delta->isGreaterThan($gamma),   "{$delta_str} > {$gamma_str}");
        $this->assertFalse($alfa->isGreaterThan(90),        "{$alfa_str} > 90°");
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->getInvalidArgumentMessage(true, ["int", "float", "string", Angle::class], Angle::class."::isGreaterThan", 1));
        $alfa->isGreaterThan(true);
    }

    /**
     * @testdox can be or not greater than or equal another angle.
     */
    public function test_greater_than_or_equal_comparison()
    {
        // Arrange
        $alfa = Angle::createFromDecimal(90);
        $beta = Angle::createFromDecimal(180);
        $alfa_str = (string) $alfa;
        $beta_str = (string) $beta;

        // Act & Assert
        $this->assertFalse($alfa->isGreaterThanOrEqual(180),    "{$alfa_str} >= 180°");
        $this->assertFalse($alfa->isGreaterThanOrEqual($beta),  "{$alfa_str} >= {$beta_str}");
        $this->assertFalse($alfa->gte(180),                     "{$alfa_str} >= 180°");
        $this->assertFalse($alfa->gte($beta),                   "{$alfa_str} >= {$beta_str}");
        $this->assertTrue($beta->isGreaterThanOrEqual(180),     "{$alfa_str} >= {$beta_str}");
        $this->assertTrue($beta->isGreaterThanOrEqual($beta),   "{$beta_str} >= {$beta_str}");
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->getInvalidArgumentMessage(true, ["int", "float", "string", Angle::class], Angle::class."::isGreaterThanOrEqual", 1));
        $alfa->isGreaterThanOrEqual(true);
    }

    /**
     * @testdox can be or not less than another angle.
     */
    public function test_less_than_comparison()
    {
        // Arrange
        $alfa = Angle::createFromDecimal(90);
        $beta = Angle::createFromDecimal(180);
        $alfa_str = (string) $alfa;
        $beta_str = (string) $beta;

        // Act & Assert
        $this->assertTrue($alfa->isLessThan(180),       "{$alfa_str} < 180°");
        $this->assertTrue($alfa->isLessThan($beta),     "{$alfa_str} < {$beta_str}");
        $this->assertFalse($beta->isLessThan(90),       "{$beta_str} < 90");
        $this->assertFalse($beta->isLessThan($alfa),    "{$beta_str} < {$alfa_str}");
        $this->assertTrue($alfa->lt(180),               "{$alfa_str} < 180°");
        $this->assertTrue($alfa->lt($beta),             "{$alfa_str} < {$beta_str}");
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->getInvalidArgumentMessage("hello", ["int", "float", "string", Angle::class], Angle::class."::isLessThan", 1));
        $alfa->isLessThan("hello");
    }

    /**
     * @testdox can be or not greater than or equal another angle.
     */
    public function test_less_than_or_equal_comparison()
    {
        // Arrange
        $alfa = Angle::createFromDecimal(90);
        $beta = Angle::createFromDecimal(180);
        $alfa_str = (string) $alfa;
        $beta_str = (string) $beta;

        // Act & Assert
        $this->assertTrue($alfa->isLessThanOrEqual(180),        "{$alfa_str} <= 180°");
        $this->assertTrue($alfa->isLessThanOrEqual($beta),      "{$alfa_str} <= {$beta_str}");
        $this->assertFalse($beta->isLessThanOrEqual(90),        "{$beta_str} <= 90°");
        $this->assertFalse($beta->isLessThanOrEqual($alfa),     "{$beta_str} <= {$alfa_str}");
        $this->assertTrue($alfa->lte(180),                      "{$alfa_str} <= 180°");
        $this->assertTrue($alfa->lte($beta),                    "{$alfa_str} <= {$beta_str}");
        $this->assertTrue($alfa->isLessThanOrEqual(90),         "{$alfa_str} <= 90°");
        $this->assertTrue($alfa->isLessThanOrEqual($alfa),      "{$alfa_str} <= {$alfa_str}");
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->getInvalidArgumentMessage("hello", ["int", "float", "string", Angle::class], Angle::class."::isLessThanOrEqual", 1));
        $alfa->isLessThanOrEqual("hello");
    }

    /**
     * @testdox can throw InvalidArgumentException.
     */
    public function test_invalid_argument_exception()
    {
        // Arrange
        $expected_type = "int";
        $argument = "shabadula";
        $angle = $this->randomAngle();
        $class = new ReflectionClass($angle);
        $method = $class->getMethod("throwInvalidArgumentException");
        $method->setAccessible(true);
        
        // Act & Assert
        ;       $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->getInvalidArgumentMessage($argument, [$expected_type], "<the_method>", 1));
        $method->invoke($angle, $argument, [$expected_type], "<the_method>", 1);
    }

    /**
     * An invalid argument message fixture.
     *
     * @param mixed   $argument
     * @param array   $expected_types
     * @param string  $method
     * @param integer $parameter_position
     * @return string
     */
    protected function getInvalidArgumentMessage(mixed $argument, array $expected_types, string $method, int $parameter_position): string
    {
        $last_type = "";
        $total_types = count($expected_types);
        if ($total_types >= 2) {
            $last_type = " or ".$expected_types[$total_types - 1];
            unset($expected_types[$total_types - 1]);
        }
        return "$method method expects parameter $parameter_position to be ".implode(", ", $expected_types).$last_type.", but found ".gettype($argument);
    }
}