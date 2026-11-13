<?php //phpcs:disable Files.SideEffects.FoundWithSymbols -- it's normal for plugin main files to be loose with that requirement.

/**
 * @package AvpvhPrintWebsite
 *
 * @wordpress-plugin
 * Plugin Name: AV-PvH Print Website
 * Plugin URI: https://github.com/grmt/avpvh-print-website
 * Description: Make printing your blog easy and impressive. For you & your visitors. One post or thousands.
 * Author: grmt, based on Print My Blog by Michael Nelson
 * Author URI: https://github.com/grmt/avpvh-print-website
 * Version: 4.0.1
 * Requires at least: 7.1
 * Requires PHP: 8.4
 * Text Domain: avpvh-print-website
 * Update URI: https://github.com/grmt/avpvh-print-website
 */


if (! defined('AVPVH_PRWB_MIN_PHP_VER_REQUIRED')) {
    define('AVPVH_PRWB_MIN_PHP_VER_REQUIRED', '8.4.0');
}
if (! defined('AVPVH_PRWB_MIN_WP_VER_REQUIRED')) {
    define('AVPVH_PRWB_MIN_WP_VER_REQUIRED', '7.1');
}
global $wp_version;
// make sure another version of PMB isn't installed
if (defined('AVPVH_PRWB_VERSION')) {
    /**
     * Function that says another version of PMB is already active.
     *
     * @return void
     */
    function avpvh_prwb_already_active()
    {
?>
        <div class="error">
            <p>
                <?php
                esc_html_e(
                    'We’re sorry, but you have another version of AV-PvH Print Website active. Only one can be active at a time. Please deactivate one.',
                    'avpvh-print-website'
                );
                ?>
            </p>
        </div>
    <?php
    }

    add_action('admin_notices', 'avpvh_prwb_already_active', 1, 0);

    // then make sure the minimum version of PHP is being used
} elseif (version_compare(PHP_VERSION, AVPVH_PRWB_MIN_PHP_VER_REQUIRED, '<')) {
    /**
     * Function that says PHP version isn't high enough to run PMB.
     *
     * @return void
     */
    function avpvh_prwb_minimum_php_version_error()
    {
    ?>
        <div class="error">
            <p>
                <?php
                printf(
                    // translators: 1: version number, 2: version number, 3: HTML line break, 4: website address
                    esc_html__(
                        'We’re sorry, but AV-PvH Print Website requires PHP version %1$s or greater in order to operate. You are currently running version %2$s.%3$sIn order to update your version of PHP, you will need to contact your current hosting provider.%3$sFor information on stable PHP versions, please go to %4$s.',
                        'avpvh-print-website'
                    ),
                    esc_html(AVPVH_PRWB_MIN_PHP_VER_REQUIRED),
                    PHP_VERSION,
                    '<br/>',
                    '<a href="http://php.net/downloads.php">http://php.net/downloads.php</a>'
                );
                ?>
            </p>
        </div>
    <?php
    }

    add_action('admin_notices', 'avpvh_prwb_minimum_php_version_error', 1, 0);
} elseif (
    version_compare(
        // first account for wp_version being pre-release
        // (like RC, beta etc) which are usually in the format like 4.7-RC3-39519
        strpos($wp_version, '-') > 0 ?
            substr($wp_version, 0, strpos($wp_version, '-')) :
            $wp_version,
        AVPVH_PRWB_MIN_WP_VER_REQUIRED,
        '<'
    )
) {
    /**
     * Function that expresses WP version isn't high enough to run PMB.
     *
     * @return void
     */
    function avpvh_prwb_minimum_wp_version_error()
    {
        global $wp_version;
    ?>
        <div class="error">
            <p>
                <?php
                printf(
                    // translators: 1: version number, 2: version number, 3: HTML line break, 4: URL
                    esc_html__(
                        'We’re sorry, but AV-PvH Print Website requires WordPress %1$s. You are using %2$s.%3$sFor information on how to update, please see %4$s.',
                        'avpvh-print-website'
                    ),
                    esc_html(AVPVH_PRWB_MIN_WP_VER_REQUIRED),
                    esc_html($wp_version),
                    '<br/>',
                    '<a href="https://wordpress.org/support/article/updating-wordpress/">https://wordpress.org/support/article/updating-wordpress/</a>'
                );
                ?>
            </p>
        </div>
<?php
    }

    add_action('admin_notices', 'avpvh_prwb_minimum_wp_version_error', 1, 0);
} else {
    // it's all good! start bootstraping PMB.
    define('AVPVH_PRWB_VERSION', '4.0.1');
    define('AVPVH_PRWB_DIR', wp_normalize_path(__DIR__) . '/');
    define('AVPVH_PRWB_MAIN_FILE', __FILE__);
    define('AVPVH_PRWB_TEMPLATES_DIR', AVPVH_PRWB_DIR . 'templates/');
    define('AVPVH_PRWB_NO_THEME_DIR', AVPVH_PRWB_TEMPLATES_DIR . 'no_theme/');
    define('AVPVH_PRWB_VENDOR_DIR', AVPVH_PRWB_DIR . 'vendor/');
    define('AVPVH_PRWB_ADMIN_CAP', 'read_private_posts');
    define('AVPVH_PRWB_BASENAME', plugin_basename(AVPVH_PRWB_MAIN_FILE));
    define('AVPVH_PRWB_DIRNAME', dirname(AVPVH_PRWB_BASENAME));
    define('AVPVH_PRWB_ADMIN_PAGE_SLUG', 'avpvh-print-website-now');
    define('AVPVH_PRWB_ADMIN_PAGE_PATH', '/admin.php?page=' . AVPVH_PRWB_ADMIN_PAGE_SLUG);
    define('AVPVH_PRWB_ADMIN_PROJECTS_PAGE_SLUG', 'avpvh-print-website-projects');
    define('AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH', '/admin.php?page=' . AVPVH_PRWB_ADMIN_PROJECTS_PAGE_SLUG);
    define('AVPVH_PRWB_ADMIN_SETTINGS_PAGE_SLUG', 'avpvh-print-website-settings');
    define('AVPVH_PRWB_ADMIN_SETTINGS_PAGE_PATH', '/admin.php?page=' . AVPVH_PRWB_ADMIN_SETTINGS_PAGE_SLUG);
    define('AVPVH_PRWB_ADMIN_DESIGNS_PAGE_SLUG', 'avpvh-print-website-designs');
    define('AVPVH_PRWB_ADMIN_DESIGNS_PAGE_PATH', '/admin.php?page=' . AVPVH_PRWB_ADMIN_DESIGNS_PAGE_SLUG);
    define('AVPVH_PRWB_DESIGNS_DIR', AVPVH_PRWB_DIR . 'designs/');
    define('AVPVH_PRWB_TWINE_MAIN_FILE', AVPVH_PRWB_MAIN_FILE);
    if (! defined('AVPVH_PRWB_DISABLE_EXTERNAL_REQUESTS')) {
        define('AVPVH_PRWB_DISABLE_EXTERNAL_REQUESTS', true);
    }

    /* WPML support */
    if (! defined('WPML_LOAD_API_SUPPORT')) {
        define('WPML_LOAD_API_SUPPORT', true);
    }

    /**
     * Adds a wp-option to indicate that PMB has been activated via the WP admin plugins page.
     * This can be used to do initial plugin installation or redirect the user to the setup page.
     */
    function avpvh_prwb_plugin_activation()
    {
        update_option('avpvh_prwb_activation', true, false);
    }

    register_activation_hook(AVPVH_PRWB_MAIN_FILE, 'avpvh_prwb_plugin_activation');
    define('AVPVH_PRWB_PRINTPAGE_SLUG', 'avpvh-print-website');
    require_once 'bootstrap.php';

    // Disable the active theme if generating a PDF.
    // This needs to be done super early
    // phpcs:disable WordPress.Security.NonceVerification.Recommended -- we're just looking, not processing or saving etc.
    if ((
            defined('DOING_AJAX') ||
            isset($_REQUEST[Avpvh\PrintWebsite\controllers\Frontend::AVPVH_PRWB_AJAX_INDICATOR])
        ) &&
        isset($_REQUEST['action'], $_REQUEST['format']) &&
        $_REQUEST['action'] === Avpvh\PrintWebsite\controllers\Frontend::AVPVH_PRWB_PROJECT_STATUS_ACTION
    ) {
        $use_theme = true;
        if (isset($_REQUEST[Avpvh\PrintWebsite\controllers\Frontend::AVPVH_PRWB_USE_THEME]) && $_REQUEST[Avpvh\PrintWebsite\controllers\Frontend::AVPVH_PRWB_USE_THEME] === '0') {
            $use_theme = false;
        }

        if ($use_theme) {
            // ha, they say to use the theme. So don't change anything
            return;
        }
        // unregister the theme once we have a moment to override it
        add_action(
            'template_redirect',
            function () {
                // We don't want the theme interfering. Kill it.
                add_filter('wp_using_themes', '__return_false');
            },
            9
        );

        // some plugins and theme still assume a theme, so give them the directory of our bundled fake theme
        add_filter(
            'template_directory',
            function () {
                return AVPVH_PRWB_NO_THEME_DIR;
            },
            100
        );
        add_filter(
            'stylesheet_directory',
            function () {
                return AVPVH_PRWB_NO_THEME_DIR;
            },
            100
        );
        add_action(
            'init',
            function () {
                remove_action('wp_head', 'wp_custom_css_cb', 11);
                remove_action('wp_head', 'wp_custom_css_cb', 101);
            }
        );
    }
}
