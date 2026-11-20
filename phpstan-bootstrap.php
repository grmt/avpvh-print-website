<?php
define('AVPVH_PRWB_TWINE_MAIN_FILE', __FILE__);
define('AVPVH_PRWB_MAIN_FILE', __FILE__);
define('AVPVH_PRWB_DIR', __DIR__ . '/');
define('AVPVH_PRWB_VENDOR_DIR', AVPVH_PRWB_DIR . 'vendor/');
define('AVPVH_PRWB_VERSION', '4.0.3');
define('AVPVH_PRWB_MIN_PHP_VER_REQUIRED', '8.4.0');
define('AVPVH_PRWB_MIN_WP_VER_REQUIRED', '7.1');
define('AVPVH_PRWB_TEMPLATES_DIR', AVPVH_PRWB_DIR . 'templates/');
define('AVPVH_PRWB_NO_THEME_DIR', AVPVH_PRWB_TEMPLATES_DIR . 'no_theme/');
define('AVPVH_PRWB_ADMIN_CAP', 'read_private_posts');
define('AVPVH_PRWB_BASENAME', 'avpvh-print-website/avpvh-print-website.php');
define('AVPVH_PRWB_DIRNAME', 'avpvh-print-website');
define('AVPVH_PRWB_ADMIN_PAGE_SLUG', 'avpvh-print-website-now');
define('AVPVH_PRWB_ADMIN_PAGE_PATH', '/admin.php?page=' . AVPVH_PRWB_ADMIN_PAGE_SLUG);
define('AVPVH_PRWB_ADMIN_PROJECTS_PAGE_SLUG', 'avpvh-print-website-projects');
define('AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH', '/admin.php?page=' . AVPVH_PRWB_ADMIN_PROJECTS_PAGE_SLUG);
define('AVPVH_PRWB_ADMIN_SETTINGS_PAGE_SLUG', 'avpvh-print-website-settings');
define('AVPVH_PRWB_ADMIN_SETTINGS_PAGE_PATH', '/admin.php?page=' . AVPVH_PRWB_ADMIN_SETTINGS_PAGE_SLUG);
define('AVPVH_PRWB_ADMIN_DESIGNS_PAGE_SLUG', 'avpvh-print-website-designs');
define('AVPVH_PRWB_ADMIN_DESIGNS_PAGE_PATH', '/admin.php?page=' . AVPVH_PRWB_ADMIN_DESIGNS_PAGE_SLUG);
define('AVPVH_PRWB_DESIGNS_DIR', AVPVH_PRWB_DIR . 'designs/');
define('AVPVH_PRWB_DISABLE_EXTERNAL_REQUESTS', true);
define('AVPVH_PRWB_PRINTPAGE_SLUG', 'avpvh-print-website');
define('AVPVH_PRWB_REST_PROXY_EXISTS', false);
define('AVPVH_PRWB_ASSETS_URL', '/wp-content/plugins/avpvh-print-website/assets/');
define('AVPVH_PRWB_IMAGES_URL', AVPVH_PRWB_ASSETS_URL . 'images/');
define('AVPVH_PRWB_SCRIPTS_URL', AVPVH_PRWB_ASSETS_URL . 'scripts/');
define('AVPVH_PRWB_STYLES_URL', AVPVH_PRWB_ASSETS_URL . 'styles/');
define('AVPVH_PRWB_ASSETS_DIR', AVPVH_PRWB_DIR . 'assets/');
define('AVPVH_PRWB_IMAGES_DIR', AVPVH_PRWB_ASSETS_DIR . 'images/');
define('AVPVH_PRWB_SCRIPTS_DIR', AVPVH_PRWB_ASSETS_DIR . 'scripts/');
define('AVPVH_PRWB_STYLES_DIR', AVPVH_PRWB_ASSETS_DIR . 'styles/');
define('AVPVH_PRWB_DESIGNS_URL', '/wp-content/plugins/avpvh-print-website/designs/');
define('AVPVH_PRWB_MNELSON4_JS_DIR', AVPVH_PRWB_DIR . 'src/mnelson4/AdminNotices/');
define('AVPVH_PRWB_MNELSON4_JS_URL', '/wp-content/plugins/avpvh-print-website/src/mnelson4/AdminNotices/');
define('ICL_TM_TRANSLATION_READY_TO_DOWNLOAD', 2);

$avpvh_prwb_loader = require AVPVH_PRWB_VENDOR_DIR . 'autoload.php';
if ($avpvh_prwb_loader instanceof \Composer\Autoload\ClassLoader) {
    $avpvh_prwb_loader->addPsr4(
        'SzepeViktor\\PHPStan\\WordPress\\',
        AVPVH_PRWB_VENDOR_DIR . 'szepeviktor/phpstan-wordpress/src/'
    );
}

// Add WPML functions for PHPstan
if (! function_exists('wpml_get_active_languages')) {
    function wpml_get_active_languages($args = '')
    {
        return [];
    }

    function wpml_get_default_language()
    {
        return '';
    }

    function wpml_object_id_filter($element_id, $element_type = 'post', $return_original_if_missing = false, $language_code = null)
    {
        return $element_id;
    }

    class SitePress
    {
        function get_default_language()
        {
            return '';
        }

        function get_language_details($code)
        {
            return [];
        }

        function get_flag_url($code)
        {
            return '';
        }
    }

    class WPML_Post_Status_Display
    {
        function __construct($active_languages = [])
        {
        }

        function get_status_html($post_id, $lang)
        {
            return ['', '', '', 0];
        }

        function get_status_data($post_id, $lang)
        {
            return ['', '', 0, '', 0];
        }
    }
}