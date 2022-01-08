<?php
namespace MarcoConsiglio\Trigonometry\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Interfaces\Angle as AngleInterface;

/**
 * Sums two angles.
 */
class FromAngles extends SumBuilder
{
    /**
     * The first addend.
     *
     * @var \MarcoConsiglio\Trigonometry\Interfaces\Angle
     */
    protected AngleInterface $first_angle;

    /**
     * The second addend.
     *
     * @var \MarcoConsiglio\Trigonometry\Interfaces\Angle
     */
    protected AngleInterface $second_angle;

    /**
     * The sum of the two angles in decimal format.
     *
     * @var float
     */
    protected float $decimal_sum;

    /**
     * Constructs a FromAngles builder with two angles.
     *
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $first_angle
     * @param \MarcoConsiglio\Trigonometry\Interfaces\Angle $second_angle
     */
    public function __construct(AngleInterface $first_angle, AngleInterface $second_angle)
    {
        $this->first_angle = $first_angle;
        $this->second_angle = $second_angle;
    }

    /**
     * Check for overflow above/below +/-360°.
     *
     * @return void
     */
    public function checkOverflow()
    {
        if ($this->decimal_sum > Angle::MAX_DEGREES) {
            $this->decimal_sum -= Angle::MAX_DEGREES;
        } elseif ($this->decimal_sum < -Angle::MAX_DEGREES) {
            $this->decimal_sum += Angle::MAX_DEGREES;
        }    
    }

    /**
     * Calcs degrees.
     *
     * @return void
     */
    public function calcDegrees()
    {
        $this->degrees = intval(abs($this->decimal_sum));       
    }

    /**
     * Calc minutes.    
     *
     * @return void
     */
    public function calcMinutes()
    {
        $this->minutes = intval((abs($this->decimal_sum) - $this->degrees) * 60);      
    }

    /**
     * Calc seconds.
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
     * Calc sign.
     *
     * @return void
     */
    public function calcSign()
    {
        if ($this->decimal_sum < 0) {
            $this->sign = Angle::COUNTER_CLOCKWISE;
        }
    }

    /**
     * Sum the decimal representation of the two addend.
     *
     * @return void
     */
    protected function calcDecimalSum()
    {
        $this->decimal_sum = $this->first_angle->toDecimal() + $this->second_angle->toDecimal();
    }

    /**
     * Fetch data to build a Sum class.
     *
     * @return array
     */
    public function fetchData(): array
    {
        $this->calcDecimalSum();
        $this->checkOverflow();
        $this->calcDegrees();
        $this->calcMinutes();
        $this->calcSeconds();
        $this->calcSign();
        return parent::fetchData();
    }
}