<?php

namespace Avpvh\PrintWebsite\Twine\forms\strategies\display;

/**
 * Class DatepickerDisplay
 * @package Avpvh\PrintWebsite\Twine\forms\strategies\display
 */
class DatepickerDisplay extends TextInputDisplay
{
    /**
     * DatepickerDisplay constructor.
     */
    public function __construct()
    {
        parent::__construct('datepicker');
    }
}
