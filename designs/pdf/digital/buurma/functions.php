<?php // phpcs:disable Files.SideEffects.FoundWithSymbols -- sorry, this file is meant for everything
// Add filters, action callback, and functions you want to use in your design.
// Note that this file only gets included when gnerating a new project, not on every pageload.
add_action(
    'avpvh_prwb_pdf_generation_start',
    function (\Avpvh\PrintWebsite\entities\ProjectGeneration $project_generation, \Avpvh\PrintWebsite\orm\entities\Design $design) {
        global $avpvh_prwb_design;
        $avpvh_prwb_design = $design;
        add_action('wp_enqueue_scripts', 'avpvh_prwb_enqueue_buurma_script', 1001);
        add_filter(
            'Avpvh\PrintWebsite\services\generators\PdfGenerator->printScripts prince_js_vars',
            'avpvh_prwb_buurma_modify_prince_vars'
        );
    },
    10,
    2
);

/**
 * Enqueues scripts.
 * @throws Exception
 */
function avpvh_prwb_enqueue_buurma_script()
{
    global $avpvh_prwb_design;
    $css = avpvh_prwb_design_styles($avpvh_prwb_design);
    $svg_doer = new \Avpvh\PrintWebsite\services\SvgDoer();
    $svg_data = $svg_doer->getSvgDataAsColor(
        $avpvh_prwb_design->getDesignTemplate()->getDir() . 'assets/banner.svg',
        $avpvh_prwb_design->getSetting('title_page_banner_color')
    );
    $bg_color = $avpvh_prwb_design->getSetting('background_color');
    $color_guru = new \Avpvh\PrintWebsite\services\ColorGuru();
    wp_add_inline_style(
        'avpvh-print-website-print-common',
        $css . '
					/* BUURMA DESIGN INLINE CSS */
					@page title-page /*body*/{
					background: url("' . $svg_data . '") no-repeat,
						url("' . $avpvh_prwb_design->getSetting('background_embellishment') . '") center center no-repeat,
						linear-gradient(' . $color_guru->convertHextToRgba($bg_color, 1) . ', ' . $color_guru->convertHextToRgba($bg_color, .3) . ');
					background-size:
						/* banner */ 100% 150px, 
						/* logo */ 40%, 
						/* gradient */ 40%;
					@top-right {
			            content: "' . $avpvh_prwb_design->getSetting('org') . '";
			            color:white;
			        }
				}
				@page front-matter{
					background: url(' . $avpvh_prwb_design->getSetting('background_embellishment') . ') right bottom/150px no-repeat,
						linear-gradient(127deg, ' . $color_guru->convertHextToRgba($bg_color, .3) . ' 0%, ' . $color_guru->convertHextToRgba($bg_color, .3) . ' 80%, ' . $color_guru->convertHextToRgba($bg_color, 1) . ' 100%);
				}
				@page main /*article*/{
					background: url(' . $avpvh_prwb_design->getSetting('background_embellishment') . ') right bottom/150px no-repeat,
						linear-gradient(127deg, ' . $color_guru->convertHextToRgba($bg_color, .3) . ' 0%, ' . $color_guru->convertHextToRgba($bg_color, .3) . ' 80%, ' . $color_guru->convertHextToRgba($bg_color, 1) . ' 100%);
				}
				@page back-matter{
					background: url(' . $avpvh_prwb_design->getSetting('background_embellishment') . ') center center no-repeat,
						linear-gradient(' . $color_guru->convertHextToRgba($bg_color, 1) . ', ' . $color_guru->convertHextToRgba($bg_color, .3) . ');
					background-size:80%, 100%;
				}'
    );
    wp_localize_script(
        'avpvh-print-website-design',
        'avpvh_prwb_design_options',
        avpvh_prwb_design_settings($avpvh_prwb_design)
    );
}

/**
 * Tell the generator we use a post-per-page so layout can be more efficient.
 * @param array $prince_js_vars
 * @return array
 */
function avpvh_prwb_buurma_modify_prince_vars($prince_js_vars)
{
    $prince_js_vars['page_per_post'] = true;
    return $prince_js_vars;
}