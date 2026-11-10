<?php // phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols -- sorry, this file is meant for everything
// Add filters, action callback, and functions you want to use in your design.
// Note that this file only gets included when gnerating a new project, not on every pageload.
add_action(
    'avpvh_prwb_pdf_generation_start',
    function (\Avpvh\PrintWebsite\entities\ProjectGeneration $project_generation, \Avpvh\PrintWebsite\orm\entities\Design $design) {
        global $avpvh_prwb_design;
        $avpvh_prwb_design = $design;
        add_action('wp_enqueue_scripts', 'avpvh_prwb_enqueue_haller_script', 1001);
    },
    10,
    2
);

/**
 * Adds scripts to print page
 * @throws Exception
 */
function avpvh_prwb_enqueue_haller_script()
{
    global $avpvh_prwb_design;
    $css = avpvh_prwb_design_styles($avpvh_prwb_design);
    $css .= '@media print, screen {
                @page { size:' . $avpvh_prwb_design->getSetting('page_width') . ' ' . $avpvh_prwb_design->getSetting('page_height') . ';
                }
            }';

    $columns = (int)$avpvh_prwb_design->getSetting('columns');
    $css .= '.pmb-front-matter, .pmb-main-matter, .pmb-back-matter{columns:' . $columns . '}';

    if ($avpvh_prwb_design->getSetting('images_full_column')) {
        $css .= ' figure.wp-caption:not(.mayer-no-resize, .emoji), figure.wp-block-image:not(.mayer-no-resize, .emoji), .pmb-posts .pmb-image img:not(.mayer-no-resize, .emoji), img:not(.mayer-no-resize, .emoji){width:100% !important;height:auto;}
        .wp-block-image:not(.mayer-no-resize) figure{margin:0 0 0 0; width:100%}';
    }
    if ($avpvh_prwb_design->getSetting('no_extra_columns')) {
        $css .= '.pmb-section:not(.pmb-single-column) .wp-block-columns{display:block;}';
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