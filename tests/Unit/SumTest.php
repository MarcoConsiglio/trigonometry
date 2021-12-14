<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit;

use MarcoConsiglio\Trigonometry\Angle;
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

    // /*
    //  * This method is called before each test.
    //  */
    // protected function setUp(): void
    // {
    //     parent::setUp();
    //     $this->first_angle = $this->randomAngle();
    //     $this->second_angle = $this->randomAngle();
    // }

    
    /**
     * @testdox can sum two positive angles.
     */
    public function test_positive_sum()
    {
        // Arrange
        $this->first_angle = $this->randomAngleGreaterThanFlat(Angle::CLOCKWISE);
        $this->second_angle = $this->randomAngleGreaterThanFlat(Angle::CLOCKWISE);
        
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
        $this->first_angle = $this->randomAngleGreaterThanFlat(Angle::COUNTER_CLOCKWISE);
        $this->second_angle = $this->randomAngleGreaterThanFlat(Angle::COUNTER_CLOCKWISE);

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
     * @param integer|null $sign
     * @return void
     */
    protected function randomAngleGreaterThanFlat(int $sign = null)
    {
        do {
            $angle = $this->randomAngle($sign);
        } while (abs($angle->toDecimal()) < 180);
        return $angle;
    }
}