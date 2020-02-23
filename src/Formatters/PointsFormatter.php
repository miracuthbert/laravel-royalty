<?php

namespace Miracuthbert\Royalty\Formatters;

class PointsFormatter
{
    /**
     * Points.
     *
     * @var int
     */
    protected $points;

    /**
     * PointsFormatter constructor.
     *
     * @param int $points
     * @return void
     */
    public function __construct($points)
    {
        $this->points = $points;
    }

    /**
     * Get the absolute points value.
     *
     * @return int
     */
    public function value()
    {
        return $this->points;
    }

    /**
     * Get a formatted number value.
     *
     * @return string
     */
    public function number()
    {
        return number_format($this->value());
    }

    /**
     * Get the shorthand value.
     *
     * @return int|string
     */
    public function shorthand()
    {
        $points = $this->value();

        if ($points === 0) {
            return 0;
        }

        switch ($points) {
            case $points < 1000:
                return number_format($points);
                break;
            case $points < 1000000:
                return sprintf(
                    '%sk',
                    (float)number_format($points / 1000, 1)
                );
                break;
            case $points < 1000000000:
                return sprintf(
                    '%sm',
                    (float)number_format($points / 1000000, 1)
                );
                break;
            case $points < 1000000000000:
                return sprintf(
                    '%sb',
                    (float)number_format($points / 1000000000, 1)
                );
                break;
            case $points < 1000000000000000:
                return sprintf(
                    '%st',
                    (float)number_format($points / 1000000000000, 1)
                );
                break;
            default:
                return;
        }
    }
}
