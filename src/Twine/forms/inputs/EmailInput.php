<?php

namespace Avpvh\PrintWebsite\Twine\forms\inputs;

use Avpvh\PrintWebsite\Twine\forms\strategies\display\TextInputDisplay;
use Avpvh\PrintWebsite\Twine\forms\strategies\normalization\TextNormalization;
use Avpvh\PrintWebsite\Twine\forms\strategies\validation\EmailValidation;

/**
 * Email_Input
 *
 * @package         Event Espresso
 * @subpackage
 * @author              Mike Nelson
 */
class EmailInput extends FormInputBase
{

    /**
     * @param array $input_settings
     */
    public function __construct($input_settings = array())
    {
        $this->setDisplayStrategy(new TextInputDisplay('email'));
        $this->setNormalizationStrategy(new TextNormalization());
        $this->addValidationStrategy(
            new EmailValidation(
                isset($input_settings['validation_error_message'])
                    ? $input_settings['validation_error_message']
                    : null
            )
        );
        parent::__construct($input_settings);
        $this->setHtmlClass($this->htmlClass() . ' email');
    }
}
