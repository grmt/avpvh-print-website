<?php

namespace Avpvh\PrintWebsite\controllers;

use Avpvh\PrintWebsite\domain\PrintButtons;
use Avpvh\PrintWebsite\entities\DesignTemplate;
use Avpvh\PrintWebsite\entities\FileFormat;
use Avpvh\PrintWebsite\orm\entities\Design;
use Avpvh\PrintWebsite\orm\entities\Project;
use Avpvh\PrintWebsite\system\Context;
use Avpvh\PrintWebsite\Twine\controllers\BaseController;

/**
 * Class Shortcodes
 *
 * Adds and executes shortcodes.
 * @package Avpvh\PrintWebsite\controllers
 */
class Shortcodes extends BaseController
{

    /**
     * Adds shortcodes.
     */
    public function setHooks()
    {
        $shortcodes = [
            'print_buttons' => 'printButtons',
            'print_page_url' => 'printPageUrl',
            'project_title' => 'projectTitle',
            'toc' => 'tableOfContents',
            'title_page' => 'titlePage',
            'byline' => 'pmbByline',
            'footnote' => 'footnote',
            'web_only_text' => 'webOnlyText',
            'web_only_blocks' => 'webOnlyBlocks',
            'print_only_text' => 'printOnlyText',
            'print_only_blocks' => 'printOnlyBlocks',
            'project_setting' => 'projectSetting',
        ];
        foreach ($shortcodes as $name => $method) {
            add_shortcode('avpvh_print_website_' . $name, [$this, $method]);
            // The Print My Blog names, so content written before the rename keeps working.
            add_shortcode('pmb_' . $name, [$this, $method]);
        }
    }
	// @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

    /**
     * Adds a span whose contents will only be shown in the screen
     * @param array $atts
     * @param string $content
     * @return string
     */
    public function webOnlyText($atts, $content)
    {
        return '<span class="pmb-screen-only">' . $content . '</span>';
    }

    /**
     * Adds a div whose contents will only be shown on the screen
     * @param array $atts
     * @param string $content
     * @return string
     */
    public function webOnlyBlocks($atts, $content)
    {
        return '<div class="pmb-screen-only">' . $content . '</div>';
    }

    /**
     * Adds a span whose contents will only be shown in the screen
     * @param array $atts
     * @param string $content
     * @return string
     */
    public function printOnlyText($atts, $content)
    {
        return '<span class="pmb-print-only">' . $content . '</span>';
    }

    /**
     * Adds a div whose contents will only be shown on the screen
     * @param array $atts
     * @param string $content
     * @return string
     */
    public function printOnlyBlocks($atts, $content)
    {
        return '<div class="pmb-print-only">' . $content . '</div>';
    }
    /**
     * @param array $atts
     * @return string|string[]
     */
    public function printPageUrl($atts)
    {
        $atts = shortcode_atts(
            [
                'ID' => null,
                'format' => 'print',
                'add_protocol' => true,
            ],
            $atts
        );
        // if it was put in a URL, the most likely place to put this shortcode, the quotes became HTML entities which
        // need to be removed
        foreach ($atts as $key => $val) {
            $atts[$key] = str_replace('"', '', html_entity_decode($val, ENT_COMPAT));
        }

        $url = Context::instance()->reuse('Avpvh\PrintWebsite\domain\PrintPageUrlGenerator', [$atts['ID']])->getUrl($atts['format']);
        // remove the starting "http://" and "https://" because, if used in an anchor link, those get added automatically
        if (! $atts['add_protocol']) {
            $url = str_replace(
                ['http://', 'https://', '://'],
                '',
                $url
            );
        }
        return $url;
    }

    /**
     * @param array $atts
     * @return string
     */
    public function printButtons($atts)
    {
        $atts = shortcode_atts(
            [
                'ID' => null,
            ],
            $atts
        );
        return Context::instance()->reuse('Avpvh\PrintWebsite\domain\PrintButtons')->getHtmlForPrintButtons($atts['ID']);
    }

    /**
     * @return string
     */
    public function projectTitle()
    {
        global $avpvh_prwb_project;
        if ($avpvh_prwb_project instanceof Project) {
            return $avpvh_prwb_project->getPublishedTitle();
        }
        return '<-- pmb there is no project title because this post is not being viewed as part of a project. '
        . 'You should probably not show this post to site visitors by making it private.-->';
    }

    /**
     * @return string
     */
    public function tableOfContents()
    {
        return apply_filters(
            '\Avpvh\PrintWebsite\controllers\Shortcodes->tableOfContents',
            '<div  class="pmb-toc">
	        <ul id="pmb-toc-list" class="pmb-toc-list ">
	            <!-- Populated dynamically by JS -->
	        </ul>
	    </div>'
        );
    }

    /**
     * @return string
     * @throws \Avpvh\PrintWebsite\exceptions\TemplateDoesNotExist
     */
    public function titlePage()
    {
        global $avpvh_prwb_project, $avpvh_prwb_design, $avpvh_prwb_format;
        if (
            $avpvh_prwb_design instanceof Design
            && $avpvh_prwb_design->getDesignTemplate() instanceof DesignTemplate
            && $avpvh_prwb_design->getDesignTemplate()->supports(DesignTemplate::TEMPLATE_TITLE_PAGE)
        ) {
            $template_path = $avpvh_prwb_design->getDesignTemplate()->getTemplatePathToDivision(
                DesignTemplate::TEMPLATE_TITLE_PAGE
            );
            require $template_path;
        } else {
            return do_shortcode('<h1>[avpvh_print_website_project_title]</h1>');
        }
    }

    /**
     * @return string
     */
    public function pmbByline()
    {
        global $avpvh_prwb_project;
        if ($avpvh_prwb_project instanceof Project) {
            return $avpvh_prwb_project->getPmbMeta('byline');
        }
        return '<-- pmb there is no project byline because this post is not being viewed as part of a project. '
            . 'You should probably not show this post to site visitors by making it private.-->';
    }

    /**
     * Just wraps the content in a footnote
     * @param array $atts
     * @param string $content
     * @param string $shortcode_tag
     *
     * @return string
     */
    public function footnote($atts, $content, $shortcode_tag)
    {
        return '<span class="pmb-footnote">' . $content . '</span>';
    }

    /**
     * Shortcode for getting a setting on a project.
     * @param array $atts
     * @return mixed|string|null
     * @throws \Exception
     */
    public function projectSetting($atts)
    {
        global $avpvh_prwb_project, $avpvh_prwb_format;
        $key = isset($atts['name']) ? (string)$atts['name'] : '';

        $key = sanitize_key($key);

        if ($key) {
            if ($avpvh_prwb_project instanceof Project) {
                return $avpvh_prwb_project->getSetting($key);
            } else {
                return 'Project Setting ' . $key;
            }
        } else {
            if ($avpvh_prwb_project instanceof Project && $avpvh_prwb_format instanceof FileFormat) {
                return '[avpvh_print_website_project_setting] requires you provide the setting\'s name. Available settings are: ' . wp_json_encode($avpvh_prwb_project->getDesignFor($avpvh_prwb_format)->getSettings());
            } else {
                return 'Project Setting ' . $key;
            }
        }
    }
}
