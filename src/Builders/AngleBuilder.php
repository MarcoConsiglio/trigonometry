<?php
namespace MarcoConsiglio\Trigonometry\Builders;

use MarcoConsiglio\Trigonometry\Interfaces\AngleBuilder as AngleBuilderInterface;

/**
 * Represents an angle builder.
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

    /**
     * Check for overflow above/below +/-360°.
     *
     * @return void
     */
    abstract public function checkOverflow();

    /**
     * Calc degrees.
     *
     * @return void
     */
    abstract public function calcDegrees();

    /**
     * Calc minutes.    
     *
     * @return void
     */
    abstract public function calcMinutes();

    /**
     * Calc seconds.
     *
     * @return void
     */
    abstract public function calcSeconds();

    /**
     * Calc sign.
     *
     * @return void
     */
    abstract public function calcSign();

    /**
     * Fetch data to build.
     *
     * @return array
     */
    public function fetchData(): array
    {
        return [
            $this->degrees,
            $this->minutes,
            $this->seconds,
            $this->sign
        ];
    }

    /**
     * Correct the properties overflow.
     *
     * @return void
     */
    protected function overflow()
    {
        if (round($this->seconds, 0) >= 60) {
            $this->seconds = 0;
            $this->minutes += 1;
        }
        if (round($this->minutes, 0) >= 60) {
            $this->minutes = 0;
            $this->degrees += 1;
        }
    }
}