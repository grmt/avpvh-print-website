function avpvh_prwb_generate_doc_from_html(is_preview, success_callback, failure_callback){
    var use_middleman = parseInt(avpvh_prwb_pro.use_avpvh_prwb_central_for_previews) || ! is_preview;
    if(use_middleman){
        var authorization_data = avpvh_prwb_pro.license_data;
    } else {
        var authorization_data = 'YOUR_API_KEY_HERE';
    }

    var dynamic_doc_attrs = JSON.parse(JSON.stringify(avpvh_prwb_pro.doc_attrs));
    // if this a preview, always override whether its a test request or not.
    if(is_preview){
        dynamic_doc_attrs.test = true;
    }
    var jq_html = jQuery('html');
    var html = '<html class="' + jq_html[0].className + '">' + jq_html.html() + '</html>';

    // don't send script tags, as we don't want Prince to execute Javascript we've already executed in the browser
    var open_tag = '<script';
    var close_tag = '</script>';
    var open_tag_pos = html.indexOf(open_tag);
    var close_tag_pos = html.indexOf(close_tag);

    while(open_tag_pos !== -1){
        var pre_script = html.substring(0,open_tag_pos);
        var post_script = html.substring(close_tag_pos + close_tag.length);
        html =  pre_script + post_script;
        open_tag_pos = html.indexOf(open_tag);
        close_tag_pos = html.indexOf(close_tag);
    }

    // unleash the Javascript for Prince!!
    html = html
        .replaceAll('<prince-script', '<script')
        .replaceAll('</prince-script>','</script>');

    // jQuery escaped the contents of the Prince script when we fetched it, so un-escaped it using underscore.js
    var open_tag_pos = html.indexOf('<script');
    var close_tag_pos = html.indexOf(close_tag);
    var pre_script = html.substring(0,open_tag_pos);
    var script_contents = html.substring(open_tag_pos + open_tag.length + 1, close_tag_pos);
    script_contents = _.unescape(script_contents);
    var post_script = html.substring(close_tag_pos + close_tag.length);
    html =  pre_script + open_tag + '>' + script_contents + close_tag + post_script;

    dynamic_doc_attrs.document_content = html;
    console.log(html);


    var server_communicator = new PmbAsyncPdfCreation(
        use_middleman,
        authorization_data,
        dynamic_doc_attrs,
        (response) => {
            console.log(response);
        },
        success_callback,
        failure_callback
    );
    server_communicator.begin();
}

/**
 * Generates the test document from the current page, downloads it, and enables downloading the paid PDF
 * @param jqelement
 */
function avpvh_prwb_generate_test_doc(jqelement) {
    avpvh_prwb_generate_doc_from_html(
        true,
        (download_url) => {

            avpvh_prwb_stop_doing_button(jqelement);
            jQuery('.pmb-download-live').removeClass('pmb-disabled');
            jQuery('.pmb-pro-description').html(avpvh_prwb_pro.translations.pro_description);
            window.location.href = download_url;
        },
        (error_message) => {
            // jQuery('.pmb-downloading-live-pdf').hide();
            // jQuery('.pmb-error-downloading-test-pdf').show();
            if(error_message === 'Socket error downloading document content from supplied url.'){
                error_message = avpvh_prwb_pro.translations.socket_error;
            }
            avpvh_prwb_stop_doing_button(jqelement);
            jQuery.ajax(
                avpvh_prwb_pro.ajaxurl,
                {
                    'method': 'POST',
                    'data':{
                        'action':'avpvh_prwb_report_error',
                        'error': error_message,
                        'project_id': avpvh_prwb_pro.project_id,
                        'format': avpvh_prwb_pro.format
                    }
                }
            );
            alert(error_message);
        }
    );
}

/**
 * Generates the paid document from the current page and downloads it
 * @param jqelement
 */
function avpvh_prwb_generate_live_doc(jqelement) {
    avpvh_prwb_generate_doc_from_html(
        false,
        (download_url) => {
            avpvh_prwb_stop_doing_button(jqelement);
            window.location.href = download_url;
            jQuery.ajax(
                avpvh_prwb_pro.ajaxurl,
                {
                    'method': 'POST',
                    'data':{
                        'action':'avpvh_prwb_reduce_credits',
                        '_wpnonce':avpvh_prwb_pro.avpvh_prwb_nonce,
                    }
                }
            );
            jQuery('.pmb-pro-description').hide();
            jQuery('.pmb-pro-after-pro').show();
        },
        (error_message) => {
            if(error_message === 'Socket error downloading document content from supplied url.'){
                error_message = avpvh_prwb_pro.translations.socket_error;
            }
            avpvh_prwb_stop_doing_button(jqelement);
            jQuery.ajax(
                avpvh_prwb_pro.ajaxurl,
                {
                    'method': 'POST',
                    'data':{
                        'action':'avpvh_prwb_report_error',
                        'error': error_message,
                        'project_id': avpvh_prwb_pro.project_id,
                        'format': avpvh_prwb_pro.format,
                        '_wpnonce':avpvh_prwb_pro.avpvh_prwb_nonce,
                    }
                }
            );
            alert(error_message);
        }
    );
}
/**
 * Hide a targeted print option and replace it with a refresh button and message. The purpose of this function is to avoid rendering issues when two generation buttons are clicked consecutively. 
 * @param {*} option_to_hide The jQuery element of the .pmb-print-option element to hide. Must be either "browser" or "propdf".
 */
