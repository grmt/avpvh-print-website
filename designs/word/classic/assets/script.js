// once doc conversion requested, process the HTML and trigger when we're ready.
jQuery(document).on('avpvh_prwb_doc_conversion_requested', function(){
    // inform pmb-word__prmemium_only.js that we've handled this
    avpvh_prwb_doc_conversion_request_handled = true;
    avpvh_prwb_standard_print_page_wrapup();
    avpvh_prwb_default_align_center();

    avpvh_prwb_replace_links_for_word(avpvh_prwb_design_options['external_links'], avpvh_prwb_design_options['internal_links']);
    avpvh_prwb_fix_headings_for_word_toc();
    // once done loading external images, tell ePub generator code that we're done.
    jQuery(document).on("avpvh_prwb_external_resouces_loaded", function() {
        jQuery(document).trigger('avpvh_prwb_doc_conversion_ready');
    });
    if(avpvh_prwb_design_options.convert_videos === '1') {
        jQuery(document).on('avpvh_prwb_done_processing_videos', function () {
            avpvh_prwb_word_classic_after_videos();
            var erc = new PmbExternalResourceCacher();
            erc.replaceExternalImages();
        });
        avpvh_prwb_convert_youtube_videos_to_images('simple');
    } else {
        avpvh_prwb_word_classic_after_videos();
        var erc = new PmbExternalResourceCacher();
        erc.replaceExternalImages();
    }
});

function avpvh_prwb_word_classic_after_videos(){
    avpvh_prwb_change_image_quality(avpvh_prwb_design_options.image_quality, avpvh_prwb_design_options.domain);
}