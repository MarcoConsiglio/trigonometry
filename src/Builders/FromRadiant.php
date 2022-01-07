<?php
namespace MarcoConsiglio\Trigonometry\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;

/**
 *  Builds an angle starting from a radiant value.
 */
class FromRadiant extends FromDecimal
{
    /**
     * The radiant value used to build an Angle.
     *
     * @var float
     */
    protected float $radiant;

    /**
     * Constructs an AngleBuilder with a decimal value.
     *
     * @param float $radiant
     */
    public function __construct(float $radiant)
    {
        $this->radiant = $radiant;
        $this->checkOverflow($radiant);
        parent::__construct(rad2deg($radiant));
    }

    /**
     * Check for overflow above/below +/-360°.
     *
     * @param mixed $data
     * @return void
     */
    public function checkOverflow()
    {
        $this->validate($this->radiant);
    }

    /**
     * Tells if the radiant is more than 2 * PI.
     *
     * @param float $data
     * @return void
     * 
     */
    protected function validate(float $data)
    {
        if (abs($data) > Angle::MAX_RADIANT) {
            throw new AngleOverflowException("The angle can't be greater than 360°.");
        }
    }
}