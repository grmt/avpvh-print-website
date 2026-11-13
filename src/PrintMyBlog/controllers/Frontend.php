<?php

namespace Avpvh\PrintWebsite\controllers;

use Exception;
use Avpvh\PrintWebsite\domain\FrontendPrintSettings;
use Avpvh\PrintWebsite\domain\PrintNowSettings;
use Avpvh\PrintWebsite\entities\ProjectProgress;
use Avpvh\PrintWebsite\orm\managers\ProjectManager;
use Avpvh\PrintWebsite\services\FileFormatRegistry;
use Avpvh\PrintWebsite\system\Context;
use Avpvh\PrintWebsite\Twine\controllers\BaseController;
use Avpvh\PrintWebsite\Twine\helpers\Array2;
use WP_Post;
use Avpvh\PrintWebsite\domain\DefaultFileFormats;

/**
 * Class PmbFrontend
 *
 * Sets up generic frontend logic.
 *
 * @package     AV-PvH Print Website
 * @author         Mike Nelson
 * @since         $VID:$
 *
 */
class Frontend extends BaseController
{
    /**
     * Request variable indicating a request like AJAX.
     */
    const AVPVH_PRWB_AJAX_INDICATOR = 'avpvh_prwb_ajax';

    /**
     * Request variable indicating a request to generate a project.
     */
    const AVPVH_PRWB_LOADING_PAGE_INDICATOR = 'avpvh_prwb_loading';

    /**
     * Request variable indicating which post to generate for the loading page.
     */
    const AVPVH_PRWB_QUERYARG_POST = 'avpvh_prwb_p';

    /**
     * Request variable indicating which format to generate for the loading page.
     */
    const AVPVH_PRWB_QUERYARG_FORMAT = 'avpvh_prwb_f';

    /**
     * Action indicating we should generate a project or check its status.
     */
    const AVPVH_PRWB_PROJECT_STATUS_ACTION = 'avpvh_prwb_project_status';

    /**
     * Querystring variable name to indicate whether to use or disable the WP theme on the next request
     * (used because we have to decide very early, before most of PMB is initialized)
     */
    const AVPVH_PRWB_USE_THEME = 'avpvh_prwb_use_theme';

    /**
     * @var ProjectManager
     */
    protected $project_manager;

    /**
     * @var FileFormatRegistry
     */
    protected $format_registry;


    /**
     * @var \Avpvh\PrintWebsite\Twine\orm\entities\PostWrapper|null
     */
    protected $project;

    /**
     * Context injects these dependencies.
     * @param ProjectManager $project_manager
     * @param FileFormatRegistry $format_registry
     */
    public function inject(
        ProjectManager $project_manager,
        FileFormatRegistry $format_registry
    ) {
        $this->project_manager = $project_manager;
        $this->format_registry = $format_registry;
    }

