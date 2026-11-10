<?php

namespace Avpvh\PrintWebsite\controllers;

use Avpvh\PrintWebsite\mnelson4\rest_api_detector\RestApiDetector;
use Avpvh\PrintWebsite\mnelson4\rest_api_detector\RestApiDetectorError;
use Avpvh\PrintWebsite\domain\PrintOptions;
use stdClass;
use Avpvh\PrintWebsite\Twine\controllers\BaseController;

/**
 * Class PmbPrintPage
 *
 * Sets up the print page.
 *
 * @package     AV-PvH Print Website
 * @author         Mike Nelson
 * @since         $VID:$
 *
 */
class LegacyPrintPage extends BaseController
{
    /**
     * @var URL of domain we'd like this site to proxy for, so we can print that blog instead.
     */
    protected $proxy_for;

    /**
     * Sets up hooks.
     */
    public function setHooks()
    {
        add_filter(
            'template_include',
            array($this, 'templateInclude'),
            /**
            After Elementor at priority 12,
            Enfold theme at the ridiculous priority 20,000...
            Someday, perhaps we should have a regular page dedicated to AV-PvH Print Website.
            If you're reading this code and agree, feel free to work on a pull request!
            */
            20001
        );
    }

    /**
     * @param $template
     * @return string
     * @deprecated since 2.23.0 use LegacyPrintPage::templateInclude() instead
     */
    public function templateRedirect($template){
        return $this->templateInclude($template);
    }

    /**
     * Determines if the request is for our page generator page, and if so, uses our template for it.
     * @param string $template
     * @since 1.0.0
     */
    public function templateInclude($template)
    {
        // Allow linking directly to a print page without using a form.
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
        if (isset($_GET[AVPVH_PRWB_PRINTPAGE_SLUG]) && $_GET[AVPVH_PRWB_PRINTPAGE_SLUG] === '1') {
            try {
                $site_info = new RestApiDetector($this->getFromRequest('site', ''));
            } catch (RestApiDetectorError $exception) {
                global $avpvh_prwb_wp_error;
                $avpvh_prwb_wp_error = $exception->wp_error();
                return AVPVH_PRWB_TEMPLATES_DIR . 'print_page_error.php';
            }
            // phpcs:disable WordPress.WhiteSpace.PrecisionAlignment.Found
            global $avpvh_prwb_site_name,
                   $avpvh_prwb_site_description,
                   $avpvh_prwb_site_url,
                   $avpvh_prwb_download_file_name,
                   $avpvh_prwb_show_site_title,
                   $avpvh_prwb_show_site_tagline,
                   $avpvh_prwb_show_site_url,
                   $avpvh_prwb_show_filters,
                   $avpvh_prwb_show_date_printed,
                   $avpvh_prwb_show_credit,
                   $avpvh_prwb_after_date,
                   $avpvh_prwb_before_date,
                   $avpvh_prwb_post_type,
                   $avpvh_prwb_taxonomy_filters,
                   $avpvh_prwb_format,
                   $avpvh_prwb_browser,
                   $avpvh_prwb_author;
            // phpcs:enable WordPress.WhiteSpace.PrecisionAlignment.Found
            $avpvh_prwb_site_url = str_replace(
                array(
                    'https://',
                    'http://',
                ),
                '',
                $site_info->getSite()
            );
            $avpvh_prwb_site_name = $site_info->getName();
            // If there's no name for the site, use the URL. dotEpub has an error if there is no title for the eBooks.
            if (! $avpvh_prwb_site_name) {
                $avpvh_prwb_site_name = $avpvh_prwb_site_url;
            }
            $avpvh_prwb_site_description = $site_info->getDescription();

            $avpvh_prwb_show_site_title = $this->getFromRequest('show_site_title', false);
            $avpvh_prwb_show_site_tagline = $this->getFromRequest('show_site_tagline', false);
            $avpvh_prwb_show_site_url = $this->getFromRequest('show_site_url', false);
            $avpvh_prwb_show_filters = $this->getFromRequest('show_filters', false);
            $avpvh_prwb_show_date_printed = $this->getFromRequest('show_date_printed', false);
            $avpvh_prwb_show_credit = $this->getFromRequest('show_credit', false);
            $user_id_to_filter_by = $this->getFromRequest('pmb-author', null);
            if ($user_id_to_filter_by) {
                $avpvh_prwb_author = get_userdata($user_id_to_filter_by);
            } else {
                $avpvh_prwb_author = null;
            }
            if ($site_info->isLocal()) {
                $this->proxy_for = '';
            } else {
                $this->proxy_for = $site_info->getRestApiUrl();
            }
            // enqueue our scripts and styles at the right time
            // specifically, after everybody else, so we can override them.
            add_action(
                'wp_enqueue_scripts',
                array($this, 'enqueueScripts'),
                100
            );
            $avpvh_prwb_after_date = $this->getDateString('after');
            $avpvh_prwb_before_date = $this->getDateString('before');

            // Figure out what post type was selected.
            $post_types_using_query_var = get_post_types(
                array(
                    'name' => isset($_GET['post-type']) ? sanitize_key(wp_unslash($_GET['post-type'])) : 'post',
                ),
                'object'
            );
            if (is_array($post_types_using_query_var)) {
                $post_type_info = reset($post_types_using_query_var);
                $avpvh_prwb_post_type = $post_type_info->label;
            } else {
                $avpvh_prwb_post_type = esc_html__('Unknown post type', 'avpvh-print-website');
            }

            // Figure out what taxonomies were selected (if any) and their terms.
            // Ideally we'll do this via the REST API, but I'm in a pinch so just doing it via PHP and
            // only when not using WP REST Proxy.
            if (empty($_GET['site']) && ! empty($_GET['taxonomies'])) {
                // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- we're right about to sanitize them.
                $filtering_taxonomies = wp_unslash($_GET['taxonomies']);
                foreach ($filtering_taxonomies as $taxonomy => $terms_ids) {
                    $matching_taxonomy_objects = get_taxonomies(
                        array(
                            'rest_base' => sanitize_key($taxonomy),
                        ),
                        'objects'
                    );
                    if (! is_array($matching_taxonomy_objects) || ! $matching_taxonomy_objects) {
                        continue;
                    }
                    $taxonomy_object = reset($matching_taxonomy_objects);
                    $term_objects = get_terms(
                        array(
                            'include' => implode(',', array_map('intval', $terms_ids)),
                            'hide_empty' => false,
                        )
                    );
                    $term_names = array();
                    foreach ($term_objects as $term_object) {
                        $term_names[] = $term_object->name;
                    }
                    $avpvh_prwb_taxonomy_filters[] = array(
                        'taxonomy' => $taxonomy_object,
                        'terms' => $term_names,
                    );
                }
            } else {
                $avpvh_prwb_taxonomy_filters = array();
            }
            $avpvh_prwb_format = $this->getFromRequest('avpvh_prwb_f', $this->getFromRequest('format', 'print'));
            $avpvh_prwb_download_file_name = $this->getDownloadFileName($avpvh_prwb_site_name, $avpvh_prwb_format);
            $avpvh_prwb_browser = $this->getBrowser();
            return AVPVH_PRWB_TEMPLATES_DIR . 'print_page.php';
        }
        return $template;
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
    }

