<?php
namespace MarcoConsiglio\Trigonometry\Interfaces;

/**
 * The angle behavior.
 */
interface Angle
{
    public static function createFromValues(int $degrees, int $minutes, float $seconds): Angle;
    
    public static function createFromString(string $angle): Angle;

    public static function createFromDecimal(float $decimal_degrees): Angle;

    public static function createFromRadiant(float $radiant): Angle;

    public function toggleDirection(): Angle;

    public function getDegrees(): array;

    public function isClockwise(): bool;

    public function isCounterClockwise(): bool;

    public function toDecimal(): float;

    public function toRadiant(): float;

    public function isGreaterThan($angle): bool;

    public function gt($angle): bool;

    public function isGreaterThanOrEqual($angle): bool;

    public function gte($angle): bool;

    public function isLessThan($angle): bool;

    public function lt($angle): bool;

    public function isLessThanOrEqual($angle): bool;

    public function lte($angle): bool;
}