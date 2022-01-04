<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\FromDecimal;
use MarcoConsiglio\Trigonometry\Operations\Sum;
use MarcoConsiglio\Trigonometry\Tests\TestCase;

/**
 * @testdox A sum operation
 */
class SumTest extends TestCase
{
    /**
     * The first addend.
     *
     * @var \MarcoConsiglio\Trigonometry\Angle
     */
    protected Angle $first_angle;

    /**
     * The second addend.
     *
     * @var \MarcoConsiglio\Trigonometry\Angle
     */
    protected Angle $second_angle;

    public function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped("Angle comparison is not working.");
    }
    

    
    /**
     * @testdox can sum two positive angles.
     */
    public function test_positive_sum()
    {
        // Arrange
        $this->first_angle = $this->getRandomAngleGreaterThanFlat();
        $this->second_angle = $this->getRandomAngleGreaterThanFlat();
        
        // Act
        $sum_angle = new Sum($this->first_angle, $this->second_angle);
        
        // Assert
        $failure_message = "Can't sum two angles: {$this->first_angle->__toString()} and {$this->second_angle->__toString()}.";
        $sum = $this->first_angle->toDecimal() + $this->second_angle->toDecimal();
        if ($sum > Angle::MAX_DEGREES) {
            $sum -= Angle::MAX_DEGREES;
        } elseif ($sum < -Angle::MAX_DEGREES) {
            $sum += Angle::MAX_DEGREES;
        }
        $this->assertEquals($sum, $sum_angle->toDecimal(), $failure_message);
    }

    /**
     * @testdox can sum two negative angles.
     */
    public function test_negative_sum()
    {
        // Arrange
        $this->first_angle = $this->getRandomAngleGreaterThanFlat(negative: true);
        $this->second_angle = $this->getRandomAngleGreaterThanFlat(negative: true);

        // Act
        $sum_angle = new Sum($this->first_angle, $this->second_angle);
        $failure_message = "Can't sum two angles: {$this->first_angle->__toString()} and {$this->second_angle->__toString()}.";

        // Assert
        $sum = $this->first_angle->toDecimal() + $this->second_angle->toDecimal();
        if ($sum > Angle::MAX_DEGREES) {
            $sum -= Angle::MAX_DEGREES;
        } elseif ($sum < -Angle::MAX_DEGREES) {
            $sum += Angle::MAX_DEGREES;
        }
        $this->assertEquals($sum, $sum_angle->toDecimal(), $failure_message);
    }

    /**
     * Gets a random angle greater than 180°.
     *
     * @param bool $negative
     * @return void
     */
    protected function getRandomAngleGreaterThanFlat(bool $negative = false): Angle
    {
        $attempts = 0;
        do {
            $angle = $this->getRandomAngle($negative);
            $attempts++;
            $not_found = $angle->isLessThan(180);
        } while ($not_found && $attempts < 3);
        if ($not_found) {
            return new Angle(new FromDecimal(181));
        }
    }
}