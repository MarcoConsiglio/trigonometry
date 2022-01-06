<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;
use MarcoConsiglio\Trigonometry\Builders\FromString;
use MarcoConsiglio\Trigonometry\Exceptions\NoMatchException;

/**
 * @testdox The FromString builder
 */
class FromStringTest extends BuilderTestCase
{
    /**
     * @testdox can create a positive angle from a string value.
     */
    public function test_can_create_positive_angle()
    {
        $this->markTestSkipped("This is an Erratic Test.");
        $this->testAngleCreation(FromString::class);
    }

    /**
     * @testdox can create a negative angle from a string value.
     */
    public function test_can_create_negative_angle()
    {
        $this->testAngleCreation(FromString::class, negative: true);
    }
    
    /**
     * @testdox cannot create an angle with more than +360°.
     */
    public function test_cannot_create_with_positive_excess_degrees()
    {
        $this->testAngleCreationException(FromString::class, NoMatchException::class);
    }

    /**
     * @testdox cannot create an angle with less than -360°.
     */
    public function test_cannot_create_with_negative_excess_degrees()
    {
        $this->testAngleCreationException(FromString::class, NoMatchException::class, negative: true);

    }
}