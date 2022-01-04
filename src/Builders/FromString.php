<?php
namespace MarcoConsiglio\Trigonometry\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\RegExFailureException;
use MarcoConsiglio\Trigonometry\Exceptions\NoMatchException;

/**
 *  Builds an angle starting from a string value.
 */
class FromString extends AngleBuilder
{
    /**
     * The string measure of an Angle.
     *
     * @var string
     */
    protected string $measure;

    /**
     * The parsing status.
     *
     * @var mixed
     */
    protected mixed $parsing_status;

    /**
     * The regex matches.
     *
     * @var array
     */
    protected array $matches = [];

    /**
     * Builds an AngleBuilder with a string value.
     *
     * @param string $measure
     * @return void
     */
    public function __construct(string $measure)
    {    
        $this->measure = $measure;
        $this->parseDegreesString($this->measure);
        $this->checkOverflow();
    }

    /**
     * Parse an angle measure string and break down the values.
     *
     * @param string $angle
     * @return void
     * @throws \MarcoConsiglio\Trigonometry\Exceptions\NoMatchException No angle measure is found.
     * @throws \MarcoConsiglio\Trigonometry\Exceptions\RegExFailureException Error while parsing with a regular expression.
     */
    protected function parseDegreesString(string $angle)
    {
        $this->parsing_status = preg_match(Angle::ANGLE_REGEX, $angle, $this->matches);
    }

    /**
     * Check for overflow above/below +/-360°.
     *
     * @return void
     */
    public function checkOverflow()
    {
        if ($this->parsing_status === 0) {
            throw new NoMatchException($this->measure);
        }
        if ($this->parsing_status === false) {
            throw new RegExFailureException(preg_last_error_msg());
        }
    }

    /**
     * Calc degrees.
     *
     * @return void
     */
    public function calcDegrees()
    {
        $this->degrees = abs((int) $this->matches[2]);
    }

    /**
     * Calc minutes.
     *
     * @return void
     */
    public function calcMinutes()
    {
        $this->minutes = (int) $this->matches[3];
    }

    /**
     * Calc seconds.
     *
     * @return void
     */
    public function calcSeconds()
    {
        $this->seconds = $this->matches[4];
    }

    /**
     * Calc sign.
     *
     * @param mixed $data
     * @return void
     */
    public function calcSign()
    {
        $this->sign = strpos($this->matches[2], '-') === 0 ? Angle::COUNTER_CLOCKWISE : Angle::CLOCKWISE;
    }

    /**
     * Fetches the data to build an Angle.
     *
     * @return array
     */
    public function fetchData(): array
    {
        $this->calcDegrees();
        $this->calcMinutes();
        $this->calcSeconds();
        $this->calcSign();
        return parent::fetchData();
    }
}