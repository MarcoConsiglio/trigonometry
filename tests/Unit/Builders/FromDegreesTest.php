<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\FromDegrees;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;

/**
 * @testdox The FromDegrees builder
 */
class FromDegreesTest extends BuilderTestCase
{
    /**
     * @testdox cannot create an angle with more than +360°.
     */
    public function test_cannot_create_with_positive_excess_degrees()
    {
        $this->testAngleCreationException(FromDegrees::class, AngleOverflowException::class);
    }

    /**
     * @testdox cannot create an angle with less than -360°.
     */
    public function test_cannot_create_with_negative_excess_degrees()
    {
        $this->testAngleCreationException(FromDegrees::class, AngleOverflowException::class, negative: true);
    }
}