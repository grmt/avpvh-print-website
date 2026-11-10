<?php

namespace Avpvh\PrintWebsite\services\generators;

use Exception;
use Avpvh\PrintWebsite\entities\DesignTemplate;
use Avpvh\PrintWebsite\entities\SectionTemplate;
use Avpvh\PrintWebsite\orm\entities\Design;
use Avpvh\PrintWebsite\orm\entities\Project;
use Avpvh\PrintWebsite\orm\entities\ProjectSection;
use Avpvh\PrintWebsite\system\Context;
use Avpvh\PrintWebsite\Twine\services\filesystem\File;

/**
 * Class PdfIntermediaryHtmlGenerator
 * Generates an intermediary HTML file on the server which the browser can use to generate a PDF file.
 * @package Avpvh\PrintWebsite\services\generators
 */
class PdfGenerator extends HtmlBaseGenerator
{
    /**
     * Enqueues themes and styles we'll use on this AJAX request.
     */
    public function enqueueStylesAndScripts()
    {
        wp_enqueue_style('avpvh-print-website-print-common-pdf');
        wp_enqueue_style('avpvh-print-website-pro-page');
        wp_enqueue_style('avpvh-print-website-plugin-compatibility');
        wp_enqueue_script(
            'avpvh-print-website-pdf-beautifier-functions',
            AVPVH_PRWB_SCRIPTS_URL . 'pdf-beautifier-functions.js',
            ['avpvh-print-website-beautifier-functions'],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'pdf-beautifier-functions.js')
        );
        wp_enqueue_script('avpvh-print-website-pro-page');
        wp_enqueue_style(
            'avpvh-print-website-pro-pdf',
            AVPVH_PRWB_STYLES_URL . 'pmb-pro-pdf.css',
            null,
            filemtime(AVPVH_PRWB_STYLES_DIR . 'pmb-pro-pdf.css')
        );
        wp_enqueue_script(
            'avpvh-print-website-pro-pdf',
            AVPVH_PRWB_ASSETS_URL . 'scripts/pmb-pro-pdf.js',
            array('jquery', 'avpvh-print-website-pro-page'),
            filemtime(AVPVH_PRWB_ASSETS_DIR . 'scripts/pmb-pro-pdf.js')
        );
        wp_enqueue_script('avpvh-print-website-general');
        $style_file = $this->getDesignDir() . 'assets/style.css';
        $script_file = $this->getDesignDir() . 'assets/script.js';
        if (file_exists($style_file)) {
            wp_enqueue_style(
                'avpvh-print-website-design',
                $this->getDesignAssetsUrl() . 'style.css',
                ['avpvh-print-website-print-common', 'avpvh-print-website-plugin-compatibility'],
                filemtime($style_file),
                null
            );
        }
        if (file_exists($script_file)) {
            wp_enqueue_script(
                'avpvh-print-website-design',
                $this->getDesignAssetsUrl() . 'script.js',
                ['jquery', 'avpvh-print-website-beautifier-functions'],
                filemtime($script_file)
            );
        }
        wp_localize_script(
            'avpvh-print-website-pro-page',
            'avpvh_prwb_pro',
            [
                'external_requests_enabled' => avpvh_prwb_external_requests_enabled(),
                'site_url' => site_url(),
                'domain' => avpvh_prwb_get_domain(),
                'avpvh_prwb_nonce' => wp_create_nonce('avpvh_prwb_pro_page'),
                'ajaxurl' => admin_url('admin-ajax.php'),
                'project_id' => $this->project->getWpPost()->ID,
                'format' => $this->project_generation->getFormat()->slug(),
                'external_resouce_mapping' => $this->external_resource_cache->getMapping(),
                'domains_to_not_map' => $this->external_resource_cache->domainsToNotMap(),
                'translations' => [
                    'error_generating' => __('There was an error preparing your content.', 'avpvh-print-website'),
                    'socket_error' => __('Your project could not be accessed in order to generate the file. Maybe your website is not public?', 'avpvh-print-website'),
                    'many_articles' => __('Your project is very big and you might have errors downloading the file. If so, try splitting your content into multiple projects and instead creating multiple smaller files.', 'avpvh-print-website'),
                    'many_images' => __('Your project has lots of images and you might have errors downloading the file. If so, try spltting your content into multiple projects or reducing the image quality set on your design.', 'avpvh-print-website'),
                ],
            ]
        );

        add_action(
            'wp_print_scripts',
            [$this, 'printScripts']
        );
    }

    /**
     * Prints the scripts and other stuff that's really custom (like the Prince script)
     */
    public function printScripts()
    {
        // now add the Prince script, which Prince will run
        // pass in its variables, like maximum image size
        $prince_js_vars = [
            'page_per_post' => (int)$this->design->getSetting('page_per_post'),
        ];
        $max_image_size = $this->design->getSetting('image_size');
        if (! $max_image_size) {
            $max_image_size = 2000;
        }
        $prince_js_vars['max_image_size'] = $max_image_size;
        $prince_js_vars = apply_filters(
            'Avpvh\PrintWebsite\services\generators\PdfGenerator->printScripts prince_js_vars',
            $prince_js_vars,
            $this->project_generation
        );
        echo '<prince-script>'
            . 'var avpvh_prwb = ' . wp_json_encode($prince_js_vars) . ';'
            // put that file's JS directly on the page.
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            . htmlentities(avpvh_prwb_get_contents(AVPVH_PRWB_SCRIPTS_DIR . '/prince-print-page.js'), ENT_NOQUOTES)
            . '</prince-script>';
    }

    /**
     * Adds a "base" tag to the head which tells DocRaptor how to resolve relative links. See
     * https://help.docraptor.com/en/articles/2154806-file-system-access-is-not-allowed-error-message
     */
    public function addBaseTag()
    {
        echo '<base href="' . esc_url(site_url()) . '">';
    }

    /**
     * Start making the intermediary file.
     */
    public function startGenerating()
    {
        parent::startGenerating(); // TODO: Change the autogenerated stub
        // Add the "base" tag so relative links work. But if Oxygen pagebuilder is active, we need to use a different hook.
        // (because Oxygen puts everything from wp_head in the footer)
        if (defined('CT_VERSION')) {
            add_action('oxygen_enqueue_frontend_scripts', [$this, 'addBaseTag']);
        } else {
            add_action('wp_head', [$this, 'addBaseTag']);
        }
    }




    /**
     * Writes out the PMB Pro print "window" which appears at the top of pro print pages.
     * Echoes, instead of using `$this->file_writer`, because this is a callback on an action called inside the template HTML.
     * @throws Exception
     */
    public function addPrintWindowToPage()
    {
        // this is like require or include; escaping happens in the template file.
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo avpvh_prwb_get_contents(
            AVPVH_PRWB_TEMPLATES_DIR . 'partials/pro_print_page_window.php',
            [
                'project' => $this->project,
                'generate_url' => $this->getUrlBackToGenerateStep(),
            ]
        );
    }
}
