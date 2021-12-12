<?php
namespace MarcoConsiglio\Trigonometry\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;
use MarcoConsiglio\Trigonometry\Interfaces\AngleBuilder;

class FromDecimal implements AngleBuilder
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
     * Builder constructor.
     *
     * @param float $decimal
     */
    public function __construct(float $decimal)
    {
        $this->checkOverflow($decimal);
        $this->calcDegrees($decimal);
        $this->calcMinutes($decimal);
        $this->calcSeconds($decimal);
        $this->calcSign($decimal);
    }

    /**
     * Check for overflow above 360°.
     *
     * @param mixed $data
     * @return void
     */
    public function checkOverflow($data)
    {
        if ($this->exceedsRoundAngle($data)) {
            throw new AngleOverflowException;
        }
    }

    /**
     * Tells if decimal is more than 360.
     *
     * @param float $data
     * @return boolean
     */
    protected final function exceedsRoundAngle(float $data): bool
    {
        if (abs($data) > Angle::MAX_DEGREES) {
            return true;
        }
        return false;
    }

    /**
     * Calc degrees.
     *
     * @param mixed $data
     * @return void
     */
    public function calcDegrees($data)
    {
        $this->degrees = intval(abs($data));
    }

    /**
     * Calc minutes.
     *
     * @param mixed $data
     * @return void
     */
    public function calcMinutes($data)
    {
        $this->minutes = intval((abs($data) - $this->degrees) * 60);
    }

    /**
     * Calc seconds.
     *
     * @param mixed $data
     * @return void
     */
    public function calcSeconds($data)
    {
        $this->seconds = round(
            (abs($data) - $this->degrees - $this->minutes / 60) * 3600, 
            1,
            PHP_ROUND_HALF_DOWN
        );
    }

    /**
     * Calc sign.
     *
     * @param mixed $data
     * @return void
     */
    public function calcSign($data)
    {
        $this->sign = $data >= 0 ? Angle::CLOCKWISE : Angle::COUNTER_CLOCKWISE;
    }

    /**
     * Fetch data for building.
     *
     * @return array
     */
    public function fetchData()
    {
        return [
            $this->degrees,
            $this->minutes,
            $this->seconds,
            $this->sign
        ];
    }
}