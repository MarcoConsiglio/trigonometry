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
     * @param mixed $data
     * @return void
     */
    public function checkOverflow($data);

    /**
     * Calc degrees.
     *
     * @param mixed $data
     * @return void
     */
    public function calcDegrees($data);

    /**
     * Calc minutes.
     *
     * @param mixed $data
     * @return void
     */
    public function calcMinutes($data);

    /**
     * Calc seconds.
     * @param mixed $data
     * @return void
     */
    public function calcSeconds($data);

    /**
     * Calc sign.
     *
     * @param mixed $data
     * @return void
     */
    public function calcSign($data);

    /**
     * Fetch the data that will bee used for an angle.
     *
     * @return array
     */
    public function fetchData(): array;
}