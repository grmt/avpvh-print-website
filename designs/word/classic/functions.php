<?php
// Add filters, action callback, and functions you want to use in your design.
// Note that this file only gets included when generating a new project, not on every pageload.
add_action(
	'avpvh_prwb_pdf_generation_start',
	function(\Avpvh\PrintWebsite\entities\ProjectGeneration $project_generation, \Avpvh\PrintWebsite\orm\entities\Design $design){
	    global $avpvh_prwb_design;
	    $avpvh_prwb_design = $design;
	    add_action('wp_enqueue_scripts', 'avpvh_prwb_enqueue_classic_script', 1001);
	},
	10,
	2
);

function avpvh_prwb_enqueue_classic_script(){
    /**
     * @var $avpvh_prwb_design \Avpvh\PrintWebsite\orm\entities\Design
     */
    global $avpvh_prwb_design;
    wp_add_inline_style(
        'avpvh-print-website-print-common',
        $avpvh_prwb_design->getSetting('custom_css')
    );
    wp_localize_script(
        'avpvh-print-website-design',
        'avpvh_prwb_design_options',
        avpvh_prwb_design_settings($avpvh_prwb_design)
    );
}