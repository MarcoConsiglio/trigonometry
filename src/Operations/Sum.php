<?php
namespace MarcoConsiglio\Trigonometry\Operations;

use MarcoConsiglio\Trigonometry\Angle;

/**
 * Sums two angles.
 */
class Sum extends Angle
{
    /**
     * First addend.
     *
     * @var Angle
     */
    protected Angle $first_addend;

    /**
     * Second addend.
     *
     * @var Angle
     */
    protected Angle $second_addend;

    /**
     * Decimal sum of the two addend.
     *
     * @var float
     */
    private float $decimal_sum;

    /**
     * Constructs the Sum.
     *
     * @param Angle $first
     * @param Angle $second
     * @return void
     */
    public function __construct(Angle $first, Angle $second)
    {
        $this->first_addend = $first;
        $this->second_addend = $second;
        $this->calcDecimalSum();
        $this->calcDegrees();
        $this->calcMinutes();
        $this->calcSeconds();
        $this->calcSign();
    }

    /**
     * Calcs degrees.
     *
     * @return void
     */
    protected function calcDegrees()
    {
        $this->degrees = intval(abs($this->decimal_sum));
    }

    /**
     * Calcs minutes.
     *
     * @return void
     */
    protected function calcMinutes()
    {
        $this->minutes = intval((abs($this->decimal_sum) - $this->degrees) * 60);
    }

    /**
     * Calcs seconds.
     *
     * @return void
     */
    public function calcSeconds()
    {
        $this->seconds = round(
            (abs($this->decimal_sum) - $this->degrees - $this->minutes / 60) * 3600, 
            1,
            PHP_ROUND_HALF_DOWN
        );
    }

    /**
     * Calcs sign.
     *
     * @return void
     */
    public function calcSign()
    {
        $this->direction = $this->decimal_sum >= 0 ? Angle::CLOCKWISE : Angle::COUNTER_CLOCKWISE;
    }

    /**
     * Sum the decimal representation of the two addend.
     *
     * @return float
     */
    protected final function calcDecimalSum(): float
    {
        $decimal_sum = $this->first_addend->toDecimal() + $this->second_addend->toDecimal();
        return $this->decimal_sum = $this->cutOverflow($decimal_sum);
    }

    /**
     * Cut excess 360°.
     * 
     * @param float $sum
     * @return float
     */
    protected final function cutOverflow(float $sum): float
    {
        if ($sum > Angle::MAX_DEGREES) {
            $sum -= Angle::MAX_DEGREES;
        } elseif ($sum < -Angle::MAX_DEGREES) {
            $sum += Angle::MAX_DEGREES;
        }
        return $sum;
    }


}