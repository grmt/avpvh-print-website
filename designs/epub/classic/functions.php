<?php // phpcs:disable Files.SideEffects.FoundWithSymbols -- sorry, this file is meant for everything
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

add_filter(
    '\Avpvh\PrintWebsite\services\generators\EpubGenerator::enqueueStylesAndScripts $css',
    function($css, $design, $project){
        $header_font_size = $design->getSetting('main_header_font_size');
        $font_size = $design->getSetting('font_size');

        $font = $design->getSetting('custom_font');
        if($font) {
            $css .= '
            @font-face{
                font-family: "' . avpvh_prwb_get_filename($font, true) . '";
                font-style: normal;
                font-weight: normal;
                src : url(./fonts/' . avpvh_prwb_get_filename($font) . ');
            }
            body{
                    font-family: "' . avpvh_prwb_get_filename($font, true) . '", georgia, serif;
                    font-size: ' . $font_size . ';
            }
            ';
        }
        $header_font = $design->getSetting('custom_header_font');
        if($header_font) {
            $css .= '
            @font-face{
                font-family: "' . avpvh_prwb_get_filename($header_font, true) . '";
                font-style: normal;
                font-weight: normal;
                src : url(./fonts/' . avpvh_prwb_get_filename($header_font) . ');
            }
            h1, h2, h3, h4, h5, h6{
                    font-family: "' . avpvh_prwb_get_filename($header_font, true) . '", arial, serif;
                }
            h1.pmb-title, h1.site-title{
                    font-size: ' . $header_font_size . ';
                }';
        }
        return $css;
    },
    10,
    3
);

/**
 * Enqueues scripts on print page.
 * @throws Exception
 */
function avpvh_prwb_enqueue_classic_script()
{
    /**
     * @var $avpvh_prwb_design \Avpvh\PrintWebsite\orm\entities\Design
     */
    global $avpvh_prwb_design;
    $font = $avpvh_prwb_design->getSetting('custom_font');
    $header_font = $avpvh_prwb_design->getSetting('custom_header_font');
    $fonts_to_embed = [];
    if($font){
        $fonts_to_embed[] = [
            'filename' => avpvh_prwb_get_filename($font, false),
            'url' => $font
        ];
    }
    if($header_font){
        $fonts_to_embed[] = [
            'filename' => avpvh_prwb_get_filename($header_font, false),
            'url' => $header_font
        ];
    }
    wp_localize_script(
        'avpvh-print-website-design',
        'avpvh_prwb_design_options',
        [
            'convert_videos' => (int)$avpvh_prwb_design->getSetting('convert_videos'),
            'image_quality' => $avpvh_prwb_design->getSetting('image_quality'),
            'domain' => avpvh_prwb_site_domain(),
            'fonts_to_embed' => $fonts_to_embed
        ]
    );
}