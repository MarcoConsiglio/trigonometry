<?php
namespace MarcoConsiglio\Trigonometry\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;

/**
 * Builds an angle starting from a decimal value.
 */
class FromDecimal extends AngleBuilder
{
    /**
     * The decimal value used to build an angle.
     *
     * @var float
     */
    protected float $decimal;

    /**
     * Constructs an AngleBuilder with a decimal value.
     *
     * @param float $decimal
     * @return void
     */
    public function __construct(float $decimal)
    {
        $this->decimal = $decimal;
        $this->checkOverflow();
    }

    /**
     * Check for overflow above/below +/-360°.
     *
     * @return void
     */
    public function checkOverflow()
    {
        $this->validate($this->decimal);
    }

    /**
     * Check if values are valid.
     *
     * @param float $data
     * @return void
     */
    protected function validate(float $data)
    {
        if (abs($data) > Angle::MAX_DEGREES) {
            throw new AngleOverflowException("The angle can't be greather than 360°.");
        }
    }

    /**
     * Calc degrees.
     *
     * @return void
     */
    public function calcDegrees()
    {
        $this->degrees = intval(abs($this->decimal));
    }

    /**
     * Calc minutes.
     *
     * @return void
     */
    public function calcMinutes()
    {
        $this->minutes = intval(round((abs($this->decimal) - $this->degrees) * 60, 1, PHP_ROUND_HALF_DOWN));
    }

    /**
     * Calc seconds.
     *
     * @return void
     */
    public function calcSeconds()
    {
        $this->seconds = abs(round((abs($this->decimal) - $this->degrees - $this->minutes / 60) * 3600, 1, PHP_ROUND_HALF_DOWN));
        // $this->overflow();
    }

    /**
     * Calc sign.
     *
     * @return void
     */
    public function calcSign()
    {
        if ($this->decimal < 0) {
            $this->sign = Angle::COUNTER_CLOCKWISE;
        }
    }

    /**
     * Fetches the data to build an Angle.
     *
     * @return array
     */
    public function fetchData(): array
    {
        $this->calcDegrees();
        $this->calcMinutes();
        $this->calcSeconds();
        $this->calcSign();
        return parent::fetchData();
    }
}