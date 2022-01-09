<?php
namespace MarcoConsiglio\Trigonometry\Builders;

/**
 * Represents a sum builder.
 */
abstract class SumBuilder extends AngleBuilder
{
    /**
     * Fetch data to build a Sum class.
     *
     * @return array
     */
    public function fetchData(): array
    {
        return parent::fetchData();
    }
}