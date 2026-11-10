<?php
// phpcs:disable Files.SideEffects.FoundWithSymbols -- sorry, this file is meant for everything
// Add filters, action callback, and functions you want to use in your design.
// Note that this file only gets included when generating a new project, not on every pageload.
add_action(
    'avpvh_prwb_pdf_generation_start',
    function (\Avpvh\PrintWebsite\entities\ProjectGeneration $project_generation, \Avpvh\PrintWebsite\orm\entities\Design $design) {
        global $avpvh_prwb_design;
        $avpvh_prwb_design = $design;
        add_action('wp_enqueue_scripts', 'avpvh_prwb_enqueue_classic_script', 1001);
    },
    10,
    2
);

/**
 * Enqueues scripts in the print page.
 * @throws Exception
 */
function avpvh_prwb_enqueue_classic_script()
{
    global $avpvh_prwb_design;

    $css = avpvh_prwb_design_styles($avpvh_prwb_design);

    if ($avpvh_prwb_design->getPmbMeta('paragraph_indent')) {
        $css .= ' .pmb-article .post-inner p:not(.has-text-align-center){
                        text-indent:3em;
                        margin:0;
                    }';
    }
    if($avpvh_prwb_design->getSetting('page_per_post')){
        $css .= '
            .pmb-section-wrapper:not(.pmb-first-section-wrapper){
                break-before:page;
        }';
    }
    if ($avpvh_prwb_design->getSetting('dividing_line')) {
        $css .= '.pmb-main-matter .entry-content{border-bottom:1px solid gray;box-decoration-break: slice;}';
    }
    // if we're removing images, figures in image blocks become crazy tall because they're display:table-caption.
    // but set them just to a regular block and they look fine.
    // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison -- we want '0' to match 0.
    if ($avpvh_prwb_design->getPmbMeta('image_size') == 0) {
        $css .= '.wp-block-image figure > figcaption{ display:block;}';
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