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

/**
 * Lays the print page out in pages with Paged.js, so the designs' page numbers, running headers, table of contents
 * page numbers, page references and footnotes work when printing or saving as PDF from the browser.
 *
 * Only the content is paginated: the print window stays above the pages on screen and is hidden when printing.
 * @return {Promise} resolves when the pages are ready (straight away if Paged.js isn't available).
 */
function avpvh_prwb_paginate() {
    // paged.min.js registers itself as window.PagedModule.
    var paged = window.PagedModule || window.Paged;
    if (! paged || typeof paged.Previewer === 'undefined') {
        return Promise.resolve();
    }
    var body = document.body;
    var print_window = document.querySelector('.pmb-pro-print-window-wrapper');

    // Paged.js takes every stylesheet out of the page and puts back processed copies. Keep a screen-only copy of
    // the print window's styles (Paged.js leaves screen-only stylesheets alone).
    ['avpvh-print-website-pro-page-css', 'avpvh-print-website-pro-pdf-css'].forEach(function (id) {
        var link = document.getElementById(id);
        if (link) {
            var copy = link.cloneNode();
            copy.removeAttribute('id');
            copy.media = 'screen';
            document.head.appendChild(copy);
        }
    });

    // Stylesheets that WordPress printed in the body would end up inside the content, unprocessed, and push it to
    // a second page. Move them to the head (keeping their order) so Paged.js processes them like the others. The
    // Prince script is only for the old Prince renderer.
    body.querySelectorAll("link[rel='stylesheet'], style").forEach(function (element) {
        document.head.appendChild(element);
    });
    body.querySelectorAll('prince-script').forEach(function (element) {
        element.remove();
    });

    // CSS Paged Media gives an element without a named page of its own the named page of its first child (its
    // "start page"), as Prince does. Paged.js doesn't, so the wrappers around the content got an empty first page
    // before the title page. Give those elements their start page. Paged.js also lets an element without a named
    // page take its ancestor's, so below such a wrapper the later children get their own start page, or "pmb-auto"
    // (a name no @page rule uses, so only the general @page rules apply, like Prince's default page).
    var page_names = {};
    var not_content = ['LINK', 'STYLE', 'SCRIPT', 'PRINCE-SCRIPT', 'TEMPLATE', 'NOSCRIPT', 'META'];
    var content_children = function (element) {
        return Array.from(element.children).filter(function (child) {
            return not_content.indexOf(child.nodeName) === -1;
        });
    };
    var used_page = function (element) {
        var name = getComputedStyle(element).page;
        return name && /^[a-zA-Z_-][a-zA-Z0-9_-]*$/.test(name) ? name : 'auto';
    };
    var start_page = function (element) {
        var own = used_page(element);
        if (own !== 'auto') {
            return own;
        }
        var children = content_children(element);
        return children.length ? start_page(children[0]) : 'auto';
    };
    var set_page = function (element, name) {
        element.setAttribute('data-pmb-page', name);
        page_names[name] = true;
    };
    var apply_start_pages = function (element, below_named_wrapper) {
        if (used_page(element) !== 'auto') {
            // Its own named page (and its descendants') comes from the design's CSS, which Paged.js follows.
            return;
        }
        var start = start_page(element);
        if (start === 'auto') {
            if (below_named_wrapper) {
                // Its descendants take this from it; marking them too would make Paged.js break before each one.
                set_page(element, 'pmb-auto');
            }
            return;
        }
        set_page(element, start);
        content_children(element).forEach(function (child) {
            apply_start_pages(child, true);
        });
    };
    Array.from(body.children).forEach(function (element) {
        if (element !== print_window && not_content.indexOf(element.nodeName) === -1) {
            apply_start_pages(element, false);
        }
    });
    var page_rules = Object.keys(page_names).map(function (name) {
        return '[data-pmb-page="' + name + '"]{page:' + name + ';}';
    }).join('');
    if (page_rules) {
        var page_style = document.createElement('style');
        page_style.textContent = page_rules;
        document.head.appendChild(page_style);
    }

    // Paged.js finds the target of target-counter() (TOC page numbers, "see page 12") with a CSS selector. Section
    // ids are URL-like ("example.com/my-post/"), which don't work as selectors, so every target came out as page 0.
    // Give those elements simple ids and point the in-document links at them.
    var new_ids = {};
    var id_count = 0;
    body.querySelectorAll('[id]').forEach(function (element) {
        if (! print_window.contains(element) && ! /^[A-Za-z][A-Za-z0-9_-]*$/.test(element.id)) {
            var new_id = 'pmb-target-' + (++id_count);
            new_ids[element.id] = new_id;
            element.setAttribute('data-pmb-original-id', element.id);
            element.id = new_id;
        }
    });
    body.querySelectorAll('a[href^="#"]').forEach(function (a) {
        var target = a.getAttribute('href').substring(1);
        try {
            target = decodeURIComponent(target);
        } catch (e) {
            // keep it as it is
        }
        if (new_ids[target]) {
            a.setAttribute('href', '#' + new_ids[target]);
        }
    });

    // Move the content into a template for Paged.js: not the print window, and not the scripts, links and meta tags
    // that end up in the body (Paged.js would wrap them in an element of their own, on an empty first page).
    var template = document.createElement('template');
    Array.from(body.childNodes).forEach(function (node) {
        if (node === print_window || not_content.indexOf(node.nodeName) !== -1) {
            return;
        }
        template.content.appendChild(node);
    });
    var render_to = document.createElement('div');
    render_to.className = 'pmb-pagedjs-pages';
    body.appendChild(render_to);

    var previewer = new paged.Previewer();
    return previewer.preview(template.content, undefined, render_to).then(function () {
        var style = document.createElement('style');
        style.setAttribute('data-pagedjs-ignore', '');
        style.textContent =
            '@media screen{'
            + 'body{background:#e5e5e5;}'
            // Paged.js applies the stylesheets' print rules on screen too, including the one hiding this window.
            + '.pmb-pro-print-window-wrapper{display:block !important;background:#fff;}'
            + '.pmb-pagedjs-pages .pagedjs_pages{display:flex;flex-direction:column;align-items:center;}'
            + '.pmb-pagedjs-pages .pagedjs_page{background:#fff;margin:1em 0;box-shadow:0 0 4px rgba(0,0,0,.3);}'
            + '}'
            + '@media print{.pmb-pro-print-window-wrapper{display:none !important;}}';
        document.head.appendChild(style);
    });
}

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
            // to true), lay the content out in pages, then print
            jQuery(document).on('avpvh_prwb_doc_conversion_ready', function(){
                avpvh_prwb_paginate()
                    .catch(function (error) {
                        // Still let people print, just without the page layout.
                        console.error('Paged.js could not lay out the pages', error);
                    })
                    .then(function () {
                        avpvh_prwb_stop_doing_button(jqelement);
                        window.print();
                    });
            });
            jQuery('.pmb-pro-only').remove();
            jQuery(document).trigger('avpvh_prwb_doc_conversion_requested');
            // trigger document.avpvh_prwb_wrap_up for legacy code.
            jQuery(document).trigger('avpvh_prwb_wrap_up');
            avpvh_prwb_pro_page_rendered = true;
        }
    });
});
