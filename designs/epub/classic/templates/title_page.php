<?php
/**
 * @var $avpvh_prwb_project \Avpvh\PrintWebsite\orm\entities\Project
 * @var $avpvh_prwb_design \Avpvh\PrintWebsite\orm\entities\Design
 *
 */
?>
<div class="pmb-posts-header">
    <?php
    if (in_array('title', $avpvh_prwb_design->getSetting('header_content'), true)) {
        ?>
        <h1 class="site-title"><?php $avpvh_prwb_project->echoPublishedTitle(); ?></h1>
        <?php
    }
    if (in_array('subtitle', $avpvh_prwb_design->getSetting('header_content'), true)) {
        ?>
        <h2 class="site-description pmb-subtitle"><?php $avpvh_prwb_project->echoSetting('subtitle'); ?></h2>
        <?php
    }
    if (in_array('byline', $avpvh_prwb_design->getSetting('header_content'), true)) {
        ?>
        <h2 class="project-byline"><?php $avpvh_prwb_project->echoSetting('byline'); ?></h2>
        <?php
    }
    ?>
    <div class="pmb-title-page-meta"><?php
        if (in_array('url', $avpvh_prwb_design->getSetting('header_content'), true)) {
            $url_text = $avpvh_prwb_project->getSetting('url');
            $true_url = esc_url_raw($url_text) === $url_text;
            ?>
            <p class="site-url "><?php
                echo esc_html($url_text);
                ?></p>
            <?php
        }
        ?>
        <p class="pmb-printout-meta">
            <?php
            // give it some space
            echo ' ';
            if (in_array('date_printed', $avpvh_prwb_design->getSetting('header_content'), true)) {
                printf(
                    // translators: 1: date
                    esc_html__('Printed on %1$s', 'avpvh-print-website'),
                    esc_html(date_i18n(get_option('date_format')))
                );
            }
            ?></p>
    </div>
