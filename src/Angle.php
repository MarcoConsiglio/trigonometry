<?php 
namespace MarcoConsiglio\Trigonometry;

use InvalidArgumentException;
use MarcoConsiglio\Trigonometry\Builders\FromDecimal;
use MarcoConsiglio\Trigonometry\Builders\FromDegrees;
use MarcoConsiglio\Trigonometry\Builders\FromRadiant;
use MarcoConsiglio\Trigonometry\Builders\FromString;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;
use MarcoConsiglio\Trigonometry\Exceptions\NoMatchException;
use MarcoConsiglio\Trigonometry\Exceptions\RegExFailureException;
use MarcoConsiglio\Trigonometry\Interfaces\Angle as AngleInterface;
use MarcoConsiglio\Trigonometry\Interfaces\AngleBuilder;

/**
 * Represents an angle.
 */
class Angle implements AngleInterface
{
    /**
     *  Angle regular expression used to parse degrees, minutes and seconds values.
     * @see https://regex101.com/r/OQCxIV/1
     */
    public const ANGLE_REGEX = '/^(?:(-?360(*ACCEPT))|(-?[1-3]?[0-9]?[0-9]?))°?\s?([0-5]?[0-9])?\'?\s?([0-5]?[0-9](?:.{1}[0-9])?)?"?$/';
   
    /**
     * It represents a positive angle.
     */
    public const CLOCKWISE = 1;

    /**
     * It represents a negative angle.
     */
    public const COUNTER_CLOCKWISE = -1;

    /**
     * The degrees of a round angle.
     */
    public const MAX_DEGREES = 360;

    /**
     * The minutes of a round angle.
     */
    public const MAX_MINUTES = self::MAX_DEGREES * 60;

    /**
     * The seconds of a round angle.
     */
    public const MAX_SECONDS = self::MAX_MINUTES * 60;

    /**
     * Radiant measure of a round angle.
     */
    public const MAX_RADIANT = 2 * M_PI;

    /**
     * The degrees part.
     *
     * @var integer
     */
    protected int $degrees;

    /**
     * The minutes part.
     *
     * @var integer
     */
    protected int $minutes;

    /**
     * The seconds part.
     *
     * @var integer
     */
    protected float $seconds;

    /** 
     * The angle direction. 
     * self::CLOCKWISE means positive angle,
     * self::COUNTERCLOCKWISE means negative angle.
     */
    protected int $direction;

    /**
     * Construct an angle.
     *
     * @param \MarcoConsiglio\Trigonometry\Builders\AngleBuilder $builder
     */
    protected function __construct(AngleBuilder $builder)
    {
        [$this->degrees, $this->minutes, $this->seconds, $this->direction] = $builder->fetchData();
    }

    /**
     * Creates an angle from its values in degrees.
     *
     * @param integer $degrees
     * @param integer $minutes
     * @param float $seconds
     * @return Angle
     * @throws \MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException when creating an angle greater than 360°.
     */
    public static function createFromValues(int $degrees, int $minutes, float $seconds, int $direction = self::CLOCKWISE): Angle
    {
        return new Angle(new FromDegrees($degrees, $minutes, $seconds, $direction));
    }

    /**
     * Creates an angle from its textual representation.
     *
     * @param string $angle
     * @return Angle
     * @throws \MarcoConsiglio\Trigonometry\Exceptions\NoMatchException when $angle has no match.
     * @throws \MarcoConsiglio\Trigonometry\Exceptions\RegExFailureException when there's a failure in regex parser engine.
     */
    public static function createFromString(string $angle): Angle
    {
        return new Angle(new FromString($angle));
    }

    /**
     * Creates an angle from its decimal representation.
     *
     * @param float $decimal_degrees
     * @return Angle
     * @throws \MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException when creating an angle greater than 360°.
     */
    public static function createFromDecimal(float $decimal_degrees): Angle
    {
        return new Angle(new FromDecimal($decimal_degrees));
    }

    /**
     * Creates an angle from its radiant representation.
     *
     * @param float $radiant
     * @return Angle
     * @throws \MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException when creating an angle greater than 360°.
     */
    public static function createFromRadiant(float $radiant): Angle
    {
        return new Angle(new FromRadiant($radiant));
    }

    /**
     * Return an array containing the values
     * of degrees, minutes and seconds.
     *
     * @param bool $associative Gets an associative array.
     * @return array
     */
    public function getDegrees(bool $associative = false): array
    {
        if ($associative) {
            return [
                "degrees" => $this->degrees,
                "minutes" => $this->minutes,
                "seconds" => $this->seconds
            ];
        } else {
            return [
                $this->degrees,
                $this->minutes,
                $this->seconds
            ];
        }
    }

    /**
     * Check if this angle is clockwise or positive.
     *
     * @return boolean
     */
    public final function isClockwise(): bool
    {
        return $this->direction == self::CLOCKWISE;
    }

    /**
     * Check if this angle is counterclockwise or negative.
     *
     * @return boolean
     */
    public function isCounterClockwise(): bool
    {
        return $this->direction == self::COUNTER_CLOCKWISE;
    }

