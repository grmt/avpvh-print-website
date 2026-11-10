<?php

namespace Avpvh\PrintWebsite\Twine\forms\inputs;

use Avpvh\PrintWebsite\Twine\forms\strategies\display\ButtonDisplay;
use Avpvh\PrintWebsite\Twine\forms\strategies\normalization\TextNormalization;
use Avpvh\PrintWebsite\Twine\forms\strategies\validation\PlaintextValidation;

/**
 * Button_Input
 * Similar to SubmitInput, but renders a button element, and its text being displayed
 * can differ from the value, and it can contain HTML.
 * @package         Event Espresso
 * @subpackage
 * @author              Mike Nelson
 *
 */
class ButtonInput extends FormInputBase
{

    /**
     * @var string of HTML to put between the button tags
     */
    protected $button_content;
    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (empty($options['button_content'])) {
            $options['button_content'] = esc_html__('Button', 'avpvh-print-website');
        }
        $this->setDisplayStrategy(new ButtonDisplay());
        $this->setNormalizationStrategy(new TextNormalization());
        $this->addValidationStrategy(new PlaintextValidation());
        parent::__construct($options);
    }



    /**
     * Sets the button content
     * @see Button_Input::$_button_content
     * @param string $new_content
     */
    public function setButtonContent($new_content)
    {
        $this->button_content = $new_content;
    }

    /**
     * Gets the button content
     * @return string
     */
    public function buttonContent()
    {
        return $this->button_content;
    }
}
