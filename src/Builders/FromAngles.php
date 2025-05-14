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
     * Constructs a SumBuilder builder with two angles.
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
        // The overflow is prevented by the algorithm in calcSum().  
    }

    /**
     * Calcs degrees.
     *
     * @return void
     */
    public function calcDegrees()
    {
       // This operation is already done in calcSum(). 
    }

    /**
     * Calc minutes.    
     *
     * @return void
     */
    public function calcMinutes()
    {
       // This operation is already done in calcSum().   
    }

    /**
     * Calc seconds.
     *
     * @return void
     */
    public function calcSeconds()
    {
       // This operation is already done in calcSum().
    }

    /**
     * Calc sign.
     *
     * @return void
     */
    public function calcSign()
    {
        // This operation is already done in calcSum().
    }

    /**
     * Sum the two addend.
     *
     * @return void
     */
    protected function calcSum()
    {
        // Transform first angle in seconds.
        $first_angle_total_seconds = 
            $this->first_angle->seconds + 
            $this->first_angle->minutes * Angle::MAX_SECONDS +
            $this->first_angle->degrees * Angle::MAX_SECONDS * Angle::MAX_MINUTES;
        // Calc the sign of the first angle.
        $first_angle_total_seconds *= $this->first_angle->direction;
        // Transform second angle in seconds.
        $second_angle_total_seconds = 
            $this->second_angle->seconds +
            $this->second_angle->minutes * Angle::MAX_SECONDS +
            $this->second_angle->degrees * Angle::MAX_SECONDS * Angle::MAX_MINUTES;
        // Calc the sign of the second angle.
        $second_angle_total_seconds *= $this->second_angle->direction;
        // Calc the algebric sum in seconds.
        $temp_sum_seconds = $first_angle_total_seconds + $second_angle_total_seconds;
        // Calc the sign of the algebric sum.
        $sign = $temp_sum_seconds >= 0 ? Angle::COUNTER_CLOCKWISE : Angle::CLOCKWISE;
        // Subtract any excess of 360°.
        $temp_sum_seconds = abs($temp_sum_seconds);
        while ($temp_sum_seconds >= Angle::MAX_DEGREES * Angle::MAX_MINUTES * Angle::MAX_SECONDS) {
            $temp_sum_seconds -= Angle::MAX_DEGREES * Angle::MAX_MINUTES * Angle::MAX_SECONDS;
        }
        // Calc the values of the result angle.
        $temp_sum_minutes = 0;
        $temp_sum_degrees = 0;
        while ($temp_sum_seconds >= Angle::MAX_SECONDS) {
            $temp_sum_seconds -= Angle::MAX_SECONDS;
            $temp_sum_minutes++;
        }
        $this->seconds = $temp_sum_seconds;
        while ($temp_sum_minutes >= Angle::MAX_MINUTES) {
            $temp_sum_minutes -= Angle::MAX_MINUTES;
            $temp_sum_degrees++;
        }
        $this->minutes = $temp_sum_minutes;
        $this->degrees = $temp_sum_degrees;
        $this->direction = $sign;
    }

    /**
     * Fetch data to build a Sum class.
     *
     * @return array
     */
    public function fetchData(): array
    {
        $this->calcSum();
        $this->checkOverflow();
        $this->calcDegrees();
        $this->calcMinutes();
        $this->calcSeconds();
        $this->calcSign();
        return parent::fetchData();
    }
}