<?php
namespace MarcoConsiglio\Trigonometry\Builders;

use MarcoConsiglio\Trigonometry\Interfaces\AngleBuilder as AngleBuilderInterface;

/**
 * Represents the concept of an angle builder.
 */
abstract class AngleBuilder implements AngleBuilderInterface
{
    /**
     * Degrees
     *
     * @var integer
     */
    protected int $degrees;

    /**
     * Minutes
     *
     * @var integer
     */
    protected int $minutes;

    /**
     * Seconds
     *
     * @var float
     */
    protected float $seconds;

    /**
     * Rotation direction.
     *
     * @var integer
     */
    protected int $sign;  

    abstract public function checkOverflow($data);

    abstract public function calcDegrees($data);

    abstract public function calcMinutes($data);

    abstract public function calcSeconds($data);

    abstract public function calcSign($data);

    abstract public function fetchData(): array;

    /**
     * Correct the properties overflow.
     *
     * @return void
     */
    protected function overflow()
    {
        if ($this->seconds >= 60) {
            $this->seconds = 0;
            $this->minutes += 1;
        }
        if ($this->minutes >= 60) {
            $this->minutes = 0;
            $this->degrees += 1;
        }
    }
}