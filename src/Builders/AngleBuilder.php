<?php
namespace MarcoConsiglio\Trigonometry\Builders;

use MarcoConsiglio\Trigonometry\Angle;
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
    protected int $direction = Angle::COUNTER_CLOCKWISE;  

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
     * Calc direction.
     *
     * @return void
     */
    abstract public function calcSign();

    /**
     * Fetch data to build an Angle class.
     *
     * @return array
     */
    public function fetchData(): array
    {
        return [
            $this->degrees,
            $this->minutes,
            $this->seconds,
            $this->direction
        ];
    }
}