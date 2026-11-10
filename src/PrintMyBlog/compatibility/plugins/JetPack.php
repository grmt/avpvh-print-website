<?php

namespace Avpvh\PrintWebsite\compatibility\plugins;

use Avpvh\PrintWebsite\Twine\compatibility\CompatibilityBase;
use Avpvh\PrintWebsite\Twine\forms\base\FormSection;
use Avpvh\PrintWebsite\Twine\forms\inputs\SelectInput;

/**
 * Class JetPack
 * @package Avpvh\PrintWebsite\compatibility\plugins
 */
class JetPack extends CompatibilityBase
{
    /**
     * Sets hooks
     */
    public function setHooks()
    {
        add_filter(
            'Avpvh\PrintWebsite\domain\DefaultDesignTemplates->getGenericDesignForm',
            [$this, 'removeScaledResizeOption']
        );
    }

    /**
     * Removes the option to choose "scaled" image size as JetPack doesn't have it
     * when using their CDN.
     * @param FormSection $form
     */
    public function removeScaledResizeOption(FormSection $form)
    {
        $image_quality_input = $form->findSection('image_quality');
        if ($image_quality_input instanceof SelectInput) {
            $image_quality_input->removeOption('scaled');
        }
        return $form;
    }
}
