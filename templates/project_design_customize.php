<?php
/**
 * @var $form \Avpvh\PrintWebsite\Twine\forms\base\FormSection
 * @var $form_url string
 * @var $design \Avpvh\PrintWebsite\orm\entities\Design
 * @var $steps_to_urls array
 * @var $current_step string
 * @var $project \Avpvh\PrintWebsite\orm\entities\Project
 */
// outputs the form for the design

// save button, and textbox to make into a new design

avpvh_prwb_render_template(
    'partials/project_header.php',
    [
        'project' => $project,
        'page_title' => sprintf(
            __('Customize %s Design: %s', 'avpvh-print-website'),
            $design->getDesignTemplate()->getFormat()->coloredTitleAndIcon(),
            $design->getWpPost()->post_title
        ),
        'current_step' => $current_step,
        'steps_to_urls' => $steps_to_urls
    ]
);
avpvh_prwb_render_template(
    'partials/customize_design.php',
    [
        'design'=> $design,
        'form' => $form,
        'form_url' => $form_url
    ]
);
avpvh_prwb_render_template('partials/project_footer.php');
