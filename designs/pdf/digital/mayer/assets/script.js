// once doc conversion requested, process the HTML and trigger when we're ready.
jQuery(document).on('avpvh_prwb_doc_conversion_requested', function(){
    avpvh_prwb_doc_conversion_request_handled = true;
    // Pretty up the page
    avpvh_prwb_standard_print_page_wrapup();
    // There's not enough space for alignnone images to ever look good.
    avpvh_prwb_default_align_center();
    avpvh_prwb_replace_internal_links_with_page_refs_and_footnotes('leave', 'parens');
    new PmbToc();
    avpvh_prwb_pdf_plugin_fixups();
    jQuery(document).on('avpvh_prwb_done_processing_videos', function() {
        avpvh_prwb_change_image_quality(avpvh_prwb_design_options.image_quality, avpvh_prwb_design_options.domain);
        jQuery(document).trigger('avpvh_prwb_doc_conversion_ready');
    });
    avpvh_prwb_convert_youtube_videos_to_images('pretty', avpvh_prwb_design_options.video_qr_codes);
});