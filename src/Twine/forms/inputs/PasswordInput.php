<?php

namespace Avpvh\PrintWebsite\Twine\forms\inputs;

use Avpvh\PrintWebsite\Twine\forms\strategies\display\TextInputDisplay;
use Avpvh\PrintWebsite\Twine\forms\strategies\normalization\TextNormalization;

/**
 * Password_Input
 *
 * @package         Event Espresso
 * @subpackage
 * @author              Mike Nelson
 */
class PasswordInput extends FormInputBase
{


    /**
     * @param array $input_settings
     */
    public function __construct($input_settings = array())
    {
        $this->setDisplayStrategy(new TextInputDisplay('password'));
        $this->setNormalizationStrategy(new TextNormalization());
        parent::__construct($input_settings);
        $this->setHtmlClass($this->htmlClass() . 'password');
    }
}
