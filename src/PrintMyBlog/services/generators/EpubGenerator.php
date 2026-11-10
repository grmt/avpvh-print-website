<?php

namespace Avpvh\PrintWebsite\services\generators;

use Exception;

/**
 * Class EpubGenerator
 * @package Avpvh\PrintWebsite\services\generators
 */
class EpubGenerator extends HtmlBaseGenerator
{

    /**
     * Begins writing to html intermediary file.
     */
    public function startGenerating()
    {
        $this->disableEmojis();
        parent::startGenerating();
    }

    /**
     * Emojis break Amazon Kindle Previewer
     */
    protected function disableEmojis()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    }
    /**
     * Writes out the PMB Pro print "window" which appears at the top of pro print pages.
     * Echoes, instead of using `$this->file_writer`, because this is a callback on an action called inside the template HTML.
     */
    public function addPrintWindowToPage()
    {
        // The template file does the escaping.
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo avpvh_prwb_get_contents(
            AVPVH_PRWB_TEMPLATES_DIR . 'partials/pro_print_page_epub_window.php',
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
        wp_register_script(
            'epub-gen-memory',
            AVPVH_PRWB_SCRIPTS_URL . 'libs/epub-gen-memory__premium_only.min.js',
            [],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'libs/epub-gen-memory__premium_only.min.js')
        );
        wp_register_script(
            'avpvh-print-website-web-streams-ponyfill',
            AVPVH_PRWB_SCRIPTS_URL . 'libs/web-streams-ponyfill__premium_only.min.js',
            [],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'libs/web-streams-ponyfill__premium_only.min.js')
        );
        // https://github.com/jimmywarting/StreamSaver.js
        wp_register_script(
            'avpvh-print-website-streamsaver',
            AVPVH_PRWB_SCRIPTS_URL . 'libs/streamsaver__premium_only.min.js',
            ['avpvh-print-website-web-streams-ponyfill'],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'libs/streamsaver__premium_only.min.js')
        );
        // https://github.com/koffsyrup/FileSaver.js#examples
        wp_register_script(
            'avpvh-print-website-filesaver',
            AVPVH_PRWB_SCRIPTS_URL . 'libs/filesaver__premium_only.min.js',
            [],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'libs/filesaver__premium_only.min.js')
        );

        wp_enqueue_script(
            'avpvh-print-website-epub',
            AVPVH_PRWB_SCRIPTS_URL . 'epub-generator.js',
            ['epub-gen-memory', 'jquery', 'avpvh-print-website-beautifier-functions', 'avpvh-print-website-streamsaver', 'avpvh-print-website-filesaver', 'avpvh-print-website-general'],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'epub-generator.js')
        );
        $css = '/* custom styles */ ' . avpvh_prwb_get_contents(
            AVPVH_PRWB_STYLES_DIR . '/pmb-epub.css'
        ) . $this->design->getSetting('custom_css');


        $style_file = $this->getDesignDir() . 'assets/style.css';

        if (file_exists($style_file)) {
            $css .= '/** design styles */ ' . avpvh_prwb_get_contents($style_file) . '
            /** common styles */ ' . avpvh_prwb_get_contents(AVPVH_PRWB_ASSETS_DIR . 'styles/pmb-print-page-common.css');
        }
        $css = apply_filters(
            '\Avpvh\PrintWebsite\services\generators\EpubGenerator::enqueueStylesAndScripts $css',
            $css,
            $this->design,
            $this->project
        );
        wp_add_inline_style(
            'avpvh-print-website-pro-page',
            $css
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

        wp_localize_script(
            'avpvh-print-website-epub',
            'avpvh_prwb_pro',
            [
                'title' => $this->project->getPublishedTitle(),
                'authors' => $this->getAuthors(),
                'cover' => $this->project->getSetting('cover'),
                'css' => $css,
                'version' => '3',
                'avpvh_prwb_nonce' => wp_create_nonce('avpvh_prwb_pro_page'),
                'external_resouce_mapping' => $this->external_resource_cache->getMapping(),
                'domains_to_not_map' => $this->external_resource_cache->domainsToNotMap(),
                'ajaxurl' => admin_url('admin-ajax.php'),
                'translations' => [
                    'many_articles' => __('Your project is very big and you might have errors downloading the file. If so, try splitting your content into multiple projects and instead creating multiple smaller files.', 'avpvh-print-website'),
                    'many_images' => __('Your project has lots of images and you might have errors downloading the file. If so, try spltting your content into multiple projects or reducing the image quality set on your design.', 'avpvh-print-website'),
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
     * @return void
     * @throws Exception
     */
    protected function finishGenerating()
    {
        parent::finishGenerating();
        if ($this->design->getSetting('powered_by')) {
            $this->getFileWriter()->write(
                avpvh_prwb_get_contents($this->design->getDesignTemplate()->getDir() . 'templates/footer.php')
            );
        }
    }
}
