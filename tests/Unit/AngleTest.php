<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit;

use InvalidArgumentException;
use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\FromDecimal;
use MarcoConsiglio\Trigonometry\Tests\TestCase;
use MarcoConsiglio\Trigonometry\Builders\FromDegrees;
use MarcoConsiglio\Trigonometry\Interfaces\AngleBuilder;
use ReflectionClass;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @testdox An angle
 */
class AngleTest extends TestCase
{
    use ProphecyTrait;

    /**
     * A mocked builder.
     *
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    protected ObjectProphecy $builder;

    /**
     * The expected degrees, minutes, seconds e angle direction.
     *
     * @var array
     */
    protected array $expected;

    // /**
    //  * Degrees being tested.
    //  *
    //  * @var int
    //  */
    // protected int $degrees;

    // /**
    //  * Minutes being tested.
    //  *
    //  * @var int
    //  */
    // protected int $minutes;

    // /**
    //  * Seconds being tested.
    //  *
    //  * @var int
    //  */
    // protected float $seconds;

    // /**
    //  * How would be a textual representation of an angle.
    //  *
    //  * @var string
    //  */
    // protected string $expected_string;

    // /**
    //  * The angle being tested.
    //  *
    //  * @var \MarcoConsiglio\Trigonometry\Angle
    //  */
    // protected Angle $angle;

    /*
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->expected = $this->getRandomAngleDegrees();
        // $this->angle = $this->getRandomAngle($this->faker->boolean());
        // [$this->degrees, $this->minutes, $this->seconds] = $this->getRandomAngleDegrees();
        // $this->radiant = $this->getRandomAngleRadiant();
        // $this->expected_string = $this->degrees."° ".$this->minutes."' ".$this->seconds."\"";
        // try {
        //     $this->angle = Angle::createFromValues($this->degrees, $this->minutes, $this->seconds);
        // } catch (AngleOverflowException $e){
        //     $this->markTestSkipped($e->getMessage());
        // }
    }

    /**
     * @testdox can give degrees, minutes and seconds wrapped in a simple array.
     */
    public function test_get_angle_values_in_simple_array()
    {
        // Arrange
        $this->expected[] = $this->faker->randomElement([Angle::CLOCKWISE, Angle::COUNTER_CLOCKWISE]);
        $angle = $this->createRandomAngleWithStubBuilder(FromDegrees::class, $this->expected);

        // Act
        $actual_values = $angle->getDegrees();

        // Assert
        $failure_message = "Can't get angle values as a simple array.";
        $this->assertEquals($this->expected[0], $actual_values[0], $failure_message);
        $this->assertEquals($this->expected[1], $actual_values[1], $failure_message);
        $this->assertEquals($this->expected[2], $actual_values[2], $failure_message);
    }

    /**
     * @testdox can give degrees, minutes and seconds wrapped in an associative array.
     */
    public function test_get_angle_values_in_assoc_array()
    {
        // Arrange
        $this->expected[] = $this->faker->randomElement([Angle::CLOCKWISE, Angle::COUNTER_CLOCKWISE]);
        $angle = $this->createRandomAngleWithStubBuilder(FromDegrees::class, $this->expected);

        // Act
        $actual_values = $angle->getDegrees(associative: true);

        // Assert
        $failure_message = "Can't get angle values as a simple array.";
        $this->assertEquals($this->expected[0], $actual_values["degrees"], $failure_message);
        $this->assertEquals($this->expected[1], $actual_values["minutes"], $failure_message);
        $this->assertEquals($this->expected[2], $actual_values["seconds"], $failure_message);
    }

    /**
     * @testdox can be printed in a positive textual representation.
     */
    public function test_can_cast_positive_angle_to_string()
    {
        // Arrange
        $this->expected[] = Angle::CLOCKWISE;
        $expected_string = "{$this->expected[0]}° {$this->expected[1]}' {$this->expected[2]}\"";
        $angle = $this->createRandomAngleWithStubBuilder(FromDegrees::class, $this->expected);

        // Act & Assert
        $this->assertEquals($expected_string, (string) $angle, $this->getCastError("string"));
    }

