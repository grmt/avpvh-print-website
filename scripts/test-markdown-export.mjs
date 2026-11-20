// Browser regression checks with fixture content; no WordPress account or network needed.
// JQUERY_PATH can point to WordPress's wp-includes/js/jquery/jquery.min.js.
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { createRequire } from 'node:module';
import { fileURLToPath } from 'node:url';
import { chromium } from 'playwright';

const root = fileURLToPath(new URL('../', import.meta.url));
const require = createRequire(import.meta.url);
const JSZip = require('../assets/scripts/libs/jszip.min.js');
const jquery = process.env.JQUERY_PATH || '/usr/share/javascript/jquery/jquery.min.js';
const browser = await chromium.launch({ channel: 'chrome', headless: true, args: ['--no-sandbox'] });
const errors = [];

async function fixture(format, overrides = {}) {
    const page = await browser.newPage({ acceptDownloads: true });
    page.on('pageerror', error => errors.push(error.message));
    await page.route('**/*', route => route.abort());
    await page.setContent(`<!doctype html><html><head><base href="https://example.test/"></head><body>
        <h1 id="pmb-in-progress-h1"></h1><span class="pmb-status"></span>
        <div class="pmb-print-ready"><button id="pmb-download-markdown" disabled>Download Markdown</button>
        <div id="pmb-markdown-status" role="status"></div></div>
        <div class="pmb-posts-placeholder"></div><div class="pmb-loading-content"></div>
        <div class="pmb-posts" style="display:none"><div class="pmb-posts-header"><h1>My Website</h1><p>Site description</p></div>
        <div class="pmb-posts-body"></div></div></body></html>`);
    await page.addScriptTag({ path: jquery });
    await page.evaluate(({ format, overrides }) => {
        window.avpvh_prwb_print_data = {
            data: {
                format, post_type: 'page', statuses: ['publish'], links: 'include', image_size: 8,
                locale: 'en-US', rendering_wait: 0, columns: 1, show_title: true, show_content: true,
                show_comments: true, header_selector: '#pmb-in-progress-h1', status_span_selector: '.pmb-status',
                posts_div_selector: '.pmb-posts-body', waiting_area_selector: '.pmb-posts-placeholder',
                print_ready_selector: '.pmb-print-ready', loading_content_selector: '.pmb-loading-content', ...overrides
            },
            i18n: { comments: 'Comments', no_comments: 'No Comments', says: ' says:', ready: 'Ready' }
        };
        window.avpvh_prwb_markdown = {
            format, filename: 'Website-2026-11-20.' + (format === 'markdown_zip' ? 'zip' : 'md'),
            i18n: { generating: 'Generating', ready: 'Download ready', error: 'Export failed', empty: 'No matching pages' }
        };
        window.wp = { api: { loadPromise: { done() {} } } };
        window.Backbone = { sync() {} };
        window.avpvh_prwb = { external_requests_enabled: true };
        window.avpvh_prwb_convert_youtube_videos_to_images = () => { throw Error('Markdown must not request video thumbnails'); };
        for (const name of ['add_header_classes', 'fix_wp_videos', 'load_avada_lazy_images', 'reveal_dynamic_content', 'resize_images']) {
            window['avpvh_prwb_' + name] = () => {};
        }
        window.avpvh_prwb_remove_hyperlinks = () => jQuery('.pmb-posts a').contents().unwrap();
    }, { format, overrides });
    for (const script of ['print-page.js', 'libs/turndown.js', 'libs/turndown-plugin-gfm.js', 'libs/jszip.min.js', 'markdown-generator.js']) {
        await page.addScriptTag({ path: root + 'assets/scripts/' + script });
    }
    assert.equal(await page.locator('#pmb-download-markdown').isDisabled(), true);
    await page.evaluate(() => {
        window.renderer = new PmbPrintPage(avpvh_prwb_print_data.data, avpvh_prwb_print_data.i18n);
        for (const name of ['header', 'status_span', 'posts_div', 'waiting_area', 'print_ready', 'loading_content']) {
            renderer[name] = jQuery(renderer[name + '_selector']);
        }
    });
    return page;
}

async function render(page, empty = false) {
    await page.evaluate(empty => {
        const posts = [
            { id: 42, slug: 'same-title', title: { rendered: 'First &amp; Café' },
                content: { protected: false, rendered: `<h2>Section heading</h2><p>A <strong>bold</strong> paragraph with
                    <a href="../other/?a=1&amp;b=2">relative link</a> and <code>x*y</code>.</p>
                    <blockquote><p>A quotation</p></blockquote><ul><li>List item<ul><li>Nested item</li></ul></li></ul>
                    <pre><code class="language-js">const x = 1;\nconsole.log(x);</code></pre>
                    <table>\n<thead>\n<tr>\n<th>Name</th>\n<th>Value</th>\n</tr>\n</thead>
                    <tbody><tr><td>Café | Tea</td><td>€ 2</td></tr></tbody></table><table></table>
                    <ul><li><input type="checkbox" checked>Complete</li></ul>
                    <p><img src="/media/picture.jpg" alt="A photo"></p>
                    <img data-src="data:image/svg+xml,unsafe-image" alt="Untrusted image">
                    <iframe src="https://video.test/embed/123" title="Watch video"></iframe>
                    <span hidden>hidden-secret</span><span style="display:none">css-secret</span>
                    <script type="application/json">script-secret</script><a href="javascript:void(0)">unsafe link</a>` },
                comments: [{ id: 1, type: 'comment', date: '2026-01-01T12:00:00', author_name: 'Reader', content: { rendered: '<p>First page comment</p>' } }] },
            { id: 43, slug: 'same-title', title: { rendered: 'Second page' },
                content: { protected: false, rendered: '<p>Second page body.</p>' },
                comments: [{ id: 2, type: 'comment', date: '2026-01-01T12:00:00', author_name: 'Reader', content: { rendered: '<p>Second page comment</p>' } }] }
        ];
        if (!empty) {
            posts.forEach(post => renderer.addPostToPage({ ...post, status: 'publish', link: 'https://example.test/pages/' + post.id + '/', excerpt: { rendered: '' } }));
        }
        renderer.total_posts = empty ? 0 : posts.length;
        renderer.finish();
    }, empty);
    await page.waitForFunction(() => !document.getElementById('pmb-download-markdown').disabled);
}

