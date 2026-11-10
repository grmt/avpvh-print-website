<?php // phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols -- sorry, this file is meant for everything
// Add filters, action callback, and functions you want to use in your design.
// Note that this file only gets included when gnerating a new project, not on every pageload.
add_action(
    'avpvh_prwb_pdf_generation_start',
    function (\Avpvh\PrintWebsite\entities\ProjectGeneration $project_generation, \Avpvh\PrintWebsite\orm\entities\Design $design) {
        global $avpvh_prwb_design;
        $avpvh_prwb_design = $design;
        add_action('wp_enqueue_scripts', 'avpvh_prwb_enqueue_mayer_script', 1001);
    },
    10,
    2
);

/**
 * Adds scripts to print page
 * @throws Exception
 */
function avpvh_prwb_enqueue_mayer_script()
{
    global $avpvh_prwb_design;
    $css = avpvh_prwb_design_styles($avpvh_prwb_design);
    if ($avpvh_prwb_design->getSetting('post_header_in_columns')) {
        $css .= ' .pmb-main-matter{columns:2}';
    } else {
        $css .= 'article:not(.pmb-just-content) div.entry-content{columns:2}';
    }
    if ($avpvh_prwb_design->getSetting('dividing_line')) {
        $css .= '.pmb-main-matter .entry-content{border-bottom:1px solid gray;box-decoration-break: slice;}';
    }
    if ($avpvh_prwb_design->getSetting('images_full_column')) {
        $css .= ' figure.wp-caption:not(.mayer-no-resize, .emoji), figure.wp-block-image:not(.mayer-no-resize, .emoji), .pmb-posts .pmb-image img:not(.mayer-no-resize, .emoji), img:not(.mayer-no-resize, .emoji){width:100% !important;height:auto;}';
    }
    if ($avpvh_prwb_design->getSetting('page_per_post')) {
        $css .= ' article{page-break-before:always}';
    }
    if ($avpvh_prwb_design->getSetting('no_extra_columns')) {
        $css .= ' .pmb-print-page .wp-block-columns{display:block;}';
    }
    wp_add_inline_style(
        'avpvh-print-website-print-common',
        $css
    );
    wp_localize_script(
        'avpvh-print-website-design',
        'avpvh_prwb_design_options',
        avpvh_prwb_design_settings($avpvh_prwb_design)
    );
}