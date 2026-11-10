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
    var input = document.getElementById("pmb-print-with-browser");
    input.addEventListener("keyup", function(event) {
        // Number 13 is the "Enter" key on the keyboard
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("pmb-print-with-browser").click();
        }
    });
    jQuery('#pmb-print-with-browser').click(function(event){
        if(avpvh_prwb_pro_page_rendered){
            window.print();
        } else {
            var jqelement = jQuery(event.currentTarget);
            avpvh_prwb_doing_button(jqelement);
            // wait for the design to call document.avpvh_prwb_doc_conversion_ready (and to set avpvh_prwb_doc_conversion_request_handled
            // to true) before printing
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
