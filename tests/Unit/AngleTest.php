<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit;

use InvalidArgumentException;
use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\FromDecimal;
use MarcoConsiglio\Trigonometry\Tests\TestCase;
use MarcoConsiglio\Trigonometry\Builders\FromDegrees;
use MarcoConsiglio\Trigonometry\Interfaces\Angle as AngleInterface;
use MarcoConsiglio\Trigonometry\Interfaces\AngleBuilder;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\Builder\InvocationStubber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\AnyInvokedCount;
use PHPUnit\Framework\MockObject\Stub\Stub;
use ReflectionClass;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\MethodProphecy;
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
    }

    /**
     * @testdox has read-only properties "degrees", "minutes", "seconds", "direction".
     */
    public function test_getters()
    {
        // Arrange
        $failure_message = function (string $property) {
            return "$property property is not working correctly.";
        };
        $alfa = $this->getMockedAngle();
        $this->setAngleProperties($alfa, [1, 2, 3.4]);

        // Act & Assert
        $this->assertEquals(1, $alfa->degrees, $failure_message("degrees"));
        $this->assertEquals(2, $alfa->minutes, $failure_message("minutes"));
        $this->assertEquals(3.4, $alfa->seconds, $failure_message("seconds"));
        $this->assertEquals(Angle::CLOCKWISE, $alfa->direction, $failure_message("direction"));
        $this->assertNull($alfa->asganway);
    }

    /**
     * @testdox can give degrees, minutes and seconds wrapped in a simple array.
     */
    public function test_get_angle_values_in_simple_array()
    {
        // Arrange
        $alfa = $this->getMockedAngle();
        $angle_class = new ReflectionClass($alfa);
        $this->setAngleProperties($alfa, [1, 2, 3.4]);

        // Act
        $result = $alfa->getDegrees();

        // Assert
        $failure_message = "Can't get angle values as a simple array.";
        $this->assertEquals(1,   $result[0], $failure_message);
        $this->assertEquals(2,   $result[1], $failure_message);
        $this->assertEquals(3.4, $result[2], $failure_message);
    }

    /**
     * @testdox can give degrees, minutes and seconds wrapped in an associative array.
     */
    public function test_get_angle_values_in_assoc_array()
    {
        // Arrange
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

    /**
     * @testdox can be printed in a positive textual representation.
     */
    public function test_can_cast_positive_angle_to_string()
    {
        // Arrange
        $alfa = $this->getMockedAngle(["isCounterClockwise"]);
        $alfa->expects($this->anyTime())->method("isCounterClockwise")->willReturn(false);

        $this->setAngleProperties($alfa, [1, 2, 3.4]);
        $expected_string = "1° 2' 3.4\"";

        // Act & Assert
        $this->assertEquals($expected_string, (string) $alfa, $this->getCastError("string"));
    }

    /**
     * @testdox can be printed in a negative textual representation.
     */
    public function test_can_cast_negative_angle_to_string()
    {
        // Arrange
        $alfa = $this->getMockedAngle(["isCounterClockwise"]);
        $alfa->expects($this->anyTime())->method("isCounterClockwise")->willReturn(true);
        $this->setAngleProperties($alfa, [1, 2, 3.4]);
        $expected_string = "-1° 2' 3.4\"";

        // Act & Assert
        $this->assertEquals($expected_string, (string) $alfa, $this->getCastError("string"));
    }

    /**
     * @testdox can be casted to decimal.
     */
    public function test_can_cast_to_decimal()
    {
        // Arrange
        $alfa = $this->getMockedAngle();
        $this->setAngleProperties($alfa, [1, 2, 3.4]);

        // Act
        $decimal = $alfa->toDecimal();

        // Assert
        $this->assertIsFloat($decimal);
        $this->assertEquals(round($decimal, 6, PHP_ROUND_HALF_DOWN), 1.034278);
    }

    /**
     * @testdox can be casted to radiant.
     */
    public function test_cast_to_radiant()
    {
        // Arrange
        $alfa = $this->getMockedAngle(["toDecimal"]);
        $alfa->expects($this->once())->method("toDecimal")->willReturn(1.0342777777778);
        $this->setAngleProperties($alfa, [1, 2, 3.4]);

        // Act
        $radiant = $alfa->toRadiant();

        // Assert
        $this->assertEquals(0.018051552602, round($radiant, 12, PHP_ROUND_HALF_DOWN), $this->getCastError("radiant"));
    }

    /**
     * @testdox can be clockwise or positive.
     */
    public function test_angle_is_clockwise()
    {
        // Arrange
        $alfa = $this->getMockedAngle();

        // Act & assert
        $this->assertTrue($alfa->isClockwise(), "The angle is clockwise but found the opposite.");
    }

    /**
     * @testdox can be counterclockwise or negative.
     */
    public function test_angle_is_counterclockwise()
    {
        // Arrange
        $alfa = $this->getMockedAngle();

        // Act & assert
        $this->assertTrue($alfa->isClockwise(), "The angle is clockwise but found the opposite.");
    }

    /**
     * @testdox can be reversed from clockwise to counterclockwise.
     */
    public function test_can_toggle_rotation_from_clockwise_to_counterclockwise()
    {
        // Arrange
        $alfa = $this->getMockedAngle([]);
        $this->setAngleProperties($alfa, [1, 2, 3.4]);

        // Act
        $alfa->toggleDirection();

        // Assert
        $failure_message = "The angle should be counterclockwise but found the opposite";
        $this->assertEquals(Angle::COUNTER_CLOCKWISE, $alfa->direction, $failure_message);
    }

    /**
     * @testdox can be reversed from counterclockwise to clockwise.
     */
    public function test_can_toggle_rotation_from_counterclockwise_to_clockwise()
    {
        // Arrange
        $alfa = $this->getMockedAngle();
        $this->setAngleProperties($alfa, [1, 2, 3.4, Angle::COUNTER_CLOCKWISE]);

        // Act
        $alfa->toggleDirection();

        // Assert
        $failure_message = "The angle should be clockwise but found the opposite.";
        $this->assertEquals(Angle::CLOCKWISE, $alfa->direction, $failure_message);
    }

    /**
     * @testdox can be equal or not to another angle.
     */
    public function test_equal_comparison()
    {
        // Arrange
        $alfa = $this->getMockedAngle(["toDecimal"]);
        $beta = $this->getMockedAngle(["toDecimal"]);
        $alfa->expects($this->anyTime())->method("toDecimal")->willReturn(180.0);
        $beta->expects($this->anyTime())->method("toDecimal")->willReturn(180.0);

        // Act & Assert
        $this->assertAngleEqual($alfa, $beta);
    }

    /**
     * @testdox can throw an exception if equal comparison has an unexpected type argument.
     */
    public function test_equal_comparison_exception()
    {
        // Arrange
        $alfa = $this->getMockedAngle(["toDecimal"]);
        $alfa->expects($this->never())->method("toDecimal");

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->getInvalidArgumentMessage(
            true, ["int", "float", "string", Angle::class], Angle::class."::isEqual", 1
        ));
        $alfa->eq(true);
    }

    /**
     * @testdox can be or not greater than another.
     */
    public function test_greater_than_comparison()
    {
        // Arrange
        $alfa = $this->getMockedAngle(["toDecimal"]);
        $beta = $this->getMockedAngle(["toDecimal"]);
        $alfa->expects($this->atLeastOnce())->method("toDecimal")->willReturn(180.0);;
        $beta->expects($this->atLeastOnce())->method("toDecimal")->willReturn(90.0);;

        // Act & Assert
        $this->assertAngleGreaterThan($alfa, $beta);
    }

    /**
     * @testdox can throw an exception if greater than comparison has an unexpected type argument.
     */
    public function test_greater_than_comparison_exception()
    {
        // Arrange
        $alfa = $this->getMockedAngle(["toDecimal"]);
        $alfa->expects($this->never())->method("toDecimal");

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->getInvalidArgumentMessage(true, ["int", "float", "string", Angle::class], Angle::class."::isGreaterThan", 1));
        $alfa->gt(true); // Two birds with one stone.
    }

    /**
     * @testdox can be or not greater than or equal another angle.
     */
    public function test_greater_than_or_equal_comparison()
    {
        // Arrange
        $hide_methods = ["toDecimal", "isGreaterThan", "isEqual"];
        $alfa = $this->getMockedAngle($hide_methods);
        $beta = $this->getMockedAngle($hide_methods);
        $gamma = $this->getMockedAngle($hide_methods);
        $delta = $this->getMockedAngle($hide_methods);
        $alfa->expects($this->anyTime())->method("toDecimal")->willReturn(180.0);
        $alfa->expects($this->anyTime())->method("isEqual")->withConsecutive(["180"], [180.0], [$beta])->willReturn(true);
        $alfa->expects($this->never())->method("isGreaterThan");
        $beta->expects($this->anyTime())->method("toDecimal")->willReturn(180.0);
        $gamma->expects($this->anyTime())->method("toDecimal")->willReturn(360.0);
        $gamma->expects($this->anyTime())->method("isEqual")->willReturn(false);
        $gamma->expects($this->anyTime())->method("isGreaterThan")->withConsecutive(["-90"], [-90.0], [$delta])->willReturn(true);
        $delta->expects($this->anyTime())->method("toDecimal")->willReturn(-90.0);

        // Act & Assert
        $this->assertAngleGreaterThanOrEqual($alfa, $beta);
        $this->assertAngleGreaterThanOrEqual($gamma, $delta);
    }

    /**
     * @testdox can be or not less than another angle.
     */
    public function test_less_than_comparison()
    {
        // Arrange
        $alfa = $this->getMockedAngle(["toDecimal"]);
        $beta = $this->getMockedAngle(["toDecimal"]);
        $alfa->expects($this->anyTime())->method("toDecimal")->willReturn(180.0);
        $beta->expects($this->anyTime())->method("toDecimal")->willReturn(360.0);

        // Act & Assert
        $this->assertAngleLessThan($alfa, $beta);
    }

    /**
     * @testdox can throw an exception if less than comparison has an unexpected type argument.
     */
    public function test_less_than_comparison_exception()
    {
        // Arrange
        $alfa = $this->getMockedAngle(["toDecimal"]);
        $beta = $this->getMockedAngle(["toDecimal"]);
        $alfa->expects($this->anyTime())->method("toDecimal")->willReturn(-90.0);
        $beta->expects($this->anyTime())->method("toDecimal")->willReturn(180.0);

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($this->getInvalidArgumentMessage(true, ["int", "float", "string", Angle::class], Angle::class."::isLessThan", 1));
        $alfa->lt(true); // Two birds with one stone.
    }

    /**
     * @testdox can be or not greater than or equal another angle.
     */
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

        $this->assertAngleLessThanOrEqual($alfa, $beta);
        $this->assertAngleLessThanOrEqual($gamma, $delta);
    }

    /**
     * @testdox can throw InvalidArgumentException.
     */
    public function test_invalid_argument_exception()
    {
        // Arrange
        $expected_type = ["int", "string"];
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
     * Asserts $first_angle is greater than or equal to $second_angle. This is not a Custom Assertion bu a Parameterized Test.
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
        $failure_message = $first_angle->toDecimal() . " = " . $second_angle->toDecimal();
        $this->assertTrue($first_angle->isEqual((string) $second_angle->toDecimal()),   $failure_message);
        $this->assertTrue($first_angle->isEqual($second_angle->toDecimal()),            $failure_message);
        $this->assertTrue($first_angle->isEqual($second_angle),                         $failure_message);
        $this->assertTrue($first_angle->eq((string) $second_angle->toDecimal()),        $failure_message);
        $this->assertTrue($first_angle->eq($second_angle->toDecimal()),                 $failure_message);
        $this->assertTrue($first_angle->eq($second_angle),                              $failure_message);
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
        $this->assertTrue($first_angle->isLessThan((string) $second_angle->toDecimal()));
        $this->assertTrue($first_angle->isLessThan($second_angle->toDecimal()));
        $this->assertTrue($first_angle->isLessThan($second_angle));
        $this->assertTrue($first_angle->lt((string) $second_angle->toDecimal()));
        $this->assertTrue($first_angle->lt($second_angle->toDecimal()));
        $this->assertTrue($first_angle->lt($second_angle));
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

    /**
     * Constructs a mocked Angle.
     *
     * @param array $mocked_methods
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockedAngle(array $mocked_methods = []): MockObject
    {
        return $this->getMockBuilder(Angle::class)
            ->onlyMethods($mocked_methods)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Alias of any method.
     *
     * @return \PHPUnit\Framework\MockObject\Rule\AnyInvokedCount
     */
    public static function anyTime(): AnyInvokedCount
    {
        return self::any();
    }
}