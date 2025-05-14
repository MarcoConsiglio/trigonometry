<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit;

use InvalidArgumentException;
use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\AngleBuilder;
use MarcoConsiglio\Trigonometry\Builders\FromAngles;
use MarcoConsiglio\Trigonometry\Builders\FromDecimal;
use MarcoConsiglio\Trigonometry\Builders\FromDegrees;
use MarcoConsiglio\Trigonometry\Builders\FromRadiant;
use MarcoConsiglio\Trigonometry\Builders\FromString;
use MarcoConsiglio\Trigonometry\Builders\SumBuilder;
use MarcoConsiglio\Trigonometry\Interfaces\Angle as AngleInterface;
use MarcoConsiglio\Trigonometry\Operations\Sum;
use MarcoConsiglio\Trigonometry\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use ReflectionClass;

#[TestDox("An angle")]
#[CoversClass(Angle::class)]
#[UsesClass(AngleBuilder::class)]
#[UsesClass(FromString::class)]
#[UsesClass(FromDecimal::class)]
#[UsesClass(FromDegrees::class)]
#[UsesClass(FromRadiant::class)]
#[UsesClass(Sum::class)]
#[UsesClass(SumBuilder::class)]
#[UsesClass(FromAngles::class)]
// #[UsesClass(InvalidArgumentException::class)]
class AngleTest extends TestCase
{
    /**
     * The expected degrees, minutes, seconds e angle direction.
     *
     * @var array
     */
    protected array $expected;

    /*
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->expected = $this->getRandomAngleDegrees();
    }

    #[TestDox("has read-only properties \"degrees\", \"minutes\", \"seconds\", \"direction\".")]
    public function test_getters()
    {
        // Arrange
        $failure_message = function (string $property) {
            return "$property property is not working correctly.";
        };
        /** @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $alfa */
        $alfa = $this->getMockedAngle();
        $this->setAngleProperties($alfa, [1, 2, 3.4]);

