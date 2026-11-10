// once doc conversion requested, process the HTML and trigger when we're ready.
jQuery(document).on('avpvh_prwb_doc_conversion_requested', function(){
    avpvh_prwb_doc_conversion_request_handled = true;
    // Pretty up the pageit
    avpvh_prwb_standard_print_page_wrapup();
    if(avpvh_prwb_design_options['default_alignment'] === 'center'){
        avpvh_prwb_default_align_center();
    }
    avpvh_prwb_replace_internal_links_with_page_refs_and_footnotes(avpvh_prwb_design_options['external_links'], avpvh_prwb_design_options['internal_links'],avpvh_prwb_design_options['footnote_text'],  avpvh_prwb_design_options['internal_footnote_text']);
    new PmbToc();
    avpvh_prwb_pdf_plugin_fixups();
    jQuery(document).on('avpvh_prwb_done_processing_videos', function() {
        avpvh_prwb_resize_images(parseInt(avpvh_prwb_design_options.image_size, 10));
        if (avpvh_prwb_design_options.image_placement === 'dynamic-resize') {
            avpvh_prwb_mark_for_dynamic_resize(parseInt(avpvh_prwb_design_options.dynamic_resize_min));
        }
        avpvh_prwb_change_image_quality(avpvh_prwb_design_options.image_quality, avpvh_prwb_design_options.domain);
        jQuery(document).trigger('avpvh_prwb_doc_conversion_ready');
    });
    avpvh_prwb_convert_youtube_videos_to_images('pretty', avpvh_prwb_design_options.video_qr_codes);
});