<?php
/**
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
            </div>
        </div>

        <div class="pmb-pro-print-window-content">
            <div class="pmb-pro-print-window-options">

                <div id="pmb-print-with-browser-option" class="pmb-print-option pmb-big-option pmb-highlight">
                    <h2><?php esc_html_e('PDF', 'avpvh-print-website');?></h2>
                    <div style="display:none" class="pmb-warning" id="pmb-print-page-warnings"></div>
                    <a id="pmb-print-with-browser" class="pmb-pro-window-button" tabindex="0">
                        <?php _e('Print with Browser', 'avpvh-print-website'); ?>
                    </a>
                    <p><?php esc_html_e('Print, or choose “Save as PDF” in the print dialog.', 'avpvh-print-website');?></p>
                </div>
            </div>
        </div>
    </div>
    <?php do_action('avpvh_prwb_pro_print_page_window_end', 'pdf');?>
</div>
