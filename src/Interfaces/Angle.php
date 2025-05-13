<?php
namespace MarcoConsiglio\Trigonometry\Interfaces;

/**
 * The angle behavior.
 */
interface Angle
{
    /**
     * Creates an angle from its values.
     *
     * @param integer $degrees
     * @param integer $minutes
     * @param float   $seconds
     * @return Angle
     */
    public static function createFromValues(int $degrees, int $minutes, float $seconds): Angle;
    
    /**
     * Creates an angle from its textual representation.
     *
     * @param string $angle
     * @return Angle
     */
    public static function createFromString(string $angle): Angle;

    /**
     * Creates an angle from its decimal representation.
     *
     * @param float $decimal_degrees
     * @return Angle
     */
    public static function createFromDecimal(float $decimal_degrees): Angle;

    /**
     * Creates an angle from its radiant representation.
     *
     * @param float $radiant
     * @return Angle
     */
    public static function createFromRadiant(float $radiant): Angle;

    /**
     * Reverse the direction of rotation.
     *
     * @return Angle
     */
    public function toggleDirection(): Angle;

    /**
     * Return an array containing the values
     * of degrees, minutes, seconds.
     *
     * @return array
     */
    public function getDegrees(): array;

    /**
     * Check if this angle is clockwise or positive.
     *
     * @return boolean
     */
    public function isClockwise(): bool;

    /**
     * Check if this angle is counterclockwise or negative.
     *
     * @return boolean
     */
    public function isCounterClockwise(): bool;

    /**
     * Gets the decimal degrees representation of this angle.
     *
     * @return float
     */
    public function toDecimal(): float;

    /**
     * Gets the radiant representation of this angle.
     *
     * @return float
     */
    public function toRadiant(): float;

    /**
     * Check if this angle is greater than $angle.
     *
     * @param mixed $angle
     * @return boolean
     */
    public function isGreaterThan($angle): bool;

    /**
     * Alias of isGreaterThan method.
     *
     * @param mixed $angle
     * @return boolean
     */
    public function gt($angle): bool;

    /**
     * Check if this angle is greater than or equal to $angle.
     *
     * @param mixed $angle
     * @return boolean
     */
    public function isGreaterThanOrEqual($angle): bool;

    /**
     * Alias of isGreaterThanOrEqual method.
     *
     * @param mixed $angle
     * @return boolean
     */ 
    public function gte($angle): bool;

    /**
     * Check if this angle is less than another angle.
     *
     * @param mixed $angle
     * @return boolean
     */
    public function isLessThan($angle): bool;

    /**
     * Alias of isLessThan method.
     *
     * @param mixed $angle
     */
    public function lt($angle): bool;

    /**
     * Check if this angle is less than or equal to $angle.
     *
     * @param mixed $angle
     * @return boolean
     */
    public function isLessThanOrEqual($angle): bool;

    /**
     * Alias of isLessThanOrEqual method.
     *
     * @param mixed $angle
     * @return boolean
     */
    public function lte($angle): bool;

    /**
     * Check if this angle is equal to $angle.
     *
     * @param mixed $angle
     * @return boolean
     */
    public function isEqual($angle): bool;

    /**
     * Alias of isEqual method.
     *
     * @param [type] $angle
     * @return boolean
     */
    public function eq($angle): bool;
}