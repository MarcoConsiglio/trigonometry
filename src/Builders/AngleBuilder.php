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
     * @param mixed $data
     * @return void
     */
    abstract public function checkOverflow($data);

    /**
     * Calc degrees.
     *
     * @param mixed $data
     * @return void
     */
    abstract public function calcDegrees($data);

    /**
     * Calc minutes.    
     *
     * @param string $data
     * @return void
     */
    abstract public function calcMinutes($data);

    /**
     * Calc seconds.
     *
     * @param mixed $data
     * @return void
     */
    abstract public function calcSeconds($data);

    /**
     * Calc sign.
     *
     * @param mixed $data
     * @return void
     */
    abstract public function calcSign($data);

    abstract public function fetchData(): array;

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