    /**
     * @param string $date_filter_key
     * @return null|string
     */
    protected function getDateString($date_filter_key)
    {
        // No actions being performed so nonce unnecessary.
        // phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- there's no harm in looking.
        if (
            isset(
                $_GET['dates'],
                $_GET['dates'][$date_filter_key]
            )
        ) {
            // phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- there's no harm in looking.
            return date_i18n(get_option('date_format'), strtotime(sanitize_key(wp_unslash($_GET['dates'][$date_filter_key]))));
            // phpcs:enable WordPress.Security.NonceVerification.Recommended
        } else {
            return null;
        }
        // phpcs:enable WordPress.Security.NonceVerification.Recommended
    }

    /**
     * @since $VID:$
     * @return string
     */
    protected function getBrowser()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $agent = wp_strip_all_tags(wp_unslash($_SERVER['HTTP_USER_AGENT']));
        } else {
            $agent = '';
        }
        // From https://stackoverflow.com/questions/3047894/how-to-detect-google-chrome-as-the-user-agent-using-php
        // Note: Brave gets detected as Chrome.
        if (
            preg_match('/(Chrome|CriOS)\//i', $agent)
            //phpcs:disable Generic.Files.LineLength.TooLong
            && ! preg_match('/(Aviator|ChromePlus|coc_|Dragon|Edge|Flock|Iron|Kinza|Maxthon|MxNitro|Nichrome|OPR|Perk|Rockmelt|Seznam|Sleipnir|Spark|UBrowser|Vivaldi|WebExplorer|YaBrowser)/i', $agent)
            //phpcs:enable
        ) {
            return 'chrome';
        }
        // From https://stackoverflow.com/questions/9209649/how-to-detect-if-browser-is-firefox-with-php
        if (strlen(strstr($agent, 'Firefox')) > 0) {
            return 'firefox';
        }
        // From https://stackoverflow.com/a/186779/1493883
        if (strstr($agent, ' AppleWebKit/') && strstr($agent, ' Mobile/')) {
            return 'mobile_safari';
        }
        // From https://stackoverflow.com/q/15415883/1493883
        if (strlen(strstr($agent, 'Safari')) > 0) {
            return 'desktop_safari';
        }
        return 'unknown';
    }

    /**
     * Enqueues scripts on print page.
     */
    public function enqueueScripts()
    {
        do_action('Avpvh\PrintWebsite\controllers\PmbPrintPage->enqueueScripts');
        $format = $this->getFromRequest('format', 'print');
        wp_enqueue_script(
            'avpvh-print-website-print-page',
            AVPVH_PRWB_ASSETS_URL . 'scripts/print-page.js',
            array('jquery', 'wp-api', 'avpvh-print-website-beautifier-functions'),
            filemtime(AVPVH_PRWB_ASSETS_DIR . 'scripts/print-page.js')
        );
        wp_enqueue_style(
            'avpvh-print-website-print-page',
            AVPVH_PRWB_ASSETS_URL . 'styles/print-page.css',
            array('avpvh-print-website-print-common'),
            filemtime(AVPVH_PRWB_ASSETS_DIR . 'styles/print-page.css')
        );
        // Enqueue tiled gallery too. It's par of Jetpack so it's common, and if we're printing a WordPress.com blog
        // it's very likely to be used.
        wp_enqueue_style(
            'tiled-gallery',
            AVPVH_PRWB_ASSETS_URL . 'styles/tiled-gallery.css',
            array(),
            filemtime(AVPVH_PRWB_ASSETS_DIR . 'styles/tiled-gallery.css')
        );
        wp_enqueue_style('avpvh-print-website-plugin-compatibility');
        if ($format === 'epub') {
            $this->enqueueLocalEpubScripts();
        } elseif ($format === 'word') {
            $this->enqueueLocalWordScripts();
        }

        $post_type = $this->getFromRequest('post-type', 'post');
        if ($post_type === 'post') {
            $order_var_to_use = 'order-date';
        } else {
            $order_var_to_use = 'order-menu';
        }
        $order = $this->getFromRequest($order_var_to_use, 'asc');
        $statuses = $this->getFromRequest('statuses', ['publish', 'password', 'private', 'future']);
        $statuses = array_filter(
            $statuses,
            function ($input) {
                return in_array($input, ['draft', 'pending', 'private', 'password', 'publish', 'future', 'trash'], true);
            }
        );
        $data = [
            'header_selector' => '#pmb-in-progress-h1',
            'status_span_selector' => '.pmb-status',
            'posts_count_span_selector' => '.pmb-posts-count',
            'posts_div_selector' => '.pmb-posts-body',
            'waiting_area_selector' => '.pmb-posts-placeholder',
            'print_ready_selector' => '.pmb-print-ready',
            'loading_content_selector' => '.pmb-loading-content',
            'locale' => get_locale(),
            'image_size' => $this->getImageRelativeSize(),
            'proxy_for' => $this->proxy_for,
            'columns' => $this->getFromRequest('columns', 1),
            'post_type' => $post_type,
            'rendering_wait' => $this->getFromRequest('rendering-wait', 500),
            'include_inline_js' => (bool) $this->getFromRequest('include-inline-js', false),
            'exclude_external_content' => (bool) $this->getFromRequest('exclude-external-content', false),
            'links' => (string) $this->getFromRequest('links', 'include'),
            'filters' => (object) $this->getFromRequest('taxonomies', new stdClass()),
            'foogallery' => function_exists('foogallery_fs'),
            'is_user_logged_in' => is_user_logged_in(),
            'format' => $format,
            'statuses' => $statuses,
            'author' => $this->getFromRequest('pmb-author', null),
            'post' => $this->getFromRequest('pmb-post', null),
            'order' => $order,
            'shortcodes' => $this->getFromRequest('shortcodes', null),
        ];
        $lang = $this->getFromRequest('lang', null);
        if ($lang) {
            $data['lang'] = $lang;
        }
        // add the before and after filters, if they were provided
        $dates = $this->getFromRequest('dates', array());
        // Check if they entered the dates backwards.
        if (! empty($dates['before']) && ! empty($dates['after']) && $dates['before'] < $dates['after']) {
            $dates = [
                'after' => $dates['before'],
                'before' => $dates['after'],
            ];
        }
        if (! empty($dates['after'])) {
            $data['filters']->after = $dates['after'] . 'T00:00:00';
        }
        if (! empty($dates['before'])) {
            $data['filters']->before = $dates['before'] . 'T23:59:59';
        }
        $print_options = new PrintOptions();
        foreach ($print_options->postContentOptions() as $option_name => $option_details) {
            $data[ $option_name] = (bool)$this->getFromRequest(
                $option_name,
                false
            );
        }

        $init_error_message = esc_html__(
            'There seems to be an error initializing. Please verify you are using an up-to-date web browser.',
            'avpvh-print-website'
        );
        if (is_user_logged_in()) {
            $init_error_message .= "\n"
                . esc_html__(
                //phpcs:disable Generic.Files.LineLength.TooLong
                    'Also check the WP REST API isn’t disabled by a security plugin. Feel free to contact AV-PvH Print Website support.',
                    //phpcs:enable
                    'avpvh-print-website'
                );
        }
        wp_localize_script(
            'avpvh-print-website-print-page',
            'avpvh_prwb_print_data',
            array(
                'i18n' => array(
                    'loading_content' => esc_html__('Loading Content', 'avpvh-print-website'),
                    'loading_comments' => esc_html__('Loading Comments', 'avpvh-print-website'),
                    'organizing_posts' => esc_html__('Ordering Posts', 'avpvh-print-website'),
                    'organizing_comments' => esc_html__('Ordering Comments', 'avpvh-print-website'),
                    'rendering_posts' => esc_html__('Placing Content on Page', 'avpvh-print-website'),
                    'wrapping_up' => esc_html__('Optimizing for Print', 'avpvh-print-website'),
                    'ready' => esc_html__('Print-Page Ready', 'avpvh-print-website'),
                    'error_fetching_posts' => esc_html__(
                        'There was an error fetching posts. It was: ',
                        'avpvh-print-website'
                    ),
                    'no_response' => esc_html__('No response from WP REST API.', 'avpvh-print-website'),
                    'error' => esc_html__('Sorry, There was a Problem 😢', 'avpvh-print-website'),
                    'troubleshooting' => '',
                    'comments' => esc_html__('Comments', 'avpvh-print-website'),
                    'no_comments' => esc_html__('No Comments', 'avpvh-print-website'),
                    // Legacy code, just leave the old sub-optimal translated text.
                    // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
                    'says' => __('<span class="screen-reader-text says">says:</span>', 'avpvh-print-website'),
                    'id' => esc_html__('ID:', 'avpvh-print-website'),
                    'by' => esc_html__('By', 'avpvh-print-website'),
                    'protected' => esc_html__('Protected:', 'avpvh-print-website'),
                    'private' => esc_html__('Private:', 'avpvh-print-website'),
                    'init_error' => $init_error_message,
                    'copied' => esc_html__('Copied! Ready to paste.', 'avpvh-print-website'),
                    //phpcs:ignore Generic.Files.LineLength.TooLong
                    'copy_error' => esc_html__('There was an error copying. You can still select all the text manually and copy it.', 'avpvh-print-website'),
                ),
                'data' => $data,
            )
        );
        $this->enqueueInlineStyleBasedOnOptions();
        $this->loadThemeCompatibilityScriptsAndStylesheets();
    }

    /**
     * @param string $site_name
     * @param string $format
     * @return string
     */
    protected function getDownloadFileName($site_name, $format)
    {
        $extension_map = [
            'epub' => '.epub',
            'word' => '.doc',
        ];
        if (! isset($extension_map[$format])) {
            return '';
        }
        return sanitize_file_name($site_name . '-' . gmdate('Y-m-d') . $extension_map[$format]);
    }

    /**
     * @return void
     */
    protected function enqueueLocalEpubScripts()
    {
        wp_enqueue_script('avpvh-print-website-general');
        wp_register_script(
            'epub-gen-memory',
            AVPVH_PRWB_SCRIPTS_URL . 'libs/epub-gen-memory__premium_only.min.js',
            [],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'libs/epub-gen-memory__premium_only.min.js')
        );
        wp_register_script(
            'avpvh-print-website-filesaver',
            AVPVH_PRWB_SCRIPTS_URL . 'libs/filesaver__premium_only.min.js',
            [],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'libs/filesaver__premium_only.min.js')
        );
        wp_enqueue_script(
            'avpvh-print-website-epub',
            AVPVH_PRWB_SCRIPTS_URL . 'epub-generator.js',
            ['epub-gen-memory', 'jquery', 'avpvh-print-website-beautifier-functions', 'avpvh-print-website-filesaver', 'avpvh-print-website-general'],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'epub-generator.js')
        );
        wp_localize_script('avpvh-print-website-epub', 'avpvh_prwb_pro', $this->getLocalDocumentGeneratorData('epub'));
        wp_localize_script('avpvh-print-website-epub', 'avpvh_prwb_design_options', [
            'image_size' => $this->getImageRelativeSize(),
            'fonts_to_embed' => [],
        ]);
    }

    /**
     * @return void
     */
    protected function enqueueLocalWordScripts()
    {
        wp_enqueue_script('avpvh-print-website-general');
        wp_register_script(
            'avpvh-print-website-filesaver',
            AVPVH_PRWB_SCRIPTS_URL . 'libs/filesaver__premium_only.min.js',
            [],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'libs/filesaver__premium_only.min.js')
        );
        wp_enqueue_style(
            'avpvh-print-website-word',
            AVPVH_PRWB_STYLES_URL . 'pmb-word.css',
            [],
            filemtime(AVPVH_PRWB_STYLES_DIR . 'pmb-word.css')
        );
        wp_enqueue_script(
            'avpvh-print-website-word',
            AVPVH_PRWB_SCRIPTS_URL . 'pmb-word__premium_only.js',
            ['jquery', 'avpvh-print-website-filesaver', 'avpvh-print-website-general'],
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'pmb-word__premium_only.js')
        );
        wp_localize_script('avpvh-print-website-word', 'avpvh_prwb_pro', $this->getLocalDocumentGeneratorData('word'));
        wp_localize_script('avpvh-print-website-word', 'avpvh_prwb_design_options', [
            'image_size' => $this->getImageRelativeSize(),
        ]);
    }

    /**
     * @param string $format
     * @return array
     */
    protected function getLocalDocumentGeneratorData($format)
    {
        return [
            'title' => $this->getFromRequest('title', get_bloginfo('name')),
            'authors' => $this->getQuickPrintAuthors(),
            'cover' => '',
            'css' => $this->getQuickPrintGeneratorCss($format),
            'version' => '3',
            'translations' => [
                'many_articles' => __('Your document is very big and you might have errors downloading the file. If so, try printing fewer posts at a time.', 'avpvh-print-website'),
                'many_images' => __('Your document has lots of images and you might have errors downloading the file. If so, try reducing image size or printing fewer posts at a time.', 'avpvh-print-website'),
            ],
            'domain' => avpvh_prwb_get_domain(),
            'site_url' => site_url(),
        ];
    }

    /**
     * @param string $format
     * @return string
     */
    protected function getQuickPrintGeneratorCss($format)
    {
        $css = avpvh_prwb_get_contents(AVPVH_PRWB_ASSETS_DIR . 'styles/pmb-print-page-common.css')
            . avpvh_prwb_get_contents(AVPVH_PRWB_ASSETS_DIR . 'styles/print-page.css')
            . avpvh_prwb_get_contents(AVPVH_PRWB_ASSETS_DIR . 'styles/plugin-compatibility.css');
        if ($format === 'epub' && file_exists(AVPVH_PRWB_STYLES_DIR . 'pmb-epub.css')) {
            $css .= avpvh_prwb_get_contents(AVPVH_PRWB_STYLES_DIR . 'pmb-epub.css');
        }
        if ($format === 'word' && file_exists(AVPVH_PRWB_STYLES_DIR . 'pmb-word.css')) {
            $css .= avpvh_prwb_get_contents(AVPVH_PRWB_STYLES_DIR . 'pmb-word.css');
        }
        return $css;
    }

    /**
     * @return array|string
     */
    protected function getQuickPrintAuthors()
    {
        $user_id_to_filter_by = $this->getFromRequest('pmb-author', null);
        if ($user_id_to_filter_by) {
            $author = get_userdata($user_id_to_filter_by);
            if ($author) {
                return [$author->display_name];
            }
        }
        return '';
    }

    /**
     * @return float
     */
    protected function getImageRelativeSize()
    {
        $requested_size = sanitize_key($this->getFromRequest('image-size', 'full'));
        // The actual usable vertical space is quite a bit less than 11 inches; especially when you take
        // into account there is the post's header and metainfo.
        $page_height = 8;
        switch ($requested_size) {
            case 'large':
                return $page_height * 3 / 4;
            case 'medium':
                return $page_height / 2;
            case 'small':
                return $page_height / 4;
            case 'none':
                return 0;
            default:
                return $page_height;
        }
    }

    /**
     * Loads stylesheets that help certain themes look better on the printed page.
     * @since $VID:$
     */
    protected function loadThemeCompatibilityScriptsAndStylesheets()
    {
        $theme = wp_get_theme();
        $slug = $theme->get('TextDomain');
        $this->loadThemeCompatibilityIfItExists($slug);
        $this->loadThemeCompatibilityIfItExists($theme->template);
    }

    /**
     * @param string $slug
     */
    protected function loadThemeCompatibilityIfItExists($slug)
    {
        $theme_slug_path = 'styles/theme-compatibility/' . $slug . '.css';
        if (file_exists(AVPVH_PRWB_ASSETS_DIR . $theme_slug_path)) {
            wp_enqueue_style(
                'avpvh-print-website-print-page-theme-compatibility',
                AVPVH_PRWB_ASSETS_URL . $theme_slug_path,
                array(),
                filemtime(AVPVH_PRWB_ASSETS_DIR . $theme_slug_path)
            );
        }
        $script_slug_path = 'scripts/theme-compatibility/' . $slug . '.js';
        if (file_exists(AVPVH_PRWB_ASSETS_DIR . $script_slug_path)) {
            wp_enqueue_script(
                'avpvh-print-website-print-page-script-compatibility',
                AVPVH_PRWB_ASSETS_URL . $script_slug_path,
                array('avpvh-print-website-print-page'),
                filemtime(AVPVH_PRWB_ASSETS_DIR . $script_slug_path)
            );
        }
    }

    /**
     * Adds the styles that depend on the user's preferences.
     * @since 1.1.0
     */
    protected function enqueueInlineStyleBasedOnOptions()
    {
        $columns = intval($this->getFromRequest('columns', 1));
        $post_page_break = (bool)$this->getFromRequest('post-page-break', false);
        $font_size = sanitize_key($this->getFromRequest('font-size', 'small'));
        $css = "
        .entry-content{
            column-count: $columns;
        }
        ";
        // If it's a multi-column design, remove the margins around "avpvh_prwb_image"s. They offset the image so that even
        // if it takes up the full column width, it's now offset and so spills over onto the other column.
        // Removing the margins fixes that. And because "avpvh_prwb_image"s take up the width, they don't prevent
        // the image contained inside them from being centered anyhow. So this seems to be win-win.
        if ($columns > 1) {
            $css .= '
        	.pmb-image{
        	    margin-left:0;
        	    margin-right:0;
        	}
        	.pmb-image img{
        	    width:100%;
        	}
        	.single-featured-image-header img{
        	    width:100%;
        	}';
        }
        if ($post_page_break) {
            $css .= '.pmb-post-article:not(:first-child){page-break-before:always;}';
            $css .= '@media screen{.pmb-post-article:not(:first-child){margin-top:20vw;}}';
        }
        $font_size_map = array(
            'tiny' => '0.50em',
            'small' => '0.75em',
            // Leave out normal, let the theme decide.
            'large' => '1.25em',
        );
        if (isset($font_size_map[$font_size])) {
            $font_size_css = $font_size_map[$font_size];
            $css .= ".pmb-posts{font-size:$font_size_css;}
            h1{font-size:1.3em !important;}
            h2{font-size:1.2em !important;}
            h3{font-size:1.1em !important;}
            ul, ol, p{margin-bottom:0.5em;margin-top:0.5em;}
            blockquote{font-size:1em}";
        }

        // Dynamically handle adding the CSS to place URLs in parentheses after some hyperlinks
        // (but be careful to not put them in headers, image galleries, and other places they look terrible.)
        if ($this->getFromRequest('links', '') === 'parens') {
            $pre_selects = array(
                '.pmb-posts p',
                '.pmb-posts ul',
                '.pmb-posts ol',
            );
            $full_css_selctors = array();
            foreach ($pre_selects as $pre_select) {
                $full_css_selctors[] = $pre_select . ' a[href]:after';
            }
            $css .= implode(', ', $full_css_selctors) . '{content: " (" attr(href) ")"';
        }
        // Let's make margin smaller or bigger too, if the text was resized.
        wp_add_inline_style(
            'avpvh-print-website-print-page',
            $css
        );
    }
}
// End of file PmbPrintPage.php
// Location: Avpvh\PrintWebsite\controllers/PmbPrintPage.php
