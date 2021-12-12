<?php
namespace MarcoConsiglio\Trigonometry\Interfaces;

/**
 * How an angle should be constructed.
 */
interface AngleBuilder
{
    public function checkOverflow($data);

    public function calcDegrees($data);

    public function calcMinutes($data);

    public function calcSeconds($data);

    public function calcSign($data);

    public function fetchData(): array;
}