function avpvh_prwb_hide_print_option(option_to_hide) {
    const refresh_window_option = jQuery('#pmb-print-refresh-window-option');
    const print_with_browser_option = jQuery('#pmb-print-with-browser-option');
    const print_with_pro_pdf_option = jQuery('#pmb-print-pro-pdf-option');
    const refresh_window_option_button = jQuery('#pmb-refresh-print-window-button');
    const refresh_for_browser_msg = jQuery('#pmb-refresh-for-browser');
    const refresh_for_pro_pdf_msg = jQuery('#pmb-refresh-for-pro-pdf');

    //Attach an event listener to the refresh button to refresh the page when it is clicked.
    refresh_window_option_button.on('click', ()=>{
        location.reload();
    });

    if (option_to_hide === "browser") {
        print_with_browser_option.hide();
        refresh_for_browser_msg.show();
    } else if (option_to_hide === "propdf") {
        print_with_pro_pdf_option.hide();
        refresh_for_pro_pdf_msg.show();
    }    
    
    //Show a message and refresh button. 
    refresh_window_option.show();

}

/**
 * Callbacks that listen for document.avpvh_prwb_doc_conversion_requested should set avpvh_prwb_doc_conversion_request_handled to TRUE immediately, otherwise
 * we'll assume no callback was set and so we'll just proceed with converting the file.
 * @type {boolean}
 */
var avpvh_prwb_doc_conversion_request_handled = false;
/**
 * Keeps track of if we've finished preparing the entire print page. (So we don't process stuff over and over again if the print button
 * gets pressed again.)
 * @type boolean
 */
var avpvh_prwb_pro_page_rendered = false;
jQuery(document).on('ready', function(){
    console.log("Loading document");
    var download_test_button = jQuery('.pmb-download-test');
    setTimeout(function(){
            avpvh_prwb_stop_doing_button(download_test_button);
        },
        2000
    );
    var input = document.getElementById("pmb-print-with-browser");
    input.addEventListener("keyup", function(event) {
        // Number 13 is the "Enter" key on the keyboard
        if (event.keyCode === 13) {
            // Cancel the default action, if needed
            event.preventDefault();
            // Trigger the button element with a click
            document.getElementById("pmb-print-with-browser").click();
        }
    });
    download_test_button.click(function(event){
        //Hide the Print with Browser print option and replace it with a refresh button and message.
        avpvh_prwb_hide_print_option('browser');

        var jqelement = jQuery(event.currentTarget);
        avpvh_prwb_doing_button(jqelement);
        if(avpvh_prwb_pro_page_rendered){
            avpvh_prwb_generate_test_doc(jqelement);
        } else {
            // wait for the design to call document.avpvh_prwb_doc_conversion_ready (and to set avpvh_prwb_doc_conversion_request_handled
            // to true)  before proceeding with converting HTML to ePub
            jQuery(document).on('avpvh_prwb_doc_conversion_ready', function(){
                avpvh_prwb_generate_test_doc(jqelement);
            });
            jQuery(document).trigger('avpvh_prwb_doc_conversion_requested');
            // trigger document.avpvh_prwb_wrap_up for legacy code.
            jQuery(document).trigger('avpvh_prwb_wrap_up');
            avpvh_prwb_pro_page_rendered = true;
        }
    });
    jQuery('.pmb-download-live').click(function(event){
        var jqelement = jQuery(event.currentTarget);
        avpvh_prwb_generate_live_doc(jqelement);
        avpvh_prwb_doing_button(jqelement);
    });
    jQuery('#pmb-print-with-browser').click(function(event){
        if(avpvh_prwb_pro_page_rendered){
            window.print();
        } else {
            //Hide the Pro PDF Service print option and replace it with a refresh button and message.
            avpvh_prwb_hide_print_option('propdf');

            var jqelement = jQuery(event.currentTarget);
            avpvh_prwb_doing_button(jqelement);
            // wait for the design to call document.avpvh_prwb_doc_conversion_ready (and to set avpvh_prwb_doc_conversion_request_handled
            // to true)  before proceeding with converting HTML to ePub
            jQuery(document).on('avpvh_prwb_doc_conversion_ready', function(){
                window.print();
                setTimeout(
                    function(){
                        avpvh_prwb_stop_doing_button(jqelement);
                    },
                    2000
                );
            });
            jQuery('.pmb-pro-only').remove();
            jQuery(document).trigger('avpvh_prwb_doc_conversion_requested');
            // trigger document.avpvh_prwb_wrap_up for legacy code.
            jQuery(document).trigger('avpvh_prwb_wrap_up');
            avpvh_prwb_pro_page_rendered = true;
        }

    });
});