    /**
     * @testdox can be printed in a negative textual representation.
     */
    public function test_can_cast_negative_angle_to_string()
    {
        // Arrange
        $this->expected[] = Angle::COUNTER_CLOCKWISE;
        $expected_string = "-{$this->expected[0]}° {$this->expected[1]}' {$this->expected[2]}\"";
        $angle = $this->createRandomAngleWithStubBuilder(FromDegrees::class, $this->expected);

        // Act & Assert
        $this->assertEquals($expected_string, (string) $angle, $this->getCastError("string"));
    }

    /**
     * @testdox can be casted to decimal.
     */
    public function test_can_cast_to_decimal()
    {
        // Arrange
        $this->expected[] = $this->faker->randomElement([Angle::CLOCKWISE, Angle::COUNTER_CLOCKWISE]);
        $angle = $this->createRandomAngleWithStubBuilder(FromDegrees::class, $this->expected);

        // Act
        $decimal = $angle->toDecimal();

        // Assert
        $this->assertIsFloat($decimal);
        [$degrees, $minutes, $seconds, $sign] = $this->expected;
        $this->assertEquals(
            ($degrees + $minutes / 60 + $seconds / 3600) * $sign, 
            $decimal, 
            $this->getCastError("decimal")
        );
    }

    /**
     * @testdox can be casted to radiant.
     */
    public function test_cast_to_radiant()
    {
        // Arrange
        $this->expected[] = $this->faker->randomElement([Angle::CLOCKWISE, Angle::COUNTER_CLOCKWISE]);
        [$degrees, $minutes, $seconds, $sign] = $this->expected;
        $expected_radiant = deg2rad(($degrees + $minutes / 60 + $seconds / 3600) * $sign);
        $angle = $this->createRandomAngleWithStubBuilder(FromDegrees::class, $this->expected);

        // Act
        $radiant = $angle->toRadiant();

        // Assert
        $this->assertEquals($expected_radiant, $radiant, $this->getCastError("radiant"));
    }

    /**
     * @testdox can be reversed from clockwise to counterclockwise.
     */
    public function test_can_toggle_rotation_from_clockwise_to_counterclockwise()
    {
        // Arrange
        $this->expected[] = Angle::CLOCKWISE;
        $angle = $this->createRandomAngleWithStubBuilder(FromDegrees::class, $this->expected);

        // Act
        $angle->toggleDirection();

        // Assert
        $failure_message = "The angle should be counterclockwise but found the opposite";
        $this->assertTrue($angle->isCounterClockwise(), $failure_message);
        $this->assertFalse($angle->isClockwise(), $failure_message);
    }

    /**
     * @testdox can be reversed from counterclockwise to clockwise.
     */
    public function test_can_toggle_rotation_from_counterclockwise_to_clockwise()
    {
        // Arrange
        $this->expected[] = Angle::COUNTER_CLOCKWISE;
        $angle = $this->createRandomAngleWithStubBuilder(FromDegrees::class, $this->expected);

        // Act
        $angle->toggleDirection();

        // Assert
        $failure_message = "The angle should be clockwise but found the opposite.";
        $this->assertTrue($angle->isClockwise(), $failure_message);
        $this->assertFalse($angle->isCounterClockwise(), $failure_message);
    }

