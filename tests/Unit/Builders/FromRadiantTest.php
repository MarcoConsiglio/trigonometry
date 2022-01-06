<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\FromRadiant;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;

/**
 * @testdox The FromRadiant builder
 */
class FromRadiantTest extends BuilderTestCase
{
    /**
     * @testdox can create a positive angle from a radiant value.
     */
    public function test_can_create_positive_angle()
    {
        $this->testAngleCreation(FromRadiant::class);
    }

    /**
     * @testdox can create a negative angle from a radiant value.
     */
    public function test_can_create_negative_angle()
    {
        $this->testAngleCreation(FromRadiant::class, negative: true);
    }

    /**
     * @testdox cannot create an angle with more than +360°.
     */
    public function test_cannot_create_with_positive_excess_degrees()
    {
        $this->testAngleCreationException(FromRadiant::class, AngleOverflowException::class);
    }

    /**
     * @testdox cannot create an angle with less than -360°.
     */
    public function test_cannot_create_with_negative_excess_degrees()
    {
        $this->testAngleCreationException(FromRadiant::class, AngleOverflowException::class, negative: true);
    }   
}