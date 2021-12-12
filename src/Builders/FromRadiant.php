<?php
namespace MarcoConsiglio\Trigonometry\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;
use MarcoConsiglio\Trigonometry\Interfaces\AngleBuilder;
use MarcoConsiglio\Trigonometry\Tests\TestCase;

/**
 * Can build Angle objects from degrees values.
 */
class FromRadiant implements AngleBuilder
{
    /**
     * Degrees
     *
     * @var integer
     */
    protected int $degrees;

    /**
     * Minutes
     *
     * @var integer
     */
    protected int $minutes;

    /**
     * Seconds
     *
     * @var integer
     */
    protected int $seconds;

    /**
     * Rotation direction.
     *
     * @var integer
     */
    protected int $sign;

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
     */
    public function __construct(float $radiant)
    {
        $this->checkOverflow($radiant);
        $this->builder = new FromDecimal(rad2deg($radiant));
    }

    /**
     * Check for overflow above 360°.
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
    public function fetchData()
    {
        return $this->builder->fetchData();
    }
}