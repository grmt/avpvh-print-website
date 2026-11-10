<?php

namespace Avpvh\PrintWebsite\system;

use EventEspresso\core\domain\values\Version;
use Avpvh\PrintWebsite\compatibility\DetectAndActivate;
use Avpvh\PrintWebsite\controllers\Admin;
use Avpvh\PrintWebsite\controllers\Ajax;
use Avpvh\PrintWebsite\controllers\Common;
use Avpvh\PrintWebsite\controllers\Frontend;
use Avpvh\PrintWebsite\controllers\GutenbergBlock;
use Avpvh\PrintWebsite\controllers\LegacyPrintPage;
use Avpvh\PrintWebsite\controllers\Shortcodes;
use Avpvh\PrintWebsite\domain\DefaultDesigns;
use Avpvh\PrintWebsite\domain\DefaultDesignTemplates;
use Avpvh\PrintWebsite\domain\DefaultFileFormats;
use Avpvh\PrintWebsite\domain\DefaultSectionTemplates;
use Avpvh\PrintWebsite\Twine\admin\news\DashboardNews;
use Avpvh\PrintWebsite\system\Context;
use Avpvh\PrintWebsite\Twine\system\RequestType;
use Avpvh\PrintWebsite\Twine\system\VersionHistory;
use Avpvh\PrintWebsite\mnelson4\AdminNotices\Notices;
use Avpvh\PrintWebsite\Twine\system\Init as BaseInit;
/**
 * Class Init
 *
 * Sets up controller classes and the like.
 *
 * Managed by \Avpvh\PrintWebsite\system\Context.
 *
 * @package        AV-PvH Print Website
 * @author         Mike Nelson
 * @since          3.0.0
 *
 */
class Init extends BaseInit
{

    /**
     * @var Activation
     */
    protected $activation;

    /**
     * @var VersionHistory
     */
    protected $version_history;

    /**
     * @var RequestType
     */
    protected $request_type;

    /**
     * @var CustomPostTypes
     */
    protected $cpt;


    /**
     * Sets up PMB's general environment.
     */
    public function earlyInit()
    {
        if (function_exists('rest_proxy_loaded')) {
            define('AVPVH_PRWB_REST_PROXY_EXISTS', true);
        } else {
            define('AVPVH_PRWB_REST_PROXY_EXISTS', false);
        }
        $compatibility_mods_loader = $this->context->reuse('Avpvh\PrintWebsite\compatibility\DetectAndActivate');
        $compatibility_mods_loader->detectAndActivateGlobalCompatibilityMods();
        // There's no actions between when we know it's a REST request ('parse_request' is when "REST_REQUEST" gets
        // defined)
        // and the posts are fetched for the REST API response, except this one (and maybe another).
        add_filter('rest_pre_dispatch', [$compatibility_mods_loader, 'activateRenderingCompatibilityModes'], 11);
        parent::earlyInit();
    }

    /**
     * Includes files containing functions
     */
    protected function includes()
    {
        require_once AVPVH_PRWB_DIR . 'inc/internal_functions.php';
        require_once AVPVH_PRWB_DIR . 'inc/integration_functions.php';
        require_once AVPVH_PRWB_DIR . 'inc/template_functions.php';
        require_once AVPVH_PRWB_DIR . 'inc/design_functions.php';
    }

