<?php

namespace Avpvh\PrintWebsite\Twine\forms\inputs;

use Avpvh\PrintWebsite\Twine\forms\strategies\display\TextAreaDisplay;
use Avpvh\PrintWebsite\Twine\forms\strategies\display\WysiwygDisplay;
use Avpvh\PrintWebsite\Twine\forms\strategies\normalization\TextNormalization;
use Avpvh\PrintWebsite\Twine\forms\strategies\validation\FullHtmlValidation;
use Avpvh\PrintWebsite\Twine\forms\strategies\validation\PlaintextValidation;

/**
 * Text_Area
 *
 * @package         Event Espresso
 * @subpackage
 * @author              Mike Nelson
 *
 * This input has a default validation strategy of plaintext (which can be removed after construction)
 */
class WysiwygInput extends TextAreaInput
{

    /**
     * @param array $options_array
     */
    public function __construct($options_array = array())
    {
        $this->setDisplayStrategy(new WysiwygDisplay());
        $this->setNormalizationStrategy(new TextNormalization());
        $this->addValidationStrategy(
            new FullHtmlValidation()
        );
        parent::__construct($options_array);
    }
}
