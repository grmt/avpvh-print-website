<?php

namespace Avpvh\PrintWebsite\Twine\forms\strategies\display;

use Avpvh\PrintWebsite\Twine\forms\inputs\ButtonInput;
use Avpvh\PrintWebsite\Twine\forms\strategies\normalization\NormalizationBase;
use Avpvh\PrintWebsite\Twine\forms\strategies\normalization\NormalizationStrategyBase;

/**
 * Class ButtonDisplay
 * Description
 *
 * @package       Event Espresso
 * @author        Mike Nelson
 */
class ButtonDisplay extends DisplayBase
{

    /**
     * @return string of html to display the input
     */
    public function display()
    {
        $default_value = $this->input->getDefault();
        if ($this->input->getNormalizationStrategy() instanceof NormalizationBase) {
            $default_value = $this->input->getNormalizationStrategy()->unnormalize($default_value);
        }
        $html = $this->openingTag('button');
        $html .= $this->attributesString(
            array_merge(
                $this->standardAttributesArray(),
                array(
                    'value' => $default_value,
                )
            )
        );
        if ($this->input instanceof ButtonInput) {
            $button_content = $this->input->buttonContent();
        } else {
            $button_content = $this->input->getDefault();
        }
        $html .= '>';
        $html .= $button_content;
        $html .= $this->closingTag();
        return $html;
    }
}
