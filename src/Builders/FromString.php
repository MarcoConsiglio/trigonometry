<?php
namespace MarcoConsiglio\Trigonometry\Builders;

use MarcoConsiglio\Trigonometry\Angle;
use MarcoConsiglio\Trigonometry\Exceptions\RegExFailureException;
use MarcoConsiglio\Trigonometry\Exceptions\NoMatchException;

class FromString extends AngleBuilder
{
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
     * The measure to beign parsed.
     *
     * @var string
     */
    protected string $angle_string;

    /**
     * Builder constructor
     *
     * @param string $measure
     */
    public function __construct(string $measure)
    {    
        $this->parseDegreesString($measure);
        $this->checkOverflow();
        $this->calcSign($this->matches);
        $this->calcDegrees($this->matches);
        $this->calcMinutes($this->matches);
        $this->calcSeconds($this->matches);
    }

    /**
     * Parse an angle measure string and break down the values.
     *
     * @param string $angle
     * @return array
     * @throws \MarcoConsiglio\Trigonometry\Exceptions\NoMatchException No angle measure is found.
     * @throws \MarcoConsiglio\Trigonometry\Exceptions\RegExFailureException Error while parsing with a regular expression.
     */
    protected function parseDegreesString(string $angle)
    {
        $this->parsing_status = preg_match(Angle::ANGLE_REGEX, $angle, $this->matches);
        $this->angle_string = $angle;
    }

    /**
     * Check for overflow above/below +/-360°.
     *
     * @param mixed $data
     * @return void
     */
    public function checkOverflow($data = null)
    {
        if ($this->parsing_status === 0) {
            throw new NoMatchException($this->angle_string);
        }
        if ($this->parsing_status === false) {
            throw new RegExFailureException(preg_last_error_msg());
        }
    }

    /**
     * Calc degrees.
     *
     * @param mixed $data
     * @return void
     */
    public function calcDegrees($data)
    {
        $this->degrees = abs((int) $data[2]);
    }

    /**
     * Calc minutes.
     *
     * @param mixed $data
     * @return void
     */
    public function calcMinutes($data)
    {
        $this->minutes = (int) $data[3];
    }

    /**
     * Calc seconds.
     *
     * @param mixed $data
     * @return void
     */
    public function calcSeconds($data)
    {
        $this->seconds = $data[4];
    }

    /**
     * Calc sign.
     *
     * @param mixed $data
     * @return void
     */
    public function calcSign($data)
    {
        $this->sign = strpos($data[2], '-') === 0 ? Angle::COUNTER_CLOCKWISE : Angle::CLOCKWISE;
    }

    /**
     * Fetch data for building.
     *
     * @return array
     */
    public function fetchData(): array
    {
        return [
            $this->degrees,
            $this->minutes,
            $this->seconds,
            $this->sign
        ];
    }
}