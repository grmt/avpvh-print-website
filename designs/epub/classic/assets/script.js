// once doc conversion requested, process the HTML and trigger when we're ready.
jQuery(document).on('avpvh_prwb_doc_conversion_requested', function(){
    avpvh_prwb_doc_conversion_request_handled = true;
    avpvh_prwb_standard_print_page_wrapup();
    avpvh_prwb_default_align_center();
    avpvh_prwb_replace_internal_links_with_epub_file_links();
    avpvh_prwb_add_alt_tags();
    // avpvh_prwb_resize_images(parseInt(avpvh_prwb_design_options['image_size'],10));
    avpvh_prwb_change_image_quality(avpvh_prwb_design_options.image_quality, avpvh_prwb_design_options.domain);

    // once done loading external images, tell ePub generator code that we're done.
    jQuery(document).on("avpvh_prwb_external_resouces_loaded", function() {
        jQuery(document).trigger('avpvh_prwb_doc_conversion_ready');
    });
    if(avpvh_prwb_design_options.convert_videos === '1'){
        // if processing videos, wait until that's done to replace images.
        jQuery(document).on('avpvh_prwb_done_processing_videos', function(){
            var erc = new PmbExternalResourceCacher();
            erc.replaceExternalImages();
        });
        avpvh_prwb_convert_youtube_videos_to_images('simple');
    } else {
        var erc = new PmbExternalResourceCacher();
        erc.replaceExternalImages();
    }
});
