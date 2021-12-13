<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit;

use InvalidArgumentException;
use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;
use MarcoConsiglio\Trigonometry\Tests\TestCase;
use MarcoConsiglio\Trigonometry\Exceptions\NoMatchException;
use Laracasts\TestDummy\Factory;
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
     * @testdox can be created from values.
     */
    public function test_creates_angle_from_degrees()
    {
        // Arrange in setUp()

        // Act
        $angle = Angle::createFromValues(
            $this->degrees, 
            $this->minutes, 
            $this->seconds
        );

        // Assert
        [$degrees, $minutes, $seconds] = $angle->getDegrees();
        $failure_message = "Cannot create angle from values {$angle->__toString()}.";
        $this->assertEquals($this->degrees, $degrees, $failure_message);
        $this->assertEquals($this->minutes, $minutes, $failure_message);
        $this->assertEquals($this->seconds, $seconds, $failure_message);
        $this->assertTrue($angle->isClockwise(), $failure_message);
    }

    /**
     * @testdox can be created from string.
     */
    public function test_creates_angle_from_string()
    {
        // Arrange in setUp()
        
        // Act
        $angle = Angle::createFromString($this->expected_string);
        [$actual_degrees, $actual_minutes, $actual_seconds] = $angle->getDegrees();

        // Assert
        $failure_message = "Cannot create angle from string.";
        $this->assertEquals($this->degrees, $actual_degrees, $failure_message);
        $this->assertEquals($this->minutes, $actual_minutes, $failure_message);
        $this->assertEquals($this->seconds, $actual_seconds, $failure_message);
        $this->assertTrue($angle->isClockwise(), $failure_message);
    }

    /**
     * @testdox can be created from decimal.
     * @depends test_cast_to_decimal
     */
    public function test_creates_angle_from_decimal()
    {
        // Arrange in setUp()

        // Act
        $angle = Angle::createFromDecimal($decimal = $this->angle->toDecimal());

        // Assert
        [$degrees, $minutes, $seconds] = $angle->getDegrees();
        $failure_message = "Cannot create angle from decimal $decimal.";
        $this->assertEquals($this->degrees, $degrees, $failure_message);
        $this->assertEquals($this->minutes, $minutes, $failure_message);
        $this->assertEquals($this->seconds, $seconds, $failure_message);
        $this->assertTrue($angle->isClockwise(), $failure_message);
    }

    /**
     * @testdox can be created from radiant.
     * @depends test_creates_angle_from_decimal
     */
    public function test_creates_angle_from_radiant()
    {
        // Arrange in setUp()

        // Act
        $test_angle = Angle::createFromRadiant($radiant = $this->angle->toRadiant());

        // Assert
        [$degrees, $minutes, $seconds] = $test_angle->getDegrees();
        $failure_message = "Cannot create angle from radiant $radiant.";
        $this->assertEquals($this->degrees, $degrees, $failure_message);
        $this->assertEquals($this->minutes, $minutes, $failure_message);
        $this->assertEquals($this->seconds, $seconds, $failure_message);        
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
     * @testdox cannot be created from exciding degrees.
     */
    public function test_cannot_create_with_exceding_degrees()
    {
        /**
         * Positive angle
         */
        // Arrange in setUp
        [$degrees, $minutes, $seconds] = $this->getOverflowDegrees();
        
        // Act & Assert
        $this->expectException(AngleOverflowException::class);
        $angle = Angle::createFromValues($degrees, $minutes, $seconds);
        
        /**
         * Negative angle
         */
        // Arrange
        [$degrees, $minutes, $seconds] = $this->getOverflowDegrees();
        $degrees *= Angle::COUNTER_CLOCKWISE;

        // Act & Assert
        $this->expectException(AngleOverflowException::class);
        $angle = Angle::createFromValues($degrees, $minutes, $seconds);
    }

    /**
     * @testdox cannot be created from exceding decimal.
     */
    public function test_cannot_create_with_exceding_decimal()
    {
        /**
         * Positive angle
         */
        // Act & Assert
        $this->expectException(AngleOverflowException::class);
        $angle = Angle::createFromDecimal($this->getOverflowDecimal());

        /**
         * Negative angle
         */
        // Act & Assert
        $this->expectException(AngleOverflowException::class);
        $angle = Angle::createFromDecimal(-$this->getOverflowDecimal());
    }

    /**
     * @testdox cannot be created from exceding radiant.
     * @covers \MarcoConsiglio\Trigonometry\Builders\FromRadiant::checkOverflow
     * @covers \MarcoConsiglio\Trigonometry\Builders\FromRadiant::exceedsRoundAngle
     */
    public function test_cannot_create_with_exceding_radiant()
    {
        /**
         * Positive angle
         */
        // Act & Assert
        $this->expectException(AngleOverflowException::class);
        $angle = Angle::createFromRadiant($this->getOverflowRadiant());

        /**
         * Negative angle
         */
        $this->expectException(AngleOverflowException::class);
        $angle = Angle::createFromRadiant(-$this->getOverflowRadiant());
    }

    /**
     * @testdox cannot be created if it is a string major than 360°.
     * @covers \MarcoConsiglio\Trigonometry\Builders\FromString::checkOverflow
     * @covers \MarcoConsiglio\Trigonometry\Exceptions\NoMatchException
     */
    public function test_cannot_create_with_exceding_string()
    {
        /**
         * Positive angle.
         */
        // Arrange
        [$degrees, $minutes, $seconds] = $this->getOverflowDegrees();
        $string = $degrees."° ".$minutes."' ".$seconds."\"";
        
        // Act & Assert
        $this->expectException(NoMatchException::class);
        $angle = Angle::createFromString($string);

        /**
         * Negative angle.
         */
        // Arrange
        [$degrees, $minutes, $seconds] = $this->getOverflowDegrees();
        $degrees *= Angle::COUNTER_CLOCKWISE;
        $string = "-".$degrees."° ".$minutes."' ".$seconds."\"";

        // Act & Assert
        $this->expectException(NoMatchException::class);
        $angle = Angle::createFromString($string);
    }

    /**
     * @testdox can be negative.
     */
    public function test_can_create_negative_angles()
    {
        /**
         * Creates from values.
         */
        // Arrange in setUp()

        // Act
        $angle = Angle::createFromValues(
            $this->degrees, 
            $this->minutes, 
            $this->seconds, 
            Angle::COUNTER_CLOCKWISE
        );

        // Assert
        $failure_message = "Cannot create negative angle from values.";
        [$degrees, $minutes, $seconds] = $angle->getDegrees();
        $this->assertEquals($this->degrees, $degrees, $failure_message);
        $this->assertEquals($this->minutes, $minutes, $failure_message);
        $this->assertEquals($this->seconds, $seconds, $failure_message);
        $this->assertTrue($angle->isCounterClockwise(), $failure_message);

        /**
         * Creates from string.
         */
        // Arrange
        $this->setUp();

        // Act
        $angle = Angle::createFromString("-".$this->expected_string);
        
        // Assert
        $failure_message = "Cannot create negative angle from string.";
        [$degrees, $minutes, $seconds] = $angle->getDegrees();
        $this->assertEquals($this->degrees, $degrees, $failure_message);
        $this->assertEquals($this->minutes, $minutes, $failure_message);
        $this->assertEquals($this->seconds, $seconds, $failure_message);
        $this->assertTrue($angle->isCounterClockwise(), $failure_message);

        /**
         * Creates from decimal.
         */
        // Arrange
        $this->setUp();

        // Act
        $angle = Angle::createFromDecimal($decimal = -$this->angle->toDecimal());

        // Assert
        $failure_message = "Cannot create negative angle from decimal $decimal.";
        [$degrees, $minutes, $seconds] = $angle->getDegrees();
        $this->assertEquals($this->degrees, $degrees, $failure_message);
        $this->assertEquals($this->minutes, $minutes, $failure_message);
        $this->assertEquals($this->seconds, $seconds, $failure_message);

        /**
         * Creates from radiant.
         */
        // Arrange
        $this->setUp();

        // Act
        $angle = Angle::createFromRadiant($radiant = -$this->angle->toRadiant());

        // Assert
        $failure_message = "Cannot create negative angle from radiant $radiant.";
        [$degrees, $minutes, $seconds] = $angle->getDegrees();
        $this->assertEquals($this->degrees, $degrees, $failure_message);
        $this->assertEquals($this->minutes, $minutes, $failure_message);
        $this->assertEquals($this->seconds, $seconds, $failure_message);

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
    public function test_cast_to_radian()
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
        
        // Act & Assert
        $this->assertEquals(false, $alfa->isGreaterThan(180),       "{$alfa->__toString()} > 180°");
        $this->assertEquals(false, $alfa->isGreaterThan("180"),       "{$alfa->__toString()} > '180°'");
        $this->assertEquals(false, $alfa->isGreaterThan($beta),     "{$alfa->__toString()} > {$beta->__toString()}");
        $this->assertEquals(false, $alfa->gt(180),                  "{$alfa->__toString()} > 180°");
        $this->assertEquals(false, $alfa->gt($beta),                "{$alfa->__toString()} > 180°");
        $this->assertEquals(true, $beta->isGreaterThan(90),         "{$beta->__toString()} > 90°");
        $this->assertEquals(true, $beta->isGreaterThan($alfa),      "{$beta->__toString()} > {$alfa->__toString()}");
        $this->assertEquals(true, $gamma->isGreaterThan(-180),      "{$gamma->__toString()} > -180°");
        $this->assertEquals(true, $gamma->isGreaterThan($delta),    "{$gamma->__toString()} > {$delta->__toString()}");
        $this->assertEquals(false, $delta->isGreaterThan(-90),      "{$delta->__toString()} > -90°");
        $this->assertEquals(false, $delta->isGreaterThan($gamma),   "{$delta->__toString()} > {$gamma->__toString()}");
        $this->assertEquals(false, $alfa->isGreaterThan(90),        "{$alfa->__toString()} > 90°");
        $this->expectException(InvalidArgumentException::class);
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

        // Act & Assert
        $this->assertEquals(false, $alfa->isGreaterThanOrEqual(180),    "{$alfa->__toString()} > 180°");
        $this->assertEquals(false, $alfa->isGreaterThanOrEqual($beta),  "{$alfa->__toString()} > {$beta->__toString()}");
        $this->assertEquals(true, $beta->isGreaterThanOrEqual(180),     "{$alfa->__toString()} > {$beta->__toString()}");
        $this->assertEquals(true, $beta->isGreaterThanOrEqual($beta),   "{$alfa->__toString()} > {$beta->__toString()}");
        $this->expectException(InvalidArgumentException::class);
        $alfa->isGreaterThanOrEqual(true);
    }

    /**
     * Gets the angle values whose major than 360°.
     *
     * @return array
     */
    protected function getOverflowDegrees(): array
    {
        return [
            $this->faker->numberBetween(361, 999),
            $this->faker->numberBetween(61, 100),
            $this->faker->numberBetween(61, 100)
        ];
    }

    /**
     * Gets the angle decimal major than 360°.
     *
     * @return float
     */
    protected function getOverflowDecimal(): float
    {
        return $this->faker->randomFloat(1, 360.1, 999.0);
    }

    /**
     * Gets the angle radiant major than 360°.
     *
     * @return float
     */
    protected function getOverflowRadiant(): float
    {
        return $this->faker->randomFloat(1, Angle::MAX_RADIANT + 0.1, 10);
    }
}