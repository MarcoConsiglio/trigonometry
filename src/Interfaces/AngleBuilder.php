<?php
namespace MarcoConsiglio\Trigonometry\Interfaces;

/**
 * How an angle should be constructed.
 */
interface AngleBuilder
{
    /**
     * Check for overflow above/below +/-360°.
     *
     * @return void
     */
    public function checkOverflow();

    /**
     * Calc degrees.
     *
     * @return void
     */
    public function calcDegrees();

    /**
     * Calc minutes.
     *
     * @return void
     */
    public function calcMinutes();

    /**
     * Calc seconds.
     * @return void
     */
    public function calcSeconds();

    /**
     * Calc sign.
     *
     * @return void
     */
    public function calcSign();

    /**
     * Fetch the data that will bee used for an angle.
     *
     * @return array
     */
    public function fetchData(): array;
}