    /**
     * Reverse the direction of rotation.
     *
     * @return Angle
     */
    public final function toggleDirection(): Angle
    {
        $this->direction *= self::COUNTER_CLOCKWISE;
        return $this;
    }

    /**
     * Get the decimal degrees representation of this angle.
     *
     * @return float
     */
    public final function toDecimal(): float
    {
        $decimal = $this->degrees + $this->minutes / 60 + $this->seconds / 3600;
        $decimal *= $this->direction;
        return $decimal;
    }

    /**
     * Get the radiant representation of this angle.
     *
     * @return float
     */
    public function toRadiant(): float
    {
        return deg2rad($this->toDecimal());
    }

    /**
     * Check if this angle is greater than $angle.
     *
     * @param string|int|float|\MarcoConsiglio\Trigonometry\Interfaces\Angle $angle
     * @return boolean
     * @throws \InvalidArgumentException when $angle has an unexpected type.
     */
    public function isGreaterThan($angle): bool
    {
        if (is_numeric($angle)) {
            return $this->toDecimal() > $angle;
        } elseif ($angle instanceof AngleInterface) {
            return $this->toDecimal() > $angle->toDecimal();
        }
        throw new InvalidArgumentException("Expected an int, float or Angle object, but received ".gettype($angle));
    }

    /**
     * Alias of isGreaterThan method.
     *
     * @param string|int|float|\MarcoConsiglio\Trigonometry\Interfaces\Angle $angle
     * @return boolean
     * @throws \InvalidArgumentException when $angle has an unexpected type.
     */
    public function gt($angle): bool
    {
        return $this->isGreaterThan($angle);
    }

    /**
     * Check if this angle is greater than or equal to $angle.
     *
     * @param string|int|float|\MarcoConsiglio\Trigonometry\Interfaces\Angle $angle
     * @return boolean
     * @throws \InvalidArgumentException when $angle has an unexpected type.
     */
    public function isGreaterThanOrEqual($angle): bool
    {
        if (is_numeric($angle)) {
            if ($this->toDecimal() == $angle) {
                return true;
            }
            return $this->isGreaterThan($angle);
        } elseif ($angle instanceof AngleInterface) {
            if ($this->toDecimal() == $angle->toDecimal()) {
                return true;
            }
            return $this->isGreaterThan($angle);
        }
        throw new InvalidArgumentException("Expected an int, float or Angle object, but received ".gettype($angle));
    }

    /**
     * Alias of isGreaterThanOrEqual method.
     *
     * @param string|int|float|\MarcoConsiglio\Trigonometry\Interfaces\Angle $angle
     * @return boolean
     * @throws \InvalidArgumentException when $angle has an unexpected type.
     */
    public function gte($angle): bool
    {
        return $this->isGreaterThanOrEqual($angle);
    }

    /**
     * Check if this angle is less than another angle.
     *
     * @param string|int|float|\MarcoConsiglio\Trigonometry\Interfaces\Angle $angle
     * @return boolean
     * @throws \InvalidArgumentException when $angle has an unexpected type.
     */
    public function isLessThan($angle): bool
    {
        if (is_numeric($angle)) {
            return $this->toDecimal() < $angle;
        } elseif ($angle instanceof AngleInterface) {
            return $this->toDecimal() < $angle->toDecimal();
        }
        throw new InvalidArgumentException("Expected an int, float or Angle object, but received ".gettype($angle));
    }

    /**
     * Alias of isLessThan method.
     *
     * @param string|int|float|\MarcoConsiglio\Trigonometry\Interfaces\Angle $angle
     * @return boolean
     * @throws \InvalidArgumentException when $angle has an unexpected type.
     */
    public function lt($angle): bool
    {
        return $this->isLessThan($angle);
    }

    /**
     * Check if this angle is less than or equal to $angle.
     *
     * @param string|int|float|\MarcoConsiglio\Trigonometry\Interfaces\Angle $angle
     * @return boolean
     * @throws \InvalidArgumentException when $angle has an unexpected type.
     */
    public function isLessThanOrEqual($angle): bool
    {
        if (is_numeric($angle)) {
            if ($this->toDecimal() == $angle) {
                return true;
            }
            return $this->isLessThan($angle);
        } elseif ($angle instanceof AngleInterface) {
            if ($this->toDecimal() == $angle->toDecimal()) {
                return true;
            }
            return $this->isLessThan($angle);
        }
        throw new InvalidArgumentException("");
    }

    /**
     * Alias of isLessThanOrEqual method.
     *
     * @param string|int|float|\MarcoConsiglio\Trigonometry\Interfaces\Angle $angle
     * @return boolean
     * @throws \InvalidArgumentException when $angle has an unexpected type.
     */
    public function lte($angle): bool
    {
        return $this->isLessThanOrEqual($angle);
    }

    /**
     * Get a textual representation of this angle in degrees.
     *
     * @return string
     */
    public function __toString()
    {
        $sign = $this->isCounterClockwise() ? "-" : "";
        return $sign.$this->degrees."° ".$this->minutes."' ".$this->seconds."\"";
    }
}