<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\AngleOverflowException;
use MarcoConsiglio\Trigonometry\Builders\FromString;
use MarcoConsiglio\Trigonometry\Exceptions\NoMatchException;

class FromStringTest extends BuilderTestCase
{
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