async function getDownload(page) {
    const pending = page.waitForEvent('download');
    await page.locator('#pmb-download-markdown').click();
    const download = await pending;
    const buffer = await readFile(await download.path());
    await page.waitForFunction(() => !document.getElementById('pmb-download-markdown').disabled);
    assert.equal(await page.locator('#pmb-markdown-status').textContent(), 'Download ready');
    return { buffer, name: download.suggestedFilename() };
}

try {
    const single = await fixture('markdown');
    await render(single);
    const preview = await single.locator('.pmb-posts').innerHTML();
    const first = await getDownload(single);
    assert.equal(first.name, 'Website-2026-11-20.md');
    const text = first.buffer.toString('utf8');
    for (const expected of ['# My Website', '# First & Café', '## Section heading', '**bold**', '[relative link](https://example.test/pages/other/?a=1&b=2)',
        '```js\nconst x = 1;', '> A quotation', 'Nested item', '| Name | Value |', 'Café \\| Tea', '[x] Complete',
        '![A photo](https://example.test/media/picture.jpg)', '[Watch video](https://video.test/embed/123)', 'First page comment', 'Second page comment']) {
        assert.ok(text.includes(expected), `Combined export should contain ${expected}\n${text}`);
    }
    for (const unexpected of ['hidden-secret', 'css-secret', 'script-secret', 'javascript:', 'data:image', 'unsafe-image', '<script', '[First & Café]']) {
        assert.ok(!text.includes(unexpected), `Must omit ${unexpected}`);
    }
    assert.ok(text.indexOf('First & Café') < text.indexOf('Second page'));
    assert.equal((await getDownload(single)).buffer.toString('utf8'), text);
    assert.equal(await single.locator('.pmb-posts').innerHTML(), preview, 'Export must preserve the preview');
    const escapedLink = await single.evaluate(() => {
        const link = document.createElement('a');
        link.textContent = 'Link';
        link.setAttribute('href', String.raw`/path\)`);
        link.setAttribute('title', String.raw`slash\"quote`);
        return new TurndownService().turndown(link.outerHTML);
    });
    assert.ok(escapedLink.includes(String.raw`/path\\\)`), 'Link destinations must escape backslashes and parentheses');
    assert.ok(escapedLink.includes(String.raw`slash\\\"quote`), 'Link titles must escape backslashes and quotes');
    await single.close();

    const multiple = await fixture('markdown_zip');
    await render(multiple);
    const zipped = await getDownload(multiple);
    assert.equal(zipped.name, 'Website-2026-11-20.zip');
    const zip = await JSZip.loadAsync(zipped.buffer, { checkCRC32: true });
    const filenames = Object.keys(zip.files).filter(name => !zip.files[name].dir);
    assert.deepEqual(filenames, ['Website-2026-11-20/001-same-title.md', 'Website-2026-11-20/002-same-title.md']);
    const pages = await Promise.all(filenames.map(name => zip.file(name).async('string')));
    assert.ok(pages[0].includes('First page comment') && !pages[0].includes('Second page comment'));
    assert.ok(pages[1].includes('Second page comment') && !pages[1].includes('First page comment'));
    assert.ok(!pages[0].includes('My Website'), 'Per-page files should not repeat the site header');
    await multiple.close();

    const minimal = await fixture('markdown', { show_title: false, show_comments: false, image_size: 0, links: 'remove' });
    await render(minimal);
    const minimalText = (await getDownload(minimal)).buffer.toString('utf8');
    assert.ok(!minimalText.includes('First & Café') && !minimalText.includes('First page comment') && !minimalText.includes('picture.jpg'));
    assert.ok(minimalText.includes('relative link') && !minimalText.includes('[relative link]'));
    await minimal.close();

    const empty = await fixture('markdown_zip');
    await render(empty, true);
    let downloads = 0;
    empty.on('download', () => downloads++);
    await empty.locator('#pmb-download-markdown').click();
    await empty.waitForFunction(() => document.getElementById('pmb-markdown-status').textContent === 'No matching pages');
    assert.equal(downloads, 0);
    assert.equal(await empty.locator('#pmb-download-markdown').isDisabled(), false);
    await empty.close();
    assert.deepEqual(errors, []);
    console.log('Markdown browser checks passed: combined file, ZIP, content fidelity, repeat download, selected options, empty selection.');
} finally {
    await browser.close();
}