    /**
     * Sets up hooks which could be used on the front end.
     */
    public function setHooks()
    {
        add_filter(
            'the_content',
            array($this, 'addPrintButton'),
            /**
             * Allows changing the priority of the print buttons to place them above or below other content
             * automatically added.
             */
            apply_filters(
                'Avpvh\PrintWebsite\controllers\PmbFrontend->setHooks $priority',
                10
            )
        );

        add_filter(
            'template_redirect',
            array($this, 'templateRedirect'),
            10
        );

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
     * @param string $content
     * @return string
     */
    public function addPrintButton($content)
    {
        global $post;
        if (! $post instanceof WP_Post || ! in_array($post->post_type, ['post', 'page'], true)) {
            return $content;
        }
        $avpvh_prwb_print_settings = Context::instance()->reuse('Avpvh\PrintWebsite\domain\FrontendPrintSettings');

        $postmeta_override = get_post_meta($post->ID, 'avpvh_prwb_buttons', true);
        $active_post_types = $avpvh_prwb_print_settings->activePostTypes();
        if (
            /**
             * Lets you override if AV-PvH Print Website adds print buttons or not.
             *
             * @param bool $show whether to show print buttons on this post/page or not
             * @param WP_Post $post
             * @param FrontendPrintSettings $avpvh_prwb_print_settings
             * @param string $active_post_types 'show' to always show buttons on this post, 'hide' to hide on this post,
             *                                  'default', null, or false to use the default settings.
             * default settings.
             */
            apply_filters(
                'Avpvh\PrintWebsite\controllers\PmbFrontend->addPrintButtons $show_buttons',
                (
                isset($active_post_types[$post->post_type])
                && $active_post_types[$post->post_type]
                && (is_single() || $post->post_type === 'page')
                && ! post_password_required($post)
                && $postmeta_override !== 'hide'
                )
                || $postmeta_override === 'show',
                $post,
                $avpvh_prwb_print_settings,
                $postmeta_override
            )
        ) {
            $html = Context::instance()->reuse('Avpvh\PrintWebsite\domain\PrintButtons')->getHtmlForPrintButtons($post);
            $add_to_top = apply_filters(
                '\Avpvh\PrintWebsite\controllers\PmbFrontend->addPrintButton $add_to_top',
                $avpvh_prwb_print_settings->showButtonsAbove(),
                $post
            );
            if ($add_to_top) {
                return $html . $content;
            } else {
                return $content . $html;
            }
        }
        return $content;
    }

    /**
     * Determines if the request is for our page generator page, and if so, uses our template for it.
     * @param string $template
     * @deprecated 2.2.3. Instead use `PrintMyBlog/controllers/PmbPrintPage::templateRedirect`
     */
    public function templateInclude($template)
    {

        // check for loading page
        if (isset($_REQUEST[self::AVPVH_PRWB_LOADING_PAGE_INDICATOR])){
            return $this->loadingPage();
        }

        return $template;
    }

    public function templateRedirect(){
        // check for PMB ajax
        if (isset($_REQUEST[self::AVPVH_PRWB_AJAX_INDICATOR], $_REQUEST['action']) && $_REQUEST['action'] === self::AVPVH_PRWB_PROJECT_STATUS_ACTION) {
            return $this->pmbAjax();
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function loadingPage(){
        if (! check_admin_referer(Frontend::AVPVH_PRWB_LOADING_PAGE_INDICATOR)) {
            wp_die('The request has expired. Please refresh the previous page and try again.');
        }
        global $avpvh_prwb_format;
        $avpvh_prwb_format = Array2::setOr($_REQUEST, self::AVPVH_PRWB_QUERYARG_FORMAT, DefaultFileFormats::DIGITAL_PDF);
        $post_id = Array2::setOr($_REQUEST, self::AVPVH_PRWB_QUERYARG_POST, 0);
        if( ! $post_id){
            throw new Exception(__('Invalid Post ID. The link you used to get here may be old. If it\'s new, please contact AV-PvH Print Website support.', 'avpvh-print-website'));
        }
        $this->project = $this->project_manager->getById($post_id);
        add_action(
            'wp_enqueue_scripts',
            array($this, 'loadingPageEnqueueScripts'),
            100
        );
        return AVPVH_PRWB_TEMPLATES_DIR . 'loading.php';
    }

    public function loadingPageEnqueueScripts(){
        wp_enqueue_script(
            'avpvh-print-website-loading',
            AVPVH_PRWB_SCRIPTS_URL . 'pmb-loading.js',
            array('jquery', 'avpvh-print-website-general'),
            filemtime(AVPVH_PRWB_SCRIPTS_DIR . 'pmb-loading.js')
        );
        wp_enqueue_style(
            'avpvh-print-website-print-page',
            AVPVH_PRWB_STYLES_URL . 'pmb-loading.css',
            array('avpvh-print-website-print-common'),
            filemtime(AVPVH_PRWB_STYLES_DIR . 'pmb-loading.css')
        );

        $format = Array2::setOr($_REQUEST, 'avpvh_prwb_f',DefaultFileFormats::DIGITAL_PDF);
        $design = $this->project->getDesignFor($format);
        $use_theme = $design->getPmbMeta('use_theme');
        wp_localize_script(
            'avpvh-print-website-loading',
            'avpvh-print-website-loading',
            [
                'external_requests_enabled' => avpvh_prwb_external_requests_enabled(),
                'generate_ajax_data' => apply_filters(
                    '\Avpvh\PrintWebsite\controllers\Admin->enqueueScripts generate generate_ajax_data',
                    [
                        'action' => Frontend::AVPVH_PRWB_PROJECT_STATUS_ACTION,
                        'ID' => $this->project->getWpPost()->ID,
                        '_nonce' => wp_create_nonce('pmb-loading'),
                        'format' => $format,
                        self::AVPVH_PRWB_USE_THEME =>$use_theme ? '1' : '0',
                    ]
                ),
                'avpvh_prwb_ajax' => avpvh_prwb_ajax_url(),
                'site_url' => site_url(),

                'translations' => [
                    'error_generating' => __('There was an error preparing your content.', 'avpvh-print-website'),
                    'socket_error' => __('Your saved print could not be accessed in order to generate the file. Maybe your website is not public?', 'avpvh-print-website'),
                ],
            ]
        );
    }



    /**
     * Basically do AJAX logic. We used to simply use AJAX endpoint, but it sets IS_ADMIN to true,
     * which makes lots of plugins malfunction. Plus, many legitimately think they don't need to enqueue
     * scripts on AJAX requests.
     */
    protected function pmbAjax(){
        if (! isset($_POST['_nonce']) || ! wp_verify_nonce(sanitize_key($_POST['_nonce']), 'pmb-loading')) {
            wp_send_json_error(
                [
                    'error' => 'nonce_failure',
                    'message' => 'Nonce failure',
                ]
            );
            return;
        }
        // Only show errors if AVPVH_PRWB_DEBUG is on.
        if (defined('AVPVH_PRWB_DEBUG') ? AVPVH_PRWB_DEBUG : false) {
            // We want to see errors, so make sure they're set to display.
            // phpcs:disable WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting, WordPress.PHP.IniSet.display_errors_Blacklisted
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            // phpcs:enable WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting, WordPress.PHP.IniSet.display_errors_Blacklisted
        }
        // Find project by ID.
        // @var $project Project just so PHPstorm knows what it's dealing with.
        $project = $this->project_manager->getById((int)Array2::setOr($_REQUEST, 'ID', ''));
        $format = $this->format_registry->getFormat(sanitize_key(Array2::setOr($_REQUEST, 'format', '')));
        // @var $project_generation ProjectGeneration just so PHPstorm knows what I'm doing.
        $project_generation = $project->getGenerationFor($format);
        $project_generation->deleteGeneratedFiles();
        $project_generation->clearDirty();
        $project->getProgress()->markStepComplete(ProjectProgress::GENERATE_STEP);
        $done = $project_generation->generateIntermediaryFile();
        if ($done) {
            $url = $project_generation->getGeneratedIntermediaryFileUrl();
        } else {
            $url = null;
        }

        // If we're all done, return the file.
        $response = [
            'url' => $url,
            'media' => $format->slug() === 'digital_pdf' ? 'screen' : 'print',
        ];
        wp_send_json($response);
        exit;
    }
}
