<?php
/**
* @var $project_generation \Avpvh\PrintWebsite\entities\ProjectGeneration
 */
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
            <div class="pmb-pro-window-title"><h1><?php esc_html_e('AV-PvH Print Website — Saved Prints', 'avpvh-print-website');?></h1></div>
            <div class="pmb-pro-window-topbar-right">
            </div>
        </div>
        <div class="pmb-pro-print-window-content">
            <div class="pmb-pro-print-window-options">
                <div class="pmb-print-option pmb-constrained pmb-highlight">
                    <h2><?php esc_html_e('ePub Generator', 'avpvh-print-website');?></h2>
                    <p><?php esc_html_e('Generate an ePub file for reading on eReaders, tablets, and phones.', 'avpvh-print-website');?></p>
                    <div style="display:none" class="pmb-warning" id="pmb-print-page-warnings"></div>
                    <a class="pmb-pro-window-button pmb-disabled" id="download_link"  download="<?php echo esc_attr($project_generation->getFileNameWithExtension());?>"><?php esc_html_e('Download ePub', 'avpvh-print-website');?><div class="pmb-spinner-container"><div class="pmb-spinner"></div></div></a>
                    <p><?php esc_html_e('Please refer to our documentation if you have questions about styling ', 'avpvh-print-website');?></p>
                </div>
            </div>
        </div>
    </div>
    <?php do_action('avpvh_prwb_pro_print_page_window_end', 'epub');?>
</div>
