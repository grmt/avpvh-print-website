<?php
/**
* @var $license_info array
 * @var $project Avpvh\PrintWebsite\orm\entities\Project
 */
$generate_url = add_query_arg(
    [
        'ID' => $project->getWpPost()->ID,
        'action' => \Avpvh\PrintWebsite\controllers\Admin::SLUG_ACTION_EDIT_PROJECT,
        'subaction' => \Avpvh\PrintWebsite\entities\ProjectProgress::GENERATE_STEP
    ],
    admin_url(AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH)
);
?>
<!-- AV-PvH Print Website Version <?php echo AVPVH_PRWB_VERSION;?>-->
<div class="pmb-pro-print-window-wrapper">
    <div class="pmb-pro-print-window">
        <div class="pmb-pro-print-window-topbar">
            <div class="pmb-pro-window-topbar-left">
                <a  class="pmb-pro-window-button" href="javascript:history.back();">
                    <span class="pmb-spinner-container"><span class="dashicons dashicons-arrow-left-alt"></span></span>
                    <?php esc_html_e('Back', 'avpvh-print-website'); ?>
                </a>
            </div>
            <div class="pmb-pro-window-title"><h1><?php esc_html_e('AV-PvH Print Website — Projects', 'avpvh-print-website');?></h1></div>
            <div class="pmb-pro-window-topbar-right">
                <a class="pmb-pro-window-button" href="<?php echo esc_url(admin_url(AVPVH_PRWB_ADMIN_HELP_PAGE_PATH));?>" title="Get Help">
                    <span class="dashicons
                dashicons-sos pmb-icon"></span><?php esc_html_e('Help!', 'avpvh-print-website');?></a>
            </div>
        </div>

        <div class="pmb-pro-print-window-content">
            <div class="pmb-pro-print-window-options">

                <div id="pmb-print-with-browser-option" class="pmb-print-option">
                    <h2><?php esc_html_e('Free', 'avpvh-print-website');?></h2>
                    <a id="pmb-print-with-browser" class="pmb-pro-window-button" tabindex="0">
                        <?php _e('Print with Browser', 'avpvh-print-website'); ?>
                    </a>
                    <p><?php esc_html_e('Limited to features supported by your browser.', 'avpvh-print-website');?></p>
                </div>
                <div id="pmb-print-refresh-window-option" class="pmb-print-option" style="display: none">
                    <h2><?php esc_html_e('Note', 'avpvh-print-website');?></h2>
                    <button id="pmb-refresh-print-window-button" class="pmb-pro-window-button"><?php esc_html_e('Refresh', 'avpvh-print-website')?></button>
                    <p id="pmb-refresh-for-browser" style="display: none"><?php esc_html_e('This page has been optimized for PDF generation. Please push the refresh button if you want to print the page through your browser.', 'avpvh-print-website');?></p>
                    <p id="pmb-refresh-for-pro-pdf" style="display: none"><?php esc_html_e('This page has been optimized for printing from your browser. Please push the refresh button if you want to generate a PDF from this page.', 'avpvh-print-website');?></p>
                </div>
                <div id="pmb-print-pro-pdf-option" class="pmb-print-option pmb-big-option pmb-highlight">
                    <h2><?php esc_html_e('PDF Generator', 'avpvh-print-website');?></h2>
                    <div style="display:none" class="pmb-warning" id="pmb-print-page-warnings"></div>
                    <button class="pmb-pro-window-button pmb-download-test pmb-disabled">
                        <?php _e('Download PDF', 'avpvh-print-website'); ?> <div class="pmb-spinner-container"><div class="pmb-spinner"></div></div>
                    </button>
                    <p><?php esc_html_e('Generate a PDF from the current project settings.', 'avpvh-print-website');?></p>
                </div>
            </div>
        </div>
    </div>
    <?php do_action('avpvh_prwb_pro_print_page_window_end', 'pdf');?>
</div>
