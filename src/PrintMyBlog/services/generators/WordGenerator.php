<?php

namespace Avpvh\PrintWebsite\services\generators;

use Exception;

/**
 * Class WordGenerator
 * @package Avpvh\PrintWebsite\services\generators
 */
class WordGenerator extends HtmlBaseGenerator
{

    /**
     * Start making doc
     */
    public function startGenerating()
    {
        add_filter(
            '\Avpvh\PrintWebsite\controllers\Shortcodes->tableOfContents',
            [$this, 'wordToc']
        );
        parent::startGenerating();
    }

    /**
     * Writes out the PMB Pro print "window" which appears at the top of pro print pages.
     * Echoes, instead of using `$this->file_writer`, because this is a callback on an action called inside the template HTML.
     * @throws Exception
     */
    public function addPrintWindowToPage()
    {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo avpvh_prwb_get_contents(
            AVPVH_PRWB_TEMPLATES_DIR . 'partials/pro_print_page_word_window.php',
            [
                'project' => $this->project,
                'project_generation' => $this->project_generation,
                'generate_url' => $this->getUrlBackToGenerateStep(),
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function enqueueStylesAndScripts()
    {
        wp_enqueue_script('avpvh-print-website-pro-page');
        wp_enqueue_style('avpvh-print-website-pro-page');

        // https://github.com/koffsyrup/FileSaver.js#examples
        wp_register_script(
            'avpvh-print-website-filesaver',
            AVPVH_PRWB_SCRIPTS_URL . 'libs/filesaver__premium_only.min.js',
            [],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'libs/filesaver__premium_only.min.js')
        );

        wp_enqueue_script(
            'avpvh-print-website-word',
            AVPVH_PRWB_SCRIPTS_URL . 'pmb-word__premium_only.js',
            ['jquery', 'avpvh-print-website-filesaver', 'avpvh-print-website-general'],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'pmb-word__premium_only.js')
        );
        $css = avpvh_prwb_get_contents(
            AVPVH_PRWB_STYLES_DIR . '/pmb-word.css'
        ) . $this->design->getSetting('custom_css');

        wp_enqueue_style(
            'avpvh-print-website-word',
            AVPVH_PRWB_STYLES_URL . 'pmb-word.css',
            [],
            filemtime(AVPVH_PRWB_STYLES_DIR . 'pmb-word.css')
        );

        $style_file = $this->getDesignDir() . 'assets/style.css';
        if (file_exists($style_file)) {
            wp_enqueue_style(
                'avpvh-print-website-design',
                $this->getDesignAssetsUrl() . 'style.css',
                ['avpvh-print-website-print-common', 'avpvh-print-website-plugin-compatibility'],
                filemtime($style_file),
                null
            );
        }

        $script_file = $this->getDesignDir() . 'assets/script.js';
        if (file_exists($script_file)) {
            wp_enqueue_script(
                'avpvh-print-website-design',
                $this->getDesignAssetsUrl() . 'script.js',
                ['jquery', 'avpvh-print-website-beautifier-functions'],
                filemtime($script_file)
            );
        }

        add_filter(
            'Avpvh\PrintWebsite\services\ExternalResourceCache->domainsToNotMap()',
            [$this, 'cacheWpComToo']
        );
        wp_localize_script(
            'avpvh-print-website-word',
            'avpvh_prwb_pro',
            [
                'title' => $this->project->getPublishedTitle(),
                'authors' => $this->getAuthors(),
                'cover' => $this->project->getSetting('cover'),
                'css' => $css,
                'avpvh_prwb_nonce' => wp_create_nonce('avpvh_prwb_pro_page'),
                'external_resouce_mapping' => $this->external_resource_cache->getMapping(),
                'domains_to_not_map' => (array)$this->external_resource_cache->domainsToNotMap(),
                'ajaxurl' => admin_url('admin-ajax.php'),
                'translations' => [
                    'many_articles' => __('Your saved print is very big and you might have errors downloading the file. If so, try splitting your content into multiple saved prints and instead creating multiple smaller files.', 'avpvh-print-website'),
                    'many_images' => __('Your saved print has lots of images and you might have errors downloading the file. If so, try spltting your content into multiple saved prints or reducing the image quality set on your design.', 'avpvh-print-website'),
                ],
                'domain' => avpvh_prwb_get_domain(),
                'site_url' => site_url(),
            ]
        );
    }

    /**
     * @return false|string|string[]
     */
    protected function getAuthors()
    {
        $byline = $this->project->getSetting('byline');
        if (! $byline) {
            return '';
        }
        return array_map('trim', explode(',', str_replace(['\n'], ',', $byline)));
    }

    /**
     * Ignores original html.
     */
    public function wordToc()
    {
        return "<p class=MsoToc1> 
<!--[if supportFields]> 
<span style='mso-element:field-begin'></span> 
TOC \o \"1-3\" \u 
<span style='mso-element:field-separator'></span> 
<![endif]--> 
<span style='mso-no-proof:yes'>" . __('Table of content - In Microsoft Word, please right-click and choose "Update field".', 'avpvh-print-website') . "</span> 
<!--[if supportFields]> 
<span style='mso-element:field-end'></span> 
<![endif]--> 
</p>";
    }

    /**
     *
     * @param array $domains_to_not_map
     * @return mixed
     */
    public function cacheWpComToo($domains_to_not_map)
    {
        $key = array_search('.wp.com', $domains_to_not_map, true);
        unset($domains_to_not_map[$key]);
        return array_values($domains_to_not_map);
    }
}
