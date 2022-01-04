<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit\Bugfixes;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Tests\TestCase;

class AngleTest extends TestCase
{
    public function test_60_seconds_overflow_to_one_minute()
    {
        // Arrange
        $expected_angle = Angle::createFromValues(14, 12, 0);
        
        // Act
        $actual_angle = Angle::createFromDecimal(14.2);

        // Assert
        $this->assertEquals($expected_angle, $actual_angle, "60 seconds must overflow to 1 minute.");
    }

    public function test_60_minutes_overflow_to_one_degree()
    {
        // Arrange
        $expected_angle = Angle::createFromValues(14, 59, 60);

        $actual_angle = Angle::createFromDecimal(15);

        // Assert
        $this->assertEquals($expected_angle, $actual_angle, "60 minutes must overflow to 1 degree.");
    }
}