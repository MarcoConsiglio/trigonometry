<?php
namespace MarcoConsiglio\Trigonometry\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;

/**
 *  Builds an angle starting from a radiant value.
 */
class FromRadiant extends AngleBuilder
{

    /**
     * Another builder.
     *
     * @var \MarcoConsiglio\Trigonometry\Builders\FromDecimal
     */
    protected \MarcoConsiglio\Trigonometry\Builders\FromDecimal $builder;

    /**
     * Builder constructor
     *
     * @param integer $degrees
     * @param integer $minutes
     * @param integer $seconds
     * @param integer $sign
     * @return void
     */
    public function __construct(float $radiant)
    {
        $this->checkOverflow($radiant);
        $this->builder = new FromDecimal(rad2deg($radiant));
    }

    /**
     * Check for overflow above/below +/-360°.
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
     * Tells if the radiant is more than 2 * PI.
     *
     * @param float $data
     * @return boolean
     * 
     */
    protected final function exceedsRoundAngle(float $data): bool
    {
        if (abs($data) > Angle::MAX_RADIANT) {
            return true;
        }
        return false;
    }

    /**
     * Calc degrees.
     *
     * @param mixed $data
     * @return void
     * @codeCoverageIgnore
     * 
     */
    public function calcDegrees($data)
    {
        
    }

    /**
     * Calc minutes.
     *
     * @param mixed $data
     * @return void
     * @codeCoverageIgnore
     */
    public function calcMinutes($data)
    {
        
    }

    /**
     * Calc seconds.
     *
     * @param mixed $data
     * @return void
     * @codeCoverageIgnore
     */
    public function calcSeconds($data)
    {
       
    }

    /**
     * Calc sign.
     *
     * @param mixed $data
     * @return void
     * @codeCoverageIgnore
     */
    public function calcSign($data)
    {
        
    }

    /**
     * Fetch data for building.
     *
     * @return array
     */
    public function fetchData(): array
    {
        return $this->builder->fetchData();
    }
}