        // Act & Assert
        $this->assertEquals(1, $alfa->degrees, $failure_message("degrees"));
        $this->assertEquals(2, $alfa->minutes, $failure_message("minutes"));
        $this->assertEquals(3.4, $alfa->seconds, $failure_message("seconds"));
        $this->assertEquals(Angle::COUNTER_CLOCKWISE, $alfa->direction, $failure_message("direction"));
        $this->assertNull($alfa->asganway);
    }

    #[TestDox("can be created from separated values for degrees, minutes, seconds and direction.")]
    public function test_create_from_values()
    {
        // Arrange
        $degrees = $this->faker->numberBetween(0, 360);
        $minutes = $this->faker->numberBetween(0, 59);
        $seconds = $this->faker->numberBetween(0, 59);
        $direction = $this->faker->randomElement([Angle::COUNTER_CLOCKWISE, Angle::CLOCKWISE]);

        // Act
        $angle = Angle::createFromValues($degrees, $minutes, $seconds, $direction);

        // Assert
        $this->assertAngleHaveValues($angle, [
            "degrees" => $degrees * $direction,
            "minutes" => $minutes,
            "seconds" => $seconds,
        ]);
    }

    #[TestDox("can be created from a text representation.")]
    public function test_create_from_string()
    {
        // Arrange
        $degrees = $this->faker->numberBetween(0, 360);
        $minutes = $this->faker->numberBetween(0, 59);
        $seconds = $this->faker->numberBetween(0, 59);
        $direction = $this->faker->randomElement(["-", ""]);
        $text = "{$direction}{$degrees}° {$minutes}' {$seconds}\"";

        // Act
        $angle = Angle::createFromString($text);

        // Act
        $this->assertAngleHaveValues($angle, [
            "degrees" => $direction == "-" ? -$degrees : $degrees,
            "minutes" => $minutes,
            "seconds" => $seconds,
        ]);
    }

    #[TestDox("can be created from a decimal number.")]
    public function test_create_from_decimal()
    {
        // Arrange
        $precision = 5;
        $decimal = $this->faker->randomFloat($precision, -360, 360);

        // Act
        $angle = Angle::createFromDecimal($decimal);

        $this->assertEquals($decimal, $angle->toDecimal($precision));
    }

    #[TestDox("can be created from a radiant number.")]
    public function test_create_from_radiant()
    {
        // Arrange
        $precision = 5;
        $radiant = $this->faker->randomFloat($precision, -Angle::MAX_RADIANT, Angle::MAX_RADIANT);

        // Act
        $angle = Angle::createFromRadiant($radiant);

        // Assert
        $this->assertEquals($radiant, $angle->toRadiant($precision));
    }

    #[TestDox("can output degrees, minutes and seconds wrapped in a simple array.")]
    public function test_get_angle_values_in_simple_array()
    {
        // Arrange
        /** @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $alfa */
        $alfa = $this->getMockedAngle();
        $this->setAngleProperties($alfa, [1, 2, 3.4]);

        // Act
        $result = $alfa->getDegrees();

        // Assert
        $failure_message = "Can't get angle values as a simple array.";
        $this->assertEquals(1,   $result[0], $failure_message);
        $this->assertEquals(2,   $result[1], $failure_message);
        $this->assertEquals(3.4, $result[2], $failure_message);
    }

    #[TestDox("can output degrees, minutes and seconds wrapped in an associative array.")]
    public function test_get_angle_values_in_assoc_array()
    {
        // Arrange
        /** @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $alfa */
        $alfa = $this->getMockedAngle();
        $this->setAngleProperties($alfa, [1, 2, 3.4]);

        // Act
        $result = $alfa->getDegrees(associative: true);

        // Assert
        $failure_message = "Can't get angle values as a simple array.";
        $this->assertEquals(1,   $result["degrees"], $failure_message);
        $this->assertEquals(2,   $result["minutes"], $failure_message);
        $this->assertEquals(3.4, $result["seconds"], $failure_message);
    }

    #[TestDox("can be printed in a positive textual representation.")]
    public function test_can_cast_positive_angle_to_string()
    {
        // Arrange
        /** @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $alfa */
        $alfa = $this->getMockedAngle(["isCounterClockwise"]);
        $alfa->expects($this->anyTime())->method("isCounterClockwise")->willReturn(false);

        $this->setAngleProperties($alfa, [1, 2, 3.4]);
        $expected_string = "1° 2' 3.4\"";

        // Act & Assert
        $this->assertEquals($expected_string, (string) $alfa, $this->getCastError("string"));
    }
    
    #[TestDox("can be printed in a negative textual representation.")]
    public function test_can_cast_negative_angle_to_string()
    {
        // Arrange
        /** @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $alfa */
        $alfa = $this->getMockedAngle(["isClockwise"]);
        $alfa->expects($this->anyTime())->method("isClockwise")->willReturn(true);
        $this->setAngleProperties($alfa, [1, 2, 3.4, Angle::CLOCKWISE]);
        $expected_string = "-1° 2' 3.4\"";

        // Act & Assert
        $this->assertEquals($expected_string, (string) $alfa, $this->getCastError("string"));
    }

    #[TestDox("can be casted to decimal.")]
    public function test_can_cast_to_decimal()
    {
        // Arrange
        $precision = 5;
        $decimal = $this->faker->randomFloat($precision, -360, 360);
        $angle = Angle::createFromDecimal($decimal);

        // Act & Assert
        $result = $angle->toDecimal($precision);
        $this->assertIsFloat($result);
        $this->assertEquals($decimal, $result);
    }

    #[TestDox("can be casted to radiant.")]
    public function test_cast_to_radiant()
    {
        // Arrange
        $precision = 5;
        $radiant = $this->faker->randomFloat($precision, -Angle::MAX_RADIANT, Angle::MAX_RADIANT);
        $angle = Angle::createFromRadiant($radiant);

        // Act & Assert
        $this->assertEquals($radiant, $angle->toRadiant($precision), $this->getCastError("radiant"));
    }

    #[TestDox("can be clockwise or negative.")]
    public function test_angle_is_clockwise()
    {
        // Arrange
        /** @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $alfa */
        $alfa = $this->getMockedAngle();
        $this->setAngleProperties($alfa, [1, 0, 0, Angle::CLOCKWISE]);

        // Act & assert
        $this->assertTrue($alfa->isClockwise(), "The angle is clockwise but found the opposite.");
    }

    #[TestDox("can be counterclockwise or positive.")]
    public function test_angle_is_counterclockwise()
    {
        // Arrange
        /** @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $alfa */
        $alfa = $this->getMockedAngle();

        // Act & assert
        $this->assertTrue($alfa->isCounterClockwise(), "The angle is clockwise but found the opposite.");
    }

    #[TestDox("can be reversed from counterclockwise to clockwise.")]
    public function test_can_toggle_rotation_from_clockwise_to_counterclockwise()
    {
        // Arrange
        /** @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $alfa */
        $alfa = $this->getMockedAngle([]);
        $this->setAngleProperties($alfa, [1, 2, 3.4]);

        // Act
        $alfa->toggleDirection();

        // Assert
        $failure_message = "The angle should be counterclockwise but found the opposite";
        $this->assertEquals(Angle::CLOCKWISE, $alfa->direction, $failure_message);
    }

    #[TestDox("can be reversed from clockwise to counterclockwise.")]
    public function test_can_toggle_rotation_from_counterclockwise_to_clockwise()
    {
        // Arrange
        /** @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $alfa */
        $alfa = $this->getMockedAngle();
        $this->setAngleProperties($alfa, [1, 2, 3.4, Angle::COUNTER_CLOCKWISE]);

        // Act
        $alfa->toggleDirection();

        // Assert
        $failure_message = "The angle should be clockwise but found the opposite.";
        $this->assertEquals(Angle::CLOCKWISE, $alfa->direction, $failure_message);
    }

    #[TestDox("can be tested if it is equal to another string angle.")]
    public function test_equal_comparison_with_string()
    {
        // Arrange
        $alfa = $this->getRandomAngle($this->faker->boolean());
        $beta = (string) $alfa;
        $precision = 14;
        
        // Act
        $equal_1 = $alfa->isEqual($beta, $precision);
        $equal_2 = $alfa->eq($beta, $precision);
        
        //Assert
        $this->assertTrue($equal_1);
        $this->assertTrue($equal_2);
    }

    #[TestDox("can be tested if it is equal to another integer angle.")]
    public function test_equal_comparison_with_integer()
    {
        // Arrange
        /** @var int $degrees */
        $degrees = $this->faker->numberBetween(0, -360, 360);
        $alfa = Angle::createFromValues(abs($degrees), 0, 0, $degrees < 0 ? Angle::CLOCKWISE : Angle::COUNTER_CLOCKWISE);

        // Act
        $equal_1 = $alfa->isEqual($degrees);
        $equal_2 = $alfa->eq($degrees);

        // Assert
        $this->assertTrue($equal_1);
        $this->assertTrue($equal_2);
    }

    #[TestDox("can be tested if it is equal to another decimal angle.")]
    public function test_equal_comparison_with_float()
    {
        // Arrange
        $precision = 14;
        $decimal = $this->getRandomAngleDecimal($this->faker->boolean(), $precision);
        $alfa = Angle::createFromDecimal($decimal);

        // Act
        $equal_1 = $alfa->isEqual($decimal, $precision);
        $equal_2 = $alfa->eq($decimal, $precision);

        // Assert
        $this->assertTrue($equal_1);
        $this->assertTrue($equal_2);
    }

    #[TestDox("can be tested if it is equal to another Angle instance.")]
    public function test_equal_comparison_with_angle()
    {
        // Arrange
        $alfa = $this->getRandomAngle($this->faker->boolean());
        $beta = $alfa;

        // Act
        $equal_1 = $alfa->isEqual($beta);
        $equal_2 = $alfa->eq($beta);

        // Assert
        $this->assertTrue($equal_1);
        $this->assertTrue($equal_2);
    }

    #[TestDox("can throw an exception if equal comparison has an unexpected type argument.")]
    public function test_equal_comparison_exception()
    {
        // Arrange
        $alfa = $this->getRandomAngle($this->faker->boolean());

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->getInvalidArgumentMessage(
            true, ["int", "float", "string", Angle::class], Angle::class."::isEqual", 1
        ));
        $alfa->eq(true);
    }

    #[TestDox("can be tested if it is greater than another string angle")]
    public function test_greater_than_comparison_with_string()
    {
        // Arrange
        $precision = 14;
        $alfa = $this->getRandomAngle($this->faker->boolean());
        $addend = Angle::createFromValues(1 /* degrees */, 0 /* minutes */, 0 /* seconds */,
            $alfa->isClockwise() ? Angle::COUNTER_CLOCKWISE : Angle::CLOCKWISE
        );
        // Get an angle less than $alfa.
        $beta = new Sum(new FromAngles($alfa, $addend));

        // Act
        $greater_than_1 = $alfa->isGreaterThan((string) $beta, $precision);
        $greater_than_2 = $alfa->gt((string) $beta, $precision);

        // Assert
        $this->assertTrue($greater_than_1);
        $this->assertTrue($greater_than_2);
    }

    #[TestDox("can be tested if it is greater than another integer angle")]
    public function test_greater_than_comparison_with_integer()
    {
        // Arrange
        
    }

    #[TestDox("can throw an exception if greater than comparison has an unexpected type argument.")]
    public function test_greater_than_comparison_exception()
    {
        // Arrange
        /** @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $alfa  */
        $alfa = $this->getMockedAngle(["toDecimal"]);
        $alfa->expects($this->never())->method("toDecimal");

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->getInvalidArgumentMessage(true, ["int", "float", "string", Angle::class], Angle::class."::isGreaterThan", 1));
        $alfa->gt(true); // Two birds with one stone.
    }

    #[TestDox("can be or not greater than or equal another angle.")]
    public function test_greater_than_or_equal_comparison()
    {
        // Arrange
        $hide_methods = ["toDecimal", "isGreaterThan", "isEqual"];
        $alfa = $this->getMockedAngle($hide_methods);
        $beta = $this->getMockedAngle($hide_methods);
        $gamma = $this->getMockedAngle($hide_methods);
        $delta = $this->getMockedAngle($hide_methods);
        $alfa->expects($this->anyTime())->method("toDecimal")->willReturn(180.0);
        $alfa->expects($this->anyTime())->method("isEqual")->willReturnOnConsecutiveCalls(["180"], [180.0], [$beta])->willReturn(true);
        $alfa->expects($this->never())->method("isGreaterThan");
        $beta->expects($this->anyTime())->method("toDecimal")->willReturn(180.0);
        $gamma->expects($this->anyTime())->method("toDecimal")->willReturn(360.0);
        $gamma->expects($this->anyTime())->method("isEqual")->willReturn(false);
        $gamma->expects($this->anyTime())->method("isGreaterThan")->willReturnOnConsecutiveCalls(["-90"], [-90.0], [$delta])->willReturn(true);
        $delta->expects($this->anyTime())->method("toDecimal")->willReturn(-90.0);
        
        // Act & Assert
        /** 
         * @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $alfa
         * @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $beta
         * @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $gamma
         * @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $delta
         */
        $this->assertAngleGreaterThanOrEqual($alfa, $beta);
        $this->assertAngleGreaterThanOrEqual($gamma, $delta);
    }


    #[TestDox("can be or not less than another angle.")]
    public function test_less_than_comparison()
    {
        // Arrange
        $alfa = $this->getMockedAngle(["toDecimal"]);
        $beta = $this->getMockedAngle(["toDecimal"]);
        $gamma = $this->getMockedAngle(["toDecimal"]);
        $alfa->expects($this->anyTime())->method("toDecimal")->willReturn(180.0);
        $beta->expects($this->anyTime())->method("toDecimal")->willReturn(360.0);
        $gamma->expects($this->anyTime())->method("toDecimal")->willReturn(360.0);
        
        // Act & Assert
        /** 
         * @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $alfa
         * @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $beta
         * @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $gamma
         */
        $this->assertAngleLessThan($alfa, $beta);
        $this->assertAngleNotLessThan($beta, $gamma);
    }

    #[TestDox("can throw an exception if less than comparison has an unexpected type argument.")]
    public function test_less_than_comparison_exception()
    {
        // Arrange
        /** 
         * @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $alfa 
         * @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $beta 
         */
        $alfa = $this->getMockedAngle(["toDecimal"]);
        $beta = $this->getMockedAngle(["toDecimal"]);
        $alfa->expects($this->anyTime())->method("toDecimal")->willReturn(-90.0);
        $beta->expects($this->anyTime())->method("toDecimal")->willReturn(180.0);

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->getInvalidArgumentMessage(true, ["int", "float", "string", Angle::class], Angle::class."::isLessThan", 1));
        $alfa->lt(true); // Two birds with one stone.
    }

    #[TestDox("can be or not greater than or equal another angle.")]
    public function test_less_than_or_equal_comparison()
    {
        // Arrange
        $hide_methods = ["toDecimal", "isLessThan", "isEqual"];
        $alfa = $this->getMockedAngle($hide_methods);
        $beta = $this->getMockedAngle($hide_methods);
        $gamma = $this->getMockedAngle($hide_methods);
        $delta = $this->getMockedAngle($hide_methods);
        $alfa->expects($this->anyTime())->method("toDecimal")->willReturn(-90.0);
        $alfa->expects($this->anyTime())->method("isEqual")->willReturn(false);
        $alfa->expects($this->anyTime())->method("isLessThan")->willReturn(true);
        $beta->expects($this->anyTime())->method("toDecimal")->willReturn(180.0);
        $gamma->expects($this->anyTime())->method("toDecimal")->willReturn(-360.0);
        $gamma->expects($this->anyTime())->method("isEqual")->willReturn(true);
        $gamma->expects($this->never())->method("isLessThan");
        $delta->expects($this->anyTime())->method("toDecimal")->willReturn(-360.0);
        /** 
         * @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $alfa
         * @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $beta
         * @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $gamma
         * @var \MarcoConsiglio\Trigonometry\Angle&\PHPUnit\Framework\MockObject\MockObject $delta
         */
        $this->assertAngleLessThanOrEqual($alfa, $beta);
        $this->assertAngleLessThanOrEqual($gamma, $delta);
    }

    #[TestDox("can throw InvalidArgumentException.")]
    public function test_invalid_argument_exception()
    {
        // Arrange
        $expected_type = ["int", "float", "string", Angle::class];
        $argument = "shabadula";
        $alfa = $this->getMockedAngle();
        $class = new ReflectionClass($alfa);
        $method = $class->getMethod("throwInvalidArgumentException");
        $method->setAccessible(true);
        
        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->getInvalidArgumentMessage($argument, $expected_type, "<the_method>", 1));
        $method->invoke($alfa, $argument, $expected_type, "<the_method>", 1);
    }

    /**
     * Gets an invalid argument message fixture.
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
     * Asserts $first_angle is greater than $second_angle. This is not a Custom Assertion but a Parameterized Test.
     *
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $first_angle
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $second_angle
     * @return void
     */
    protected function assertAngleGreaterThan(AngleInterface $first_angle, AngleInterface $second_angle)
    {
        $failure_message = $first_angle->toDecimal() . " > " . $second_angle->toDecimal();

        $this->assertTrue($first_angle->isGreaterThan((string) $second_angle->toDecimal()));
        $this->assertTrue($first_angle->isGreaterThan($second_angle->toDecimal()));
        $this->assertTrue($first_angle->isGreaterThan($second_angle));
        $this->assertTrue($first_angle->gt((string) $second_angle->toDecimal()));
        $this->assertTrue($first_angle->gt($second_angle->toDecimal()));
        $this->assertTrue($first_angle->gt($second_angle));

        $this->assertFalse($second_angle->isGreaterThan((string) $first_angle->toDecimal()));
        $this->assertFalse($second_angle->isGreaterThan($second_angle->toDecimal()));
        $this->assertFalse($second_angle->isGreaterThan($second_angle));
        $this->assertFalse($second_angle->gt((string) $first_angle->toDecimal()));
        $this->assertFalse($second_angle->gt($second_angle->toDecimal()));
        $this->assertFalse($second_angle->gt($second_angle));
    }

    /**
     * Asserts $first_angle is greater than or equal to $second_angle. This is not a Custom Assertion but a Parameterized Test.
     *
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $first_angle
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $second_angle
     * @return void
     */
    protected function assertAngleGreaterThanOrEqual(AngleInterface $first_angle, AngleInterface $second_angle)
    {
        $failure_message = $first_angle->toDecimal() . " >= " . $second_angle->toDecimal();
        $this->assertTrue($first_angle->isGreaterThanOrEqual((string) $second_angle->toDecimal()),  $failure_message);
        $this->assertTrue($first_angle->isGreaterThanOrEqual($second_angle->toDecimal()),           $failure_message);
        $this->assertTrue($first_angle->isGreaterThanOrEqual($second_angle),                        $failure_message);
        $this->assertTrue($first_angle->gte((string) $second_angle->toDecimal()),                   $failure_message);
        $this->assertTrue($first_angle->gte($second_angle->toDecimal()),                            $failure_message);
        $this->assertTrue($first_angle->gte($second_angle),                                         $failure_message);
    }

    /**
     * Asserts $first_angle is equal to $second_angle. This is not a Custom Assertion but a Parameterized Test.
     *
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $first_angle
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $second_angle
     * @return void
     */
    protected function assertAngleEqual(AngleInterface $first_angle, AngleInterface $second_angle)
    {
        $failure_message = $first_angle->toDecimal() . " != " . $second_angle->toDecimal();
        $this->assertTrue($first_angle->isEqual((string) $second_angle->toDecimal()),   $failure_message);
        $this->assertTrue($first_angle->isEqual($second_angle->toDecimal()),            $failure_message);
        $this->assertTrue($first_angle->isEqual($second_angle),                         $failure_message);
        $this->assertTrue($first_angle->eq((string) $second_angle->toDecimal()),        $failure_message);
        $this->assertTrue($first_angle->eq($second_angle->toDecimal()),                 $failure_message);
        $this->assertTrue($first_angle->eq($second_angle),                              $failure_message);
    }

    /**
     * Assert the passed $values are the same of $angle. This is a Custom Assertion.
     *
     * @param AngleInterface $angle The angle being tested.
     * @param array $values The expected values of the angle.
     * @return void
     */
    protected function assertAngleHaveValues(AngleInterface $angle, array $values)
    {
        $expected_values = $angle->getDegrees(true);
        $this->assertEquals($expected_values["degrees"], $values["degrees"]);
        $this->assertEquals($expected_values["minutes"], $values["minutes"]);
        $this->assertEquals($expected_values["seconds"], $values["seconds"]);
    }

    /**
     * Asserts that $first_angle is less than $second_angle.
     *
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $first_angle
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $second_angle
     * @return void
     */
    protected function assertAngleLessThan(AngleInterface $first_angle, AngleInterface $second_angle)
    {
        $failure_message = $first_angle->toDecimal() . " < " . $second_angle->toDecimal();           
        $this->assertTrue($first_angle->isLessThan((string) $second_angle->toDecimal()), $failure_message);
        $this->assertTrue($first_angle->isLessThan($second_angle->toDecimal()),          $failure_message);
        $this->assertTrue($first_angle->isLessThan($second_angle),                       $failure_message);
        $this->assertTrue($first_angle->lt((string) $second_angle->toDecimal()),         $failure_message);
        $this->assertTrue($first_angle->lt($second_angle->toDecimal()),                  $failure_message);
        $this->assertTrue($first_angle->lt($second_angle),                               $failure_message);
    }

    /**
     * Asserts that $first_angle is NOT less than $second_angle.
     *
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $first_angle
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $second_angle
     * @return void
     */
    protected function assertAngleNotLessThan(AngleInterface $first_angle, AngleInterface $second_angle)
    {
        $failure_message = $first_angle->toDecimal() . " >= " . $second_angle->toDecimal();           
        $this->assertFalse($first_angle->isLessThan((string) $second_angle->toDecimal()), $failure_message);
        $this->assertFalse($first_angle->isLessThan($second_angle->toDecimal()),          $failure_message);
        $this->assertFalse($first_angle->isLessThan($second_angle),                       $failure_message);
        $this->assertFalse($first_angle->lt((string) $second_angle->toDecimal()),         $failure_message);
        $this->assertFalse($first_angle->lt($second_angle->toDecimal()),                  $failure_message);
        $this->assertFalse($first_angle->lt($second_angle),                               $failure_message);
    }

    /**
     * Asserts $first_angle is less than or equal to $second_angle. This is not a Custom Assertion bu a Parameterized Test.
     *
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $first_angle
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $second_angle
     * @return void
     */
    protected function assertAngleLessThanOrEqual(AngleInterface $first_angle, AngleInterface $second_angle)
    {
        $failure_message = $first_angle->toDecimal() . " <= " . $second_angle->toDecimal();
        $this->assertTrue($first_angle->isLessThanOrEqual((string) $second_angle->toDecimal()),  $failure_message);
        $this->assertTrue($first_angle->isLessThanOrEqual($second_angle->toDecimal()),           $failure_message);
        $this->assertTrue($first_angle->isLessThanOrEqual($second_angle),                        $failure_message);
        $this->assertTrue($first_angle->lte((string) $second_angle->toDecimal()),                $failure_message);
        $this->assertTrue($first_angle->lte($second_angle->toDecimal()),                         $failure_message);
        $this->assertTrue($first_angle->lte($second_angle),                                      $failure_message);
    }
}