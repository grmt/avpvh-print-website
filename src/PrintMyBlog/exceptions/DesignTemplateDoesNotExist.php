<?php

namespace Avpvh\PrintWebsite\exceptions;

use Exception;

/**
 * Class DesignTemplateDoesNotExist
 * @package Avpvh\PrintWebsite\exceptions
 */
class DesignTemplateDoesNotExist extends Exception
{
    /**
     * DesignTemplateDoesNotExist constructor.
     * @param string $design_slug
     */
    public function __construct($design_slug)
    {
        parent::__construct(
            sprintf(
                // translators: %s: slug
                __('The design with slug "%s" should exist but doesn\'t. Check the plugin that added that design is still active (and if it required a subscription, that the subscription is still up-to-date). For now, please choose a different design.', 'avpvh-print-website'),
                $design_slug
            )
        );
    }
}
