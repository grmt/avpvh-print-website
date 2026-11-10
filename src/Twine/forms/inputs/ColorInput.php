<?php

namespace Avpvh\PrintWebsite\Twine\forms\inputs;

use Avpvh\PrintWebsite\Twine\forms\strategies\display\TextInputDisplay;
use Avpvh\PrintWebsite\Twine\forms\strategies\normalization\TextNormalization;
use Avpvh\PrintWebsite\Twine\forms\strategies\validation\PlaintextValidation;

/**
 * Class ColorInput
 * @package Avpvh\PrintWebsite\Twine\forms\inputs
 */
class ColorInput extends FormInputBase
{
    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->setDisplayStrategy(new TextInputDisplay('color'));
        $this->setNormalizationStrategy(new TextNormalization());
        parent::__construct($options);
        $this->addValidationStrategy(new PlaintextValidation());
    }
}
