<?php
namespace MarcoConsiglio\Trigonometry\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;
use MarcoConsiglio\Trigonometry\Tests\TestCase;
use MarcoConsiglio\Trigonometry\Traits\WithRounding;

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
    public function __construct(int $degrees, int $minutes, float $seconds, int $sign = Angle::CLOCKWISE)
    {
        $this->degrees = $degrees;
        $this->minutes = $minutes;
        $this->seconds = $seconds;
        $this->sign = $sign;
        $this->checkOverflow();
        $this->overflow();
    }

    /**
     * Check for overflow above/below +/-360°.
     *
     * @return void
     */
    public function checkOverflow()
    {
        $seconds = $this->degrees * 60 * 60 + $this->minutes * 60 + $this->seconds;
        if ($this->exceedsRoundAngle($seconds)) {
            throw new AngleOverflowException;
        }
    }

    /**
     * Tells if the sum of total seconds is more than 360°.
     *
     * @param float $data
     * @return boolean
     */
    protected function exceedsRoundAngle(float $data): bool
    {
        if (abs($data) > Angle::MAX_SECONDS) {
            return true;
        }
        return false;
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