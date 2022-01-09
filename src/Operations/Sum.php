<?php
namespace MarcoConsiglio\Trigonometry\Operations;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\SumBuilder;

/**
 * Sums two angles.
 */
class Sum extends Angle
{

    /**
     * Decimal sum of the two addend.
     *
     * @var float
     */
    private float $decimal_sum;

    /**
     * Constructs the Sum.
     *
     * @param \MarcoConsiglio\Trigonometry\Builders\SumBuilder
     * @return void
     */
    public function __construct(SumBuilder $builder)
    {
        [$this->degrees, $this->minutes, $this->seconds, $this->direction] = $builder->fetchData();
    }
}