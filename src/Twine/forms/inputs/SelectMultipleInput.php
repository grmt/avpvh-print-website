<?php

namespace Avpvh\PrintWebsite\Twine\forms\inputs;

use Avpvh\PrintWebsite\Twine\forms\helpers\InputOption;
use Avpvh\PrintWebsite\Twine\forms\strategies\display\SelectMultipleDisplay;
use Avpvh\PrintWebsite\Twine\forms\strategies\validation\EnumValidation;
use Avpvh\PrintWebsite\Twine\forms\strategies\validation\ManyValuedValidation;

/**
 * Select_Multiple_Input
 *
 * @package         Event Espresso
 * @subpackage
 * @author              Mike Nelson
 */
class SelectMultipleInput extends FormInputWithOptionsBase
{

    /**
     * @param InputOption[] $answer_options
     * @param array $input_settings
     */
    public function __construct($answer_options, $input_settings = array())
    {
        $this->setDisplayStrategy(new SelectMultipleDisplay());
        $this->addValidationStrategy(
            new ManyValuedValidation(
                array(
                    new EnumValidation(
                        isset($input_settings['validation_error_message'])
                        ? $input_settings['validation_error_message']
                        : null
                    ),
                )
            )
        );
        $this->multiple_selections = true;
        parent::__construct($answer_options, $input_settings);
    }
}
