<?php

namespace Avpvh\PrintWebsite\services;

use Exception;
use Avpvh\PrintWebsite\entities\DesignTemplate;
use Avpvh\PrintWebsite\exceptions\DesignTemplateDoesNotExist;
use Avpvh\PrintWebsite\orm\entities\Design;
use Avpvh\PrintWebsite\system\Context;

/**
 * Class DesignTemplateRegistry
 * @package Avpvh\PrintWebsite\services
 */
class DesignTemplateRegistry
{
    /**
     * @var $design_template_callbacks callable[]
     */
    protected $design_template_callbacks;

    /**
     * @var $design_templates DesignTemplate[]
     */
    protected $design_templates;

    /**
     * @param string $slug
     * @param callable $callback that returns the args to pass into DesignTemplate::__construct()
     */
    public function registerDesignTemplateCallback($slug, $callback)
    {
        $this->design_template_callbacks[$slug] = $callback;
    }

    /**
     * @param string $slug
     *
     * @return DesignTemplate
     * @throws DesignTemplateDoesNotExist
     */
    public function getDesignTemplate($slug)
    {
        if (! isset($this->design_templates[$slug])) {
            if (! isset($this->design_template_callbacks[$slug])) {
                throw new DesignTemplateDoesNotExist($slug);
            }
            $this->design_templates[$slug] = Context::instance()->useNew(
                'Avpvh\PrintWebsite\entities\DesignTemplate',
                [
                    $slug,
                    call_user_func($this->design_template_callbacks[$slug]),
                ]
            );
        }
        if (! $this->design_templates[$slug] instanceof DesignTemplate) {
            throw new DesignTemplateDoesNotExist($slug);
        }
        return $this->design_templates[$slug];
    }

    /**
     * Gets all the registered design templates
     * @return DesignTemplate[]
     */
    public function getDesignTemplates()
    {
        foreach ($this->design_template_callbacks as $slug => $callback) {
            if (! isset($this->design_templates[$slug]) || ! $this->design_templates[$slug] instanceof DesignTemplate) {
                $this->design_templates[$slug] = call_user_func($this->design_template_callbacks[$slug]);
            }
        }
        return $this->design_templates;
    }
}
