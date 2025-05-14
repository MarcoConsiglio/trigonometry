<?php
namespace MarcoConsiglio\Trigonometry\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;

/**
 *  Builds an angle starting from degrees, minutes and seconds.
 */
class FromDegrees extends AngleBuilder
{
    protected $data;
    /**
     * Constructs and AngleBuilder with degrees, minutes, seconds and direction.
     *
     * @param integer $degrees
     * @param integer $minutes
     * @param float $seconds
     * @param integer $sign
     * @return void
     */
    public function __construct(int $degrees, int $minutes, float $seconds, int $sign = Angle::COUNTER_CLOCKWISE)
    {
        $this->degrees = $degrees;
        $this->minutes = $minutes;
        $this->seconds = $seconds;
        $this->direction = $sign;
        $this->checkOverflow();
    }

    /**
     * Check for overflow above/below +/-360°.
     *
     * @return void
     * @throws \MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException when angle values exceeds.
     */
    public function checkOverflow()
    {
        $this->validate(
            $this->degrees,
            $this->minutes,
            $this->seconds,
            $this->direction
        );
    }

    /**
     * Check if values are valid.
     *
     * @param integer $degrees
     * @param integer $minutes
     * @param float   $seconds
     * @param int  $sing
     * @return boolean
     */
    protected function validate(int $degrees, int $minutes, float $seconds, int $sign)
    {
        if ($degrees > 360) {
            throw new AngleOverflowException("The angle degrees can't be greater than 360°.");
        }
        if ($minutes > 59) {
            throw new AngleOverflowException("The angle minutes can't be greater than 59'.");
        }
        if ($seconds >= 60) {
            throw new AngleOverflowException("The angle seconds can't be greater than or equal to 60\".");
        }
        if ($degrees == 0 && $minutes == 0 && $seconds == 0) {
            $this->direction = Angle::COUNTER_CLOCKWISE;
        }
        if ($sign > 0) {
            $this->direction = Angle::COUNTER_CLOCKWISE;
        } else {
            $this->direction = Angle::CLOCKWISE;
        }
    }

    /**
     * Calc degrees.
     * 
     * @return void
     * @codeCoverageIgnore
     */
    public function calcDegrees()
    {

    }

    /**
     * Calc minutes.
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function calcMinutes()
    {

    }

    /**
     * Calc seconds.
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function calcSeconds()
    {

    }

    /**
     * Calc sign.
     *
     * @param mixed $data
     * @return void
     * @codeCoverageIgnore
     */
    public function calcSign()
    {

    }
}