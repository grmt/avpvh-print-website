<?php

namespace Avpvh\PrintWebsite\Twine\forms\strategies\layout;

use Avpvh\PrintWebsite\Twine\forms\inputs\FormInputBase;
use Avpvh\PrintWebsite\Twine\forms\inputs\HiddenInput;
use Avpvh\PrintWebsite\Twine\forms\inputs\TextAreaInput;
use Avpvh\PrintWebsite\Twine\forms\strategies\display\AdminFileUploaderDisplay;
use Avpvh\PrintWebsite\Twine\forms\strategies\display\TextAreaDisplay;
use Avpvh\PrintWebsite\Twine\forms\strategies\display\TextInputDisplay;
use Avpvh\PrintWebsite\Twine\helpers\Html;

/**
 * Like the standard two-column form section layout, but this one adds css classes
 * specific to the WP Admin
 */
class AdminTwoColumnLayout extends TwoColumnLayout
{

    /**
     * Overriding the parent table layout to include <tbody> tags
     *
     * @param array $additional_args
     * @return string
     */
    public function layoutFormBegin($additional_args = array())
    {
        $this->form_section->setHtmlClass($this->form_section->htmlClass() . ' form-table twine-two-column-layout');
        return parent::layoutFormBegin($additional_args);
    }



    /**
     * Lays out the row for the input, including label and errors
     *
     * @param FormInputBase $input
     * @return string
     */
    public function layoutInput($input)
    {
        $html_generator = Html::instance();
        if (
            $input->getDisplayStrategy() instanceof TextAreaDisplay
            || (
                $input->getDisplayStrategy() instanceof TextInputDisplay
                && ! in_array($input->getDisplayStrategy()->getType(), ['checkbox', 'radio'], true)
                )
            || $input->getDisplayStrategy() instanceof AdminFileUploaderDisplay
        ) {
            $input->setHtmlClass($input->htmlClass() . ' large-text');
        }
        if ($input instanceof TextAreaInput) {
            $input->setRows(4);
            $input->setCols(60);
        }
        $input_html = $input->getHtmlForInput();
        // maybe add errors and help text ?
        $input_html .= $input->getHtmlForErrors() !== ''
            ? $html_generator->nl() . $input->getHtmlForErrors()
            : '';
        $input_html .= $input->getHtmlForHelp() !== ''
            ? $html_generator->nl() . $input->getHtmlForHelp()
            : '';
        // overriding parent to add wp admin specific things.
        $html = '';
        if ($input instanceof HiddenInput) {
            $html .= $html_generator->noRow($input->getHtmlForInput());
        } else {
            $html .= $html_generator->tr(
                $html_generator->th(
                    $input->getHtmlForLabel(),
                    '',
                    '',
                    '',
                    'scope="row"'
                ) . $html_generator->td($input_html)
            );
        }
        return $html;
    }
}
