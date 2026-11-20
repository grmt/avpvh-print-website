/* global TurndownService, turndownPluginGfm, JSZip, avpvh_prwb_markdown, avpvh_prwb_print_data */
(function () {
    'use strict';

    // Work on copies so downloading again never changes the preview or its content.
    function prepareCopy(element, baseUrl) {
        var copy = element.cloneNode(true);
        copy.querySelectorAll('script, style, link, noscript, template, button, svg, canvas, .pmb-extra-content, .screen-reader-text, [hidden], [aria-hidden="true"]').forEach(function (node) {
            node.remove();
        });
        copy.querySelectorAll('[style]').forEach(function (node) {
            if (node.style.display === 'none' || node.style.visibility === 'hidden') {
                node.remove();
            }
        });
        copy.querySelectorAll('table').forEach(function (table) {
            if (!table.rows.length) {
                table.remove();
            }
        });
        // The GFM table plugin expects structural elements without indentation text nodes.
        copy.querySelectorAll('table, thead, tbody, tfoot, tr').forEach(function (node) {
            Array.from(node.childNodes).forEach(function (child) {
                if (child.nodeType === Node.TEXT_NODE && !child.textContent.trim()) {
                    child.remove();
                }
            });
        });
        copy.querySelectorAll('iframe, video, audio').forEach(function (node) {
            var source = node.getAttribute('src');
            var childSource = node.querySelector('source[src]');
            if (!source && childSource) {
                source = childSource.getAttribute('src');
            }
            var link = document.createElement('a');
            link.textContent = node.getAttribute('title') || source || '';
            if (source) {
                link.setAttribute('href', source);
            }
            node.replaceWith(link);
        });
        copy.querySelectorAll('img').forEach(function (image) {
            if (Number(avpvh_prwb_print_data.data.image_size) === 0) {
                image.remove();
                return;
            }
            var source = image.getAttribute('data-src-fg') || image.getAttribute('data-src')
                || image.getAttribute('data-lazy-src') || image.getAttribute('src');
            if (source) {
                image.setAttribute('src', source);
            }
        });
        copy.querySelectorAll('a[href], img[src]').forEach(function (node) {
            var attribute = node.tagName === 'IMG' ? 'src' : 'href';
            var value = node.getAttribute(attribute);
            try {
                var url = new URL(value, baseUrl);
                if (['http:', 'https:', 'mailto:', 'tel:'].includes(url.protocol)) {
                    node.setAttribute(attribute, url.href);
                } else {
                    node.removeAttribute(attribute);
                }
            } catch (error) {
                node.removeAttribute(attribute);
            }
        });
        // Metadata spans are adjacent in the print layout. Give them readable lines in Markdown.
        copy.querySelectorAll('.pmb-post-meta').forEach(function (node) {
            var paragraph = document.createElement('p');
            while (node.firstChild) {
                paragraph.appendChild(node.firstChild);
            }
            node.replaceWith(paragraph);
        });
        return copy;
    }

    function safeName(value) {
        try {
            value = decodeURIComponent(value);
        } catch (error) {
            // A literal percent sign in a title is valid too.
        }
        return value.normalize('NFC').replace(/[\u0000-\u001f\u007f<>:"/\\|?*#%]/g, '-')
            .replace(/\s+/g, '-').replace(/-+/g, '-').replace(/^[. -]+|[. -]+$/g, '').slice(0, 80)
            .replace(/[. -]+$/g, '') || 'page';
    }

    function collectPages() {
        return Array.from(document.querySelectorAll('.pmb-posts-body > .pmb-post-article')).map(function (article) {
            var container = document.createElement('div');
            container.appendChild(article.cloneNode(true));
            // Quick Print renders comments and dividers as siblings of their post.
            var sibling = article.nextElementSibling;
            while (sibling && !sibling.classList.contains('pmb-post-article')) {
                container.appendChild(sibling.cloneNode(true));
                sibling = sibling.nextElementSibling;
            }
            var heading = article.querySelector('.pmb-title');
            return {
                name: safeName(article.getAttribute('data-pmb-slug') || (heading && heading.textContent) || article.id),
                content: prepareCopy(container, article.getAttribute('data-pmb-source-url') || document.baseURI)
            };
        });
    }

    function createConverter() {
        var converter = new TurndownService({
            headingStyle: 'atx',
            codeBlockStyle: 'fenced',
            bulletListMarker: '-',
            emDelimiter: '*'
        });
        converter.use(turndownPluginGfm.gfm);
        converter.addRule('markdownTableCell', {
            filter: ['th', 'td'],
            replacement: function (content, node) {
                return (node.cellIndex === 0 ? '| ' : ' ') + content.trim().replace(/\|/g, '\\|').replace(/\n+/g, '<br>') + ' |';
            }
        });
        converter.remove(['script', 'style', 'noscript', 'button']);
        return converter;
    }

    function download(blob, filename) {
        var url = URL.createObjectURL(blob);
        var link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        link.remove();
        // Allow the browser time to start the download before releasing the data.
        setTimeout(function () { URL.revokeObjectURL(url); }, 60000);
    }

    async function exportMarkdown() {
        var button = document.getElementById('pmb-download-markdown');
        var status = document.getElementById('pmb-markdown-status');
        button.disabled = true;
        status.textContent = avpvh_prwb_markdown.i18n.generating;
        try {
            // Let the browser paint the progress message before converting large documents.
            await new Promise(function (resolve) { setTimeout(resolve, 0); });
            var pages = collectPages();
            if (!pages.length) {
                status.textContent = avpvh_prwb_markdown.i18n.empty;
                return;
            }
            var converter = createConverter();
            var blob;
            var filename = avpvh_prwb_markdown.filename;
            if (avpvh_prwb_markdown.format === 'markdown_zip') {
                var zip = new JSZip();
                var folder = zip.folder(safeName(filename.replace(/\.zip$/i, '')));
                var digits = Math.max(3, String(pages.length).length);
                pages.forEach(function (page, index) {
                    // Numbering preserves the selected order and prevents duplicate names.
                    var name = String(index + 1).padStart(digits, '0') + '-' + page.name + '.md';
                    folder.file(name, converter.turndown(page.content) + '\n');
                });
                blob = await zip.generateAsync({ type: 'blob', compression: 'DEFLATE' });
            } else {
                var parts = [];
                var header = document.querySelector('.pmb-posts-header');
                if (header) {
                    parts.push(converter.turndown(prepareCopy(header, document.baseURI)));
                }
                pages.forEach(function (page) {
                    parts.push(converter.turndown(page.content));
                });
                blob = new Blob([parts.filter(Boolean).join('\n\n---\n\n') + '\n'], { type: 'text/markdown;charset=utf-8' });
            }
            download(blob, filename);
            status.textContent = avpvh_prwb_markdown.i18n.ready;
        } catch (error) {
            status.textContent = avpvh_prwb_markdown.i18n.error;
            console.error('AV-PvH Markdown export failed:', error);
        } finally {
            button.disabled = false;
        }
    }

    jQuery(document).one('avpvh_prwb_markdown_ready', function () {
        var button = document.getElementById('pmb-download-markdown');
        if (button) {
            button.disabled = false;
            button.addEventListener('click', exportMarkdown);
        }
    });
}());
