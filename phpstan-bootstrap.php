<?php
define('AVPVH_PRWB_TWINE_MAIN_FILE', __FILE__);
define('AVPVH_PRWB_DIR', __DIR__ . '/');
define('AVPVH_PRWB_VENDOR_DIR', AVPVH_PRWB_DIR . 'vendor/');
require AVPVH_PRWB_VENDOR_DIR . 'autoload.php';

// Add WPML functions for PHPstan
if (! function_exists('wpml_get_active_languages')) {
    function wpml_get_active_languages()
    {
    }

    function wpml_get_default_language()
    {
    }

    function wpml_object_id_filter()
    {
    }

    class SitePress
    {
        function get_default_language()
        {
        }

        function get_language_details()
        {
        }
    }

    class WPML_Post_Status_Display
    {
        function __construct()
        {
        }

        function get_status_html()
        {
        }
    }
}