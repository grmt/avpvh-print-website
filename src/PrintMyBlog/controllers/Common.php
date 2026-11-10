<?php

namespace Avpvh\PrintWebsite\controllers;

use Avpvh\PrintWebsite\Twine\controllers\BaseController;

/**
 * Class PmbCommon
 *
 * Common controller logic that should run on all requests.
 *
 * @author         Mike Nelson
 * @since         $VID:$
 *
 */
class Common extends BaseController
{
    /**
     * Sets up hooks for both frontend and backend requests.
     */
    public function setHooks()
    {
        add_action(
            'wp_enqueue_scripts',
            [$this, 'enqueueScripts'],
            9
        );
        add_action(
            'admin_enqueue_scripts',
            [$this, 'enqueueScripts'],
            9
        );
        $this->registerCommonStuff();
    }

    /**
     * Just registers scripts and styles early on, so that when some other code calls "enqueue_script" or
     * "enqueue_style",
     * these will be ready.
     */
    public function registerCommonStuff()
    {
        wp_register_script(
            'avpvh-print-website-general',
            AVPVH_PRWB_SCRIPTS_URL . 'pmb-general.js',
            ['jquery', 'wp-pointer'],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'pmb-general.js')
        );
        wp_register_style(
            'avpvh-print-website-jquery-ui',
            AVPVH_PRWB_ASSETS_URL . 'styles/libs/jquery-ui/jquery-ui.min.css',
            array(),
            '1.11.4'
        );
        wp_register_style(
            'avpvh-print-website-print-common',
            AVPVH_PRWB_ASSETS_URL . 'styles/pmb-print-page-common.css',
            array(),
            filemtime(AVPVH_PRWB_ASSETS_DIR . 'styles/pmb-print-page-common.css')
        );
        wp_register_style(
            'avpvh-print-website-print-common-pdf',
            AVPVH_PRWB_ASSETS_URL . 'styles/pmb-print-page-common-pdf.css',
            array('avpvh-print-website-print-common'),
            filemtime(AVPVH_PRWB_ASSETS_DIR . 'styles/pmb-print-page-common-pdf.css')
        );
        wp_register_style(
            'avpvh-print-website-pro-page',
            AVPVH_PRWB_ASSETS_URL . 'styles/pmb-pro-print-page.css',
            array('dashicons', 'avpvh-print-website-print-common'),
            filemtime(AVPVH_PRWB_ASSETS_DIR . 'styles/pmb-pro-print-page.css')
        );
        wp_register_script(
            'avpvh-print-website-pro-page',
            AVPVH_PRWB_ASSETS_URL . 'scripts/pmb-pro-print-page.js',
            array('jquery'),
            filemtime(AVPVH_PRWB_ASSETS_DIR . 'scripts/pmb-pro-print-page.js')
        );
        wp_register_script(
            'jquery-debounce',
            AVPVH_PRWB_ASSETS_URL . 'scripts/libs/jquery.debounce-1.1.min.js',
            ['jquery'],
            '1.1'
        );
        wp_register_script(
            'avpvh-print-website-select2',
            AVPVH_PRWB_ASSETS_URL . 'scripts/libs/select2/select2.min.js',
            [],
            '4.0.6'
        );
        wp_register_style(
            'avpvh-print-website-select2',
            AVPVH_PRWB_ASSETS_URL . 'styles/libs/select2.min.css',
            [],
            '4.0.6'
        );
        wp_register_script(
            'avpvh-print-website-modal',
            AVPVH_PRWB_ASSETS_URL . 'scripts/pmb-modal.js',
            ['jquery-ui-dialog'],
            '1.0.0'
        );
        // Enqueue the CSS for compatibility with known troublemaking plugins.
        wp_register_style(
            'avpvh-print-website-plugin-compatibility',
            AVPVH_PRWB_ASSETS_URL . 'styles/plugin-compatibility.css',
            array(),
            filemtime(AVPVH_PRWB_ASSETS_DIR . 'styles/plugin-compatibility.css')
        );
        wp_register_script(
            'avpvh-print-website-qrcode',
            AVPVH_PRWB_SCRIPTS_URL . 'libs/qrcode.min.js',
            [],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'libs/qrcode.min.js')
        );
        wp_register_script(
            'avpvh-print-website-beautifier-functions',
            AVPVH_PRWB_SCRIPTS_URL . 'print-page-beautifier-functions.js',
            ['avpvh-print-website-qrcode'],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'print-page-beautifier-functions.js')
        );
        wp_localize_script(
            'avpvh-print-website-beautifier-functions',
            'avpvh_prwb',
            [
                'site_url' => site_url(),
                'site_url_attr' => esc_attr(site_url()),
                'play_button_gif' => AVPVH_PRWB_IMAGES_URL . 'play-button.gif',
                'external_requests_enabled' => avpvh_prwb_external_requests_enabled(),
            ]
        );
        wp_register_script(
            'avpvh-print-website-setup-page',
            AVPVH_PRWB_ASSETS_URL . 'scripts/setup-page.js',
            ['jquery-debounce', 'avpvh-print-website-select2', 'wp-api', 'jquery-ui-datepicker'],
            filemtime(AVPVH_PRWB_ASSETS_DIR . 'scripts/setup-page.js')
        );
        wp_register_style(
            'avpvh-print-website-setup-page',
            AVPVH_PRWB_ASSETS_URL . 'styles/setup-page.css',
            ['avpvh-print-website-common', 'avpvh-print-website-select2', 'avpvh-print-website-jquery-ui'],
            filemtime(AVPVH_PRWB_ASSETS_DIR . 'styles/setup-page.css')
        );
        wp_localize_script(
            'avpvh-print-website-setup-page',
            'avpvh_prwb_setup_page',
            [
                'translations' => [
                    'unknown_site_name' => esc_html__('Unknown site name', 'avpvh-print-website'),
                    'no_categories' => esc_html__('No categories available.', 'avpvh-print-website'),
                ],
                'data' => [
                    'site_input_selector' => '#pmb-site',
                    'spinner_selector' => '#pmb-site-checking',
                    'dynamic_categories_spinner_selector' => '#pmb-categories-spinner',
                    'site_ok_selector' => '#pmb-site-ok',
                    'site_bad_selector' => '#pmb-site-bad',
                    'site_status_selector' => '#pmb-site-status',
                    'post_type_selector' => '.pmb-post-type',
                    'dynamic_categories_selector' => '#pmb-dynamic-categories',
                    'default_rest_url' => function_exists('rest_url') ? rest_url('/wp/v2') : '',
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'author_selector' => '#pmb-author-select',
                    'nonce' => wp_create_nonce('wp_rest'),
                    'order_date_selector' => '#pmb-order-by-date',
                    'order_menu_selector' => '#pmb-order-by-menu',
                ],
            ]
        );
    }

    /**
     * Actually enqueues common scripts and styles that should be available on all page requests.
     */
    public function enqueueScripts()
    {
        wp_enqueue_style(
            'avpvh-print-website-common',
            AVPVH_PRWB_ASSETS_URL . 'styles/pmb-common.css',
            array(),
            filemtime(AVPVH_PRWB_ASSETS_DIR . 'styles/pmb-common.css')
        );
    }
}