    /**
     * @testdox can be or not greater than another.
     */
    public function test_greater_than_comparison()
    {
        // Arrange
        $alfa = $this->createRandomAngleWithStubBuilder(FromDegrees::class, [90, 0, 0, Angle::CLOCKWISE]);
        $beta = $this->createRandomAngleWithStubBuilder(FromDegrees::class, [180, 0, 0, Angle::CLOCKWISE]);
        $gamma = $this->createRandomAngleWithStubBuilder(FromDegrees::class, [90, 0, 0, Angle::COUNTER_CLOCKWISE]);
        $delta = $this->createRandomAngleWithStubBuilder(FromDegrees::class, [180, 0, 0, Angle::COUNTER_CLOCKWISE]);
        $alfa_str = (string) $alfa;
        $beta_str = (string) $beta;
        $gamma_str = (string) $gamma;
        $delta_str = (string) $delta;

        // Act & Assert
        $this->assertFalse($alfa->isGreaterThan(180),       "{$alfa_str} > 180°");
        $this->assertFalse($alfa->isGreaterThan("180"),     "{$alfa_str} > '180°'");
        $this->assertFalse($alfa->isGreaterThan($beta),     "{$alfa_str} > {$beta_str}");
        $this->assertFalse($alfa->gt(180),                  "{$alfa_str} > 180°");
        $this->assertFalse($alfa->gt("180"),                "{$alfa_str} > 180°");
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
        $alfa = $this->createRandomAngleWithStubBuilder(FromDecimal::class, [90, 0, 0, Angle::CLOCKWISE]);
        $beta = $this->createRandomAngleWithStubBuilder(FromDecimal::class, [180, 0, 0, Angle::CLOCKWISE]);
        $alfa_str = (string) $alfa;
        $beta_str = (string) $beta;

        // Act & Assert
        $this->assertFalse($alfa->isGreaterThanOrEqual(180),    "{$alfa_str} >= 180°");
        $this->assertFalse($alfa->isGreaterThanOrEqual("180"),  "{$alfa_str} >= 180°");
        $this->assertFalse($alfa->isGreaterThanOrEqual($beta),  "{$alfa_str} >= {$beta_str}");
        $this->assertFalse($alfa->gte(180),                     "{$alfa_str} >= 180°");
        $this->assertFalse($alfa->gte("180"),                   "{$alfa_str} >= 180°");
        $this->assertFalse($alfa->gte($beta),                   "{$alfa_str} >= {$beta_str}");
        $this->assertTrue($beta->isGreaterThanOrEqual(180),     "{$alfa_str} >= 180°");
        $this->assertTrue($beta->isGreaterThanOrEqual("180"),   "{$alfa_str} >= 180°");
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
        $alfa = $this->createRandomAngleWithStubBuilder(FromDecimal::class, [90, 0, 0, Angle::CLOCKWISE]);
        $beta = $this->createRandomAngleWithStubBuilder(FromDecimal::class, [180, 0, 0, Angle::CLOCKWISE]);
        $alfa_str = (string) $alfa;
        $beta_str = (string) $beta;

        // Act & Assert
        $this->assertTrue($alfa->isLessThan(180),       "{$alfa_str} < 180°");
        $this->assertTrue($alfa->isLessThan("180"),     "{$alfa_str} < 180°");
        $this->assertTrue($alfa->isLessThan($beta),     "{$alfa_str} < {$beta_str}");
        $this->assertFalse($beta->isLessThan(90),       "{$beta_str} < 90");
        $this->assertFalse($beta->isLessThan("90"),     "{$beta_str} < 90");
        $this->assertFalse($beta->isLessThan($alfa),    "{$beta_str} < {$alfa_str}");
        $this->assertTrue($alfa->lt(180),               "{$alfa_str} < 180°");
        $this->assertTrue($alfa->lt("180"),             "{$alfa_str} < 180°");
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
        $alfa = $this->createRandomAngleWithStubBuilder(FromDecimal::class, [90, 0, 0, Angle::CLOCKWISE]);
        $beta = $this->createRandomAngleWithStubBuilder(FromDecimal::class, [180, 0, 0, Angle::CLOCKWISE]);
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
        $expected_type = ["int", "string"];
        $argument = "shabadula";
        $angle = $this->createRandomAngleWithStubBuilder(FromDecimal::class, [90, 0, 0, Angle::CLOCKWISE]);
        $class = new ReflectionClass($angle);
        $method = $class->getMethod("throwInvalidArgumentException");
        $method->setAccessible(true);
        
        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->getInvalidArgumentMessage($argument, $expected_type, "<the_method>", 1));
        $method->invoke($angle, $argument, $expected_type, "<the_method>", 1);
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

    /**
     * Gets a casting error message.
     *
     * @param string $type Type to cast to.
     * @return string
     */
    protected function getCastError(string $type): string
    {
        return "Something is not working when casting to $type.";
    }

    /**
     * Sets the builder expectation.
     *
     * @param string $builder The builder class
     * @param array  $data The data expected to be fetched from the builder.
     * @return Prophecy\Prophecy\MethodProphecy
     */
    protected function expectBuilderFetchData(string $builder, array $data)
    {
        if (class_exists($builder) && is_subclass_of($builder, AngleBuilder::class)) {
            return $this->prophesize($builder)->fetchData()->willReturn($data);
        }
        return null;
    }

    /**
     * Create a random angle with a stub builder.
     *
     * @param string $builder
     * @param mixed $expected_data
     * @return \MarcoConsiglio\Trigonometry\Angle
     */
    protected function createRandomAngleWithStubBuilder(string $builder, mixed $expected_data): Angle
    {
        return new Angle($this->expectBuilderFetchData($builder, $expected_data)->getObjectProphecy()->reveal());
    }
}