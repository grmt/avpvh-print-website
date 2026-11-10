<?php

use Avpvh\PrintWebsite\services\DesignTemplateRegistry;
use Avpvh\PrintWebsite\system\Context;

/**
 * Checks the database has everything it needs, just like on a new install or upgrade.
 * Good to call when a plugin that adds a design is first activated or upgraded.
 */
function avpvh_prwb_check_db()
{
    $context = \Avpvh\PrintWebsite\system\Context::instance();
    /**
     * @var $activation \Avpvh\PrintWebsite\system\Activation
     */
    $activation = $context->reuse('Avpvh\PrintWebsite\system\Activation');
    $activation->install();
}

/**
 * Returns a form with all the generic sections in it.
 * @return \Avpvh\PrintWebsite\Twine\forms\base\FormSection
 */
function avpvh_prwb_generic_design_form()
{
    $context = \Avpvh\PrintWebsite\system\Context::instance();
    /**
     * @var $default_design_templates \Avpvh\PrintWebsite\domain\DefaultDesignTemplates
     */
    $default_design_templates = $context->reuse('Avpvh\PrintWebsite\domain\DefaultDesignTemplates');
    return $default_design_templates->getGenericDesignForm();
}

/**
 * @param string $file_format_slug
 * @param array $args passed into \Avpvh\PrintWebsite\entities\FileFormat::__construct
 */
function avpvh_prwb_register_file_format($file_format_slug, $args)
{
    /**
     * @var $file_format_registry \Avpvh\PrintWebsite\services\FileFormatRegistry
     */
    $file_format_registry = Context::instance()->reuse(
        'Avpvh\PrintWebsite\services\FileFormatRegistry'
    );
    $file_format_registry->registerFormat(
        $file_format_slug,
        $args
    );
}

/**
 * @param string $slug
 * @param callable $design_template_args_callback that returns the arguments to pass into the new \Avpvh\PrintWebsite\entities\DesignTemplate
 */
function avpvh_prwb_register_design_template($slug, $design_template_args_callback)
{
    /**
     * @var $design_template_registry DesignTemplateRegistry
     */
    $design_template_registry = Context::instance()->reuse(
        'Avpvh\PrintWebsite\services\DesignTemplateRegistry'
    );
    $design_template_registry->registerDesignTemplateCallback($slug, $design_template_args_callback);
}

/**
 * Use on action 'avpvh_prwb_register_designs'. The configuration callback will only be called on requests where necessary.
 * @param string $design_template_slug
 * @param string $design_slug
 * @param callable $design_args_callback returns an array to be passed into \Avpvh\PrintWebsite\services\DesignRegistry::createNewDesign()
 */
function avpvh_prwb_register_design($design_template_slug, $design_slug, $design_args_callback)
{
    /**
     * @var $design_registry \Avpvh\PrintWebsite\services\DesignRegistry
     */
    $design_registry = Context::instance()->reuse(
        'Avpvh\PrintWebsite\services\DesignRegistry'
    );
    $design_registry->registerDesignCallback(
        $design_template_slug,
        $design_slug,
        $design_args_callback
    );
}

/**
 * @param string $slug
 * @param string[] $design_templates
 * @param callback $section_template_args_callback see \Avpvh\PrintWebsite\entities\SectionTemplate::__construct()
 */
function avpvh_prwb_register_section_template($slug, $design_templates, $section_template_args_callback)
{
    /**
     * @var $section_template_registry Avpvh\PrintWebsite\services\SectionTemplateRegistry
     */
    $section_template_registry = Context::instance()->reuse(
        'Avpvh\PrintWebsite\services\SectionTemplateRegistry'
    );
    $section_template_registry->register($slug, $design_templates, $section_template_args_callback);
}