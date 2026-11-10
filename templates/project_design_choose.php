<?php
/**
 * @var $designs \Avpvh\PrintWebsite\orm\entities\Design[]
 * @var $chosen_design \Avpvh\PrintWebsite\orm\entities\Design
 * @var $project \Avpvh\PrintWebsite\orm\entities\Project|null
 * @var $format \Avpvh\PrintWebsite\entities\FileFormat
 * @var $steps_to_urls array
 * @var $current_step string
 */

use Avpvh\PrintWebsite\controllers\Admin;
use \Avpvh\PrintWebsite\entities\DesignTemplate;

avpvh_prwb_render_template(
    'partials/project_header.php',
    [
        'project' => $project,
        'page_title' => sprintf(
            __('Choose Design: %s', 'avpvh-print-website'),
            $format->coloredTitleAndIcon()
        ),
        'current_step' => $current_step,
        'steps_to_urls' => $steps_to_urls
    ]
);

avpvh_prwb_render_template(
    'partials/select_designs.php',
    [
        'designs'=> $designs,
        'chosen_design' => $chosen_design,
        'active_text' => __('<span>Active:</span> %s', 'avpvh-print-website'),
        'select_button_text' => esc_html__('Use This Design', 'avpvh-print-website'),
        'select_button_aria_label' => esc_html__('Use the Design "%s"', 'avpvh-print-website'),
        'customize_button_aria_label' => esc_html__('Use and Customize the Design "%s"', 'avpvh-print-website'),
        'format' => $format,
        'context' => 'project'
    ]
);
avpvh_prwb_render_template('partials/project_footer.php');