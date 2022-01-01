<?php
namespace MarcoConsiglio\Trigonometry\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;
use MarcoConsiglio\Trigonometry\Tests\TestCase;
use MarcoConsiglio\Trigonometry\Traits\WithRounding;

/**
 * Can build Angle objects from degrees values.
 */
class FromDegrees extends AngleBuilder
{
    /**
     * Builder constructor
     *
     * @param integer $degrees
     * @param integer $minutes
     * @param float $seconds
     * @param integer $sign
     */
    public function __construct(int $degrees, int $minutes, float $seconds, int $sign = Angle::CLOCKWISE)
    {
        $this->calcDegrees($degrees);
        $this->calcMinutes($minutes);
        $this->calcSeconds($seconds);
        $this->calcSign($sign);
        $this->checkOverflow();
    }

    /**
     * Check for overflow above 360°.
     *
     * @param mixed $data
     * @return void
     */
    public function checkOverflow($data = null)
    {
        $seconds = $this->degrees * 60 * 60 + $this->minutes * 60 + $this->seconds;
        if ($this->exceedsRoundAngle($seconds)) {
            throw new AngleOverflowException;
        }
    }

    /**
     * Tells if the sum of seconds is more than 360°.
     *
     * @param float $data
     * @return boolean
     */
    protected final function exceedsRoundAngle(float $data): bool
    {
        if ($data > Angle::MAX_SECONDS) {
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
        $this->degrees = $data;
    }

    /**
     * Calc minutes.
     *
     * @param mixed $data
     * @return void
     */
    public function calcMinutes($data)
    {
        $this->minutes = $data;
    }

    /**
     * Calc seconds.
     *
     * @param mixed $data
     * @return void
     */
    public function calcSeconds($data)
    {
       $this->seconds = round($data, 1, PHP_ROUND_HALF_DOWN); 
       $this->overflow();
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
    public function fetchData(): array
    {
        return [
            $this->degrees,
            $this->minutes,
            $this->seconds,
            $this->sign
        ];
    }
}