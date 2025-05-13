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
     * The remainder that remains during the conversion steps from decimal to sexagesimal degrees.
     *
     * @var float
     */
    private float $reminder;

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
        $this->reminder = abs($this->decimal) - $this->degrees;
    }

    /**
     * Calc minutes.
     *
     * @return void
     */
    public function calcMinutes()
    {
        $this->minutes = intval($this->reminder * Angle::MAX_MINUTES);
        $this->reminder = abs($this->reminder - $this->minutes / Angle::MAX_MINUTES);
    }

    /**
     * Calc seconds.
     *
     * @return void
     */
    public function calcSeconds()
    {
        $this->seconds = $this->reminder * Angle::MAX_MINUTES * Angle::MAX_SECONDS;
    }

    /**
     * Calc sign.
     *
     * @return void
     */
    public function calcSign()
    {
        if ($this->decimal < 0) {
            $this->sign = Angle::CLOCKWISE;
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