    /**
     * Just setting up code. Not doing anything yet.
     */
    protected function registerStuff()
    {
        load_plugin_textdomain('avpvh-print-website', false, AVPVH_PRWB_DIRNAME . '/lang');

        $uploads = $this->context->reuse('Avpvh\PrintWebsite\system\FileUploads');
        $uploads->setup();

        /**
         * @var $cpt CustomPostTypes
         */
        $cpt = $this->context->reuse('Avpvh\PrintWebsite\system\CustomPostTypes');
        $cpt->register();
        $this->setUrls();

        /**
         * @var $default_formats DefaultFileFormats
         */
        $default_formats = $this->context->reuse('Avpvh\PrintWebsite\domain\DefaultFileFormats');
        $default_formats->registerFileFormats();

        /**
         * @var $default_design_templates DefaultDesignTemplates
         */
        $default_design_templates = $this->context->reuse('Avpvh\PrintWebsite\domain\DefaultDesignTemplates');
        $default_design_templates->registerDesignTemplates();

        /**
         * @var $default_designs DefaultDesigns
         */
        $default_designs = $this->context->reuse('Avpvh\PrintWebsite\domain\DefaultDesigns');
        $default_designs->registerDefaultDesigns();

        /**
         * @var $default_section_templates DefaultSectionTemplates
         */
        $default_section_templates = $this->context->reuse('Avpvh\PrintWebsite\domain\DefaultSectionTemplates');
        $default_section_templates->registerDefaultSectionTemplates();
    }

    /**
     * Setting up stuff we assume is in the DB
     */
    protected function setupDbEnvironment()
    {
        $activation = $this->context->reuse('Avpvh\PrintWebsite\system\Activation');
        $activation->detectActivation();
    }

    /**
     * Taking action based on the current request
     */
    protected function takeActionOnIncomingRequest()
    {
        // Persistent notices need to be setup on both admin and ajax requests.
        if (is_admin()) {
            $persistent_messages = $this->context->reuse('Avpvh\PrintWebsite\services\PersistentNotices');
            $persistent_messages->register();
        }
        if (defined('DOING_AJAX') && DOING_AJAX) {
            $ajax = $this->context->reuse('Avpvh\PrintWebsite\controllers\Ajax');
            $ajax->setHooks();
        } elseif (is_admin()) {
            $admin = $this->context->reuse('Avpvh\PrintWebsite\controllers\Admin');
            $admin->setHooks();
        } else {
            $frontend = $this->context->reuse('Avpvh\PrintWebsite\controllers\Frontend');
            $frontend->setHooks();
            (new LegacyPrintPage())->setHooks();
        }
        // These are needed at least during frontend and ajax requests
        (new Shortcodes())->setHooks();


        $block_controller = new GutenbergBlock();
        $block_controller->setHooks();

        $common_controller = new Common();
        $common_controller->setHooks();
    }


    /**
     * @since $VID:$
     */
    public function setUrls()
    {
        $plugin_url = plugin_dir_url(AVPVH_PRWB_MAIN_FILE);
        define('AVPVH_PRWB_ASSETS_URL', $plugin_url . 'assets/');
        define('AVPVH_PRWB_IMAGES_URL', AVPVH_PRWB_ASSETS_URL . 'images/');
        define('AVPVH_PRWB_SCRIPTS_URL', AVPVH_PRWB_ASSETS_URL . 'scripts/');
        define('AVPVH_PRWB_STYLES_URL', AVPVH_PRWB_ASSETS_URL . 'styles/');

        define('AVPVH_PRWB_ASSETS_DIR', AVPVH_PRWB_DIR . 'assets/');
        define('AVPVH_PRWB_IMAGES_DIR', AVPVH_PRWB_ASSETS_DIR . 'images/');
        define('AVPVH_PRWB_SCRIPTS_DIR', AVPVH_PRWB_ASSETS_DIR . 'scripts/');
        define('AVPVH_PRWB_STYLES_DIR', AVPVH_PRWB_ASSETS_DIR . 'styles/');

        define('AVPVH_PRWB_DESIGNS_URL', $plugin_url . 'designs/');
        define('AVPVH_PRWB_MNELSON4_JS_DIR', AVPVH_PRWB_DIR . 'src/mnelson4/AdminNotices/');
        define('AVPVH_PRWB_MNELSON4_JS_URL', $plugin_url . 'src/mnelson4/AdminNotices/');
    }

    /**
     * @return \Avpvh\PrintWebsite\Twine\system\Context
     */
    protected function initContext()
    {
        return Context::instance();
    }
}
