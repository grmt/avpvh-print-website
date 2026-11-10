<?php

namespace Avpvh\PrintWebsite\Twine\forms\inputs;

use Avpvh\PrintWebsite\Twine\forms\strategies\display\DatepickerDisplay;
use Avpvh\PrintWebsite\Twine\forms\strategies\display\TextInputDisplay;
use Avpvh\PrintWebsite\Twine\forms\strategies\normalization\TextNormalization;
use Avpvh\PrintWebsite\Twine\forms\strategies\validation\PlaintextValidation;

/**
 * Datepicker_Input
 *
 * @package               Event Espresso
 * @subpackage
 * @author                Mike Nelson
 */
class DatepickerInput extends FormInputBase
{

    /**
     * @param array $input_settings
     */
    public function __construct($input_settings = array())
    {
        $this->setDisplayStrategy(new DatepickerDisplay());
        $this->setNormalizationStrategy(new TextNormalization());
        // we could do better for validation, but at least verify its plaintext
        $this->addValidationStrategy(
            new PlaintextValidation(
                isset($input_settings['validation_error_message'])
                    ? $input_settings['validation_error_message']
                    : null
            )
        );
        parent::__construct($input_settings);
        $this->setHtmlClass($this->htmlClass() . ' twine-datepicker');
    }
}
