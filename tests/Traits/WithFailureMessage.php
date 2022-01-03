<?php
namespace MarcoConsiglio\Trigonometry\Tests\Traits;

/**
 * Provides testing failure message helpers.
 */
trait WithFailureMessage
{
    /**
     * Gets a property type failure message.
     *
     * @param string $property
     * @return string
     */
    protected function typeFail(string $property): string
    {
        return "'$property' type not expected.";
    }

    /**
     * Gets a getter failure message.
     *
     * @param string $property
     * @return string
     */
    protected function getterFail(string $property): string
    {
        return "'$property' property is not working properly.";
    }

    /**
     * Gets a function failure message.
     *
     * @param string $name
     * @return string
     */
    protected function methodFail(string $name): string
    {
        return "'$name()' method is not working properly.";
    }
}