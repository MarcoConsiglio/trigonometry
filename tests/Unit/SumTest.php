<?php
namespace MarcoConsiglio\Trigonometry\Tests\Unit;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Builders\FromDecimal;
use MarcoConsiglio\Trigonometry\Interfaces\Angle as AngleInterface;
use MarcoConsiglio\Trigonometry\Operations\Sum;
use MarcoConsiglio\Trigonometry\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @testdox A sum operation
 */
class SumTest extends TestCase
{
    /**
     * @testdox first calculates the decimal sum 
     */
    public function test_positive_sum()
    {
        // Arrange
        $hide_method = [
            "toDecimal"
        ];
        $first_angle = $this->getMockedAngle(mocked_methods: $hide_method);
        $second_angle = $this->getMockedAngle(mocked_methods: $hide_method);
        $first_angle->expects($this->once())->method("toDecimal")->willReturn($this->getRandomAngleDecimal());
        $second_angle->expects($this->once())->method("toDecimal")->willReturn($this->getRandomAngleDecimal()); 
        /**
         * @var \MarcoConsiglio\Trigonometry\Interfaces\Angle $first_angle
         * @var \MarcoConsiglio\Trigonometry\Interfaces\Angle $second_angle
         */
        $this->setAngleProperties($first_angle, $this->getRandomAngleDegrees());
        $this->setAngleProperties($second_angle, $this->getRandomAngleDegrees());
        
        // Act
        $sum_angle = new Sum($first_angle, $second_angle);
        
        // Assert
        // $sum = $first_angle->toDecimal() + $second_angle->toDecimal();
        // if ($sum > Angle::MAX_DEGREES) {
        //     $sum -= Angle::MAX_DEGREES;
        // } elseif ($sum < -Angle::MAX_DEGREES) {
        //     $sum += Angle::MAX_DEGREES;
        // }
        // $this->assertGreaterThanOrEqual(0, $sum_angle->toDecimal(), $failure_message);
        $this->assertInstanceOf(Sum::class, $sum_angle, "The sum must be a Sum class.");
        $this->assertInstanceOf(Angle::class, $sum_angle, "The sum must extends the Angle class.");
        $this->assertInstanceOf(AngleInterface::class, $sum_angle, "The sum must implement the Angle interface.");
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
        return $angle;
    }

    /**
     * Constructs a mocked Sum.
     *
     * @param array   $mocked_methods
     * @param boolean $original_constructor
     * @param mixed   $constructor_arguments
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockedSum(
        array $mocked_methods = [], 
        bool $original_constructor = false, 
        mixed $constructor_arguments = []
    ): MockObject
    {
        $sum = $this->getMockBuilder(Angle::class)
            ->onlyMethods($mocked_methods)
            ->disableOriginalConstructor();
            if ($original_constructor) {
                $sum->enableOriginalConstructor()
                        ->setConstructorArgs(
                            is_array($constructor_arguments) ? $constructor_arguments : [$constructor_arguments]
                        );
            }
        return $sum->getMock();
    }
}