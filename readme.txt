=== AV-PvH Print Website ===
Contributors: grmt
Tags: print, PDF, eBook, Word, print button
Requires at least: 7.1
Tested up to: 7.1
Requires PHP: 8.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 4.0.1

Print WordPress content, or turn it into PDF, ePub and Word documents. For you and your visitors.

== Description ==

AV-PvH Print Website makes WordPress content usable outside the website: on paper, as PDF, as ePub and as Word document. It is based on Print My Blog by Michael Nelson (GPL), with the licensing, paid services and links to printmy.blog removed. Everything runs on your own site and in your browser; no content is sent to an outside service.

It has three parts:

1. **Quick Print**: print many posts or pages at once, in a few clicks.
2. **Print Buttons**: let visitors print or save individual posts and pages.
3. **Saved Prints**: set up a document once (which content, in which order, with which design and format) and generate it again whenever the content changes.

All three remove the site header, menus, sidebars and footer, and avoid page breaks inside images, between an image and its caption, and right after a heading.

= Quick Print =

* loads the chosen posts onto one page, to print from the browser (paper or "Save as PDF"), or to save as ePub or Word
* filter by post type, author, date, status, category and tag
* optionally includes comments, excerpts and featured images; each post can start on a new page
* resizes text and images, or leaves images out
* replaces embedded videos with a link, title, screenshot and QR code
* optionally removes hyperlinks
* uses the theme's and plugins' styles

= Print Buttons =

* show print, PDF, ePub and Word buttons on all posts and/or pages, or place them with the shortcode `[avpvh_print_website_print_buttons]`
* get just the URL of the print page with `[avpvh_print_website_print_page_url]`
* choose the button text and the print settings per format

= Saved Prints =

* saved prints with a title page and automatic table of contents
* digital PDF, print-ready PDF, ePub and Word document
* pick posts, pages, custom post types and "Print Materials" (posts only used in saved prints); search and filter them
* drag-and-drop ordering, parts, front matter and back matter
* designs (like themes, but for print), each with its own settings and custom CSS, reusable across saved prints
* duplicate saved prints, and copy posts to Print Materials to adjust them for print
* choose image quality and size, fonts, page sizes and article templates
* optionally apply the active theme's CSS

PDFs are made by the browser: on the print page, use "Print with Browser" and choose "Save as PDF". Before printing, [Paged.js](https://pagedjs.org) (MIT, included) lays the saved print out in pages using the designs' CSS, so page numbers, running headers, table of contents page numbers, "see page X" references and footnotes work, as they did with the original plugin's paid Prince service. Not supported: the Haller design's repeating headers and side notes, and moving images to the top or bottom of a page.

= Translations =

Includes German, French, Spanish, Italian, Japanese and Chinese translations, inherited from Print My Blog.

== Installation ==

> ⚠️ Back up your database before activating the plugin.

1. Upload the plugin to `/wp-content/plugins/avpvh-print-website`.
1. Activate it on the Plugins screen. If Print My Blog was installed, its data is moved over and Print My Blog is deactivated.

**Quick Print**

1. Under "AV-PvH Print Website", click "Quick Print".
1. Choose the format and click "Prepare Print-Page".
1. When the "Print" button appears, click it.

**Print buttons on posts and/or pages**

1. Under "AV-PvH Print Website", click "Settings".
1. Next to "Show print buttons on:", check "Posts" and/or "Pages".
1. Click "Save Settings".

**Saved Prints**

1. Under "AV-PvH Print Website", click "Saved Prints".
1. Add a saved print and follow the steps: setup, design, content, metadata, generate.

**Print form for visitors**

1. Add the "AV-PvH Print Website" block to a page.
1. Publish the page.

== Frequently Asked Questions ==

= How do I create a PDF using Quick Print? =

Quick Print puts the content onto a single web page so your browser can print it or create a PDF from it. Click "Print" and set the destination to "Save as PDF" (Chrome, Edge, Safari) or choose a PDF printer (Firefox).

Note: Images may be missing from very large PDFs. See the FAQ "My PDFs are missing images" below for help.

= How do I remove the headers and footers from printouts? =
In the browser's print dialog, open "More settings" and uncheck "Headers and footers" (Chrome, Edge), or set the header and footer fields to blank (Firefox).

= How do I create an eBook using AV-PvH Print Website? =
Choose the ePub format in Quick Print, in the print buttons, or in a saved print. The file is made in your browser.

Once the eBook has been generated, you can read it from your computer (here's instructions on how to [read ePubs](https://www.wikihow.com/Open-EPUB-Files) and [MOBI files](https://www.howtogeek.com/360419/what-is-a-mobi-file/)), or a phone or tablet.

= Some images aren't appearing =

AV-PvH Print Website can work too fast for some webservers, meaning the webservers refuse to load all the content, most noticeably some images.
In order to fix this, you need to tell AV-PvH Print Website to slow down. On the configuration page, show options, then scroll
down to show Troubleshooting options, and increase "Post Rendering Wait-Time" (e.g. to 2000 milliseconds, or whatever
value you need to in order to get all the images to load).

= My PDFs are missing images =
If your print-page has hundreds of images, browsers may generate PDFs that are missing images at the end.
To fix this, just retry "Generate the PDF". Each time the browser will fetch more images. Continue re-generating the PDF until the images at the end are included.

= The Print Page says "There seems to be an error initializing...", or is stuck on "Loading Content", or I can't filter by categories or terms from the print setup page=
*This only affects Quick Print and the print buttons; Saved Prints do not use the REST API for this.*

A plugin has probably disabled the WP REST API, which AV-PvH Print Website uses for loading data.

This is often done by a security plugin.

* Don't use "Disable REST API" plugins
* "All in One WP Security" has a setting called "Disallow Unuahtorized REST API requests". Disable it.
* Wordfence has a setting on their "All Options" page, under "Brute Force Proection", called "Prevent discovery of usernames through ‘/?author=N’ scans, the oEmbed API, and the WordPress REST API". [Disable it.](https://drive.google.com/file/d/1K3-2BjNSWQBUi1uPxq_l2gCj4xXZ44MD/view)
* iThemes security has a setting under "WordPress Tweaks" called "REST API". [Switch it to "Default Access"](https://drive.google.com/file/d/1jvujNmQuMMUDWls1LNndHM8pmpP53Ktt/view) if you want to let site visitors print your blog.


= How do I remove post content from the printout? =

There may be text, images, and other stuff that looks great when viewed from a screen, but doesn't make sense to have in the printout. E.g. related posts, share buttons, etc.
To remove them from printouts, [add the CSS class](https://technicalsupport.blog/2019/01/04/how-to-add-a-custom-css-class-to-gutenberg-blocks/) "pmb-screen-only".
Alternatively, you can use the shortcodes `[avpvh_print_website_web_only_text]...text, text, text...[/avpvh_print_website_web_only_text]` and `[avpvh_print_website_web_only_blocks]...paragraphs, images, other blocks...[/avpvh_print_website_web_only_blocks]`.

If you're handy at writing CSS, add some additional CSS to your theme that uses the "pmb-print-page" class which is added to the `body` tag of the print page.

For example, if you want to hide all divs with CSS class "my-video" in printouts, but not on the regular website, add the following CSS to your theme:

`
.pmb-print-page div.my-video{
    display:none;
}
`

Also, if it's content automatically added by a plugin or theme, please let us know in the support forum, and we can hide that content in the next release of AV-PvH Print Website.

= How do I make post content only appear in the printout? =
Use the shortcodes `[avpvh_print_website_print_only_text]...text, text, text...[/avpvh_print_website_print_only_text]` and `[avpvh_print_website_print_only_blocks]...paragraphs, images, other blocks...[/avpvh_print_website_print_only_blocks]`.

[Alternatively, add the CSS class](https://technicalsupport.blog/2019/01/04/how-to-add-a-custom-css-class-to-gutenberg-blocks/) "pmb-print-only". See the above question for more details.

= How do I show or hide background colors in the printout? =
* In Saved Prints, the background colors are dictated by your saved print's design, which might be easily customizable. Go to your saved print's "Customize Design" step and see what settings are available. You can also switch to a different design on the "Choose Design" step, or add some custom CSS to your design (`body{background-color:black;}`).*

If you're using Quick Print, the background colors are controlled by your WordPress theme and the browser.

Your theme may use different styles when printed, including setting the background to white in order to conserve ink.

But if not, your browser can also remove background colors.

* For Google Chrome: when printing, [click on "More Options", then uncheck "Background graphics"](https://drive.google.com/file/d/1V5c9XJbBGhSeRlmGxqZBRv5n4CTWZyag/view?usp=sharing)

* For Firefox: when viewing the print preview, [click "Page Setup" then uncheck "Print Background (colors & images)".](https://drive.google.com/open?id=1VoVCC6ehqd8vZyO89PQq5hZbQ4u94vBK)

* For Firefox Extension "Print to PDF": [click on "Add-ons", then click "Print to PDF", then "Options", and uncheck "Whether the background colors should be shown"](https://drive.google.com/open?id=1WjCS84LLOL6v0ccjASjJ3c_vaUmgIfTO)

= How do I force a page break before something? (So it's always at the top of a page) =
[Add the CSS class](https://technicalsupport.blog/2019/01/04/how-to-add-a-custom-css-class-to-gutenberg-blocks/) "pmb-page-break" onto the the element/block you want to be at the top of the page.

= How do I avoid a pagebreak inside something? =
[Add the CSS class](https://technicalsupport.blog/2019/01/04/how-to-add-a-custom-css-class-to-gutenberg-blocks/) "pmb-no-break" onto the element/block. The browser will avoid a pagebreak in that element if possible.
If browsers ignore that, you force the pagebreak to happen just before an element by adding the CSS class "pmb-page-break".

= How do I print a draft? =
The easiest way to print a draft post is to enable the front-end print buttons, then view the preview of the post, and click the print button you want.

= How do I show the print buttons on posts or pages? =
**Using AV-PvH Print Website Settings and Custom Fields**
You can add print buttons to all posts and/or pages from the WordPress admin dashboard, under "AV-PvH Print Website", click "Settings", then, next to "Show print buttons on:" select the posts and/or pages.
If you don't want to show them on certain posts or pages, add the custom field named "avpvh_prwb_buttons" to them with the value "hide".
Alternatively, if you only want to show the button on specific posts or pages, add the custom field "avpvh_prwb_buttons" to those posts or pages with the value "show".

**Using a Shortcode in the Post Body**
You can add print buttons directly into your posts and pages by using the shortcode `[avpvh_print_website_print_buttons]`. You can also supply it with the ID of a different post or page like so: `[avpvh_print_website_print_buttons ID=123]`.
You can alternatively use the shortcode `[avpvh_print_website_print_page_url]` to just get the URL of the print page for more advanced integrations. You can supply it with the ID of a different post, and the `format` (defaults to "print", but also accepts "pdf" or "ebook"), and whether to `add_protocol` (i.e. by default, the URL begins with "http://" or "https://", but setting this to `0` will prevent that, in case it was already added), like so: `[avpvh_print_website_print_page_url format=pdf add_protocol=0]` will produce the URL of the print page for a PDF but not add the "https://" onto the start of the URL.

**In a Widget (e.g. sidebar or footer) using a Shortcode**
Alternatively, you can add the print buttons to a widget. In the WordPress admin dashboard, under the "Appearance" menu item, click "Widgets". Add a shortcode widget and add `[avpvh_print_website_print_buttons]` like mentioned previously. The print buttons will only appear on single posts (eg not on an archive page).

**Editing Your Theme's Template Files**
If you'd like to add the print buttons somewhere in your theme's template files, add the following PHP code anywhere inside the loop:

`
<?php echo do_shortcode('[avpvh_print_website_print_buttons]'); ?>
`
or
`
<a href="<?php echo esc_url(do_shortcode('[avpvh_print_website_print_page_url]')); ?>">View Print-Ready Version of Page</a>
`

= How do I change the color of the print buttons? =
The print button's color is determined by your theme. But if you can't change the button colors using your theme's settings, you can change it with some custom CSS.
E.g. this here's how to change the button's text color to black and background color to white:

**For Block-Based Themes**
1. Go to wp-admin on your website
2. On the left, under "Appreance", click "Editor"
3. On the left, click "Styles"
4. Click the pencil icon that appears ("Edit Styles")
5. On the top-right, beside "Styles", click the three dots
6. Select "Additional CSS"
7. Add the following code:

**For Legacy Themes:**
1. Go to wp-admin on your website
2. On the left, under "Appearance", click "Customize"
3. On the left, under Additional CSS" add the the following code:

`
div.pmb-print-this-page a{
    color:black;
    background-color:white;
}
`

= How do I customize the printouts made with the Print Buttons? =
From the AV-PvH Print Website Settings page, click "Customize Buttons". From here you can customize:
* whether print buttons appear by default above or below the post content
* which print buttons appear (print, PDF, and eBook)
* the text used on each button (translate them, use an emoji, etc.)

Under each format, you can also click "Show Options". These options affect the printout produced by the button. These
options are the same as when printing out multiple posts simultaneously: you can remove the printout metadata, the post's featured image, etc.

== Screenshots ==

1. AV-PvH Print Website removes junk that doesn’t belong in a printout—like ink-guzzling logos, menus, search-bars, sidebars, footer widgets, etc.
2. Quick Print Setup page. Start printing right away, or customize which posts to print and how to print them.
3. Print Button settings page. Select whether to show on posts or pages and save. Or customize the appearance of the buttons and printout.
4. Print Buttons on a post. Notice how much clutter is removed from the printout!
5. Saved print organizer. Search for content, drag-and-drop it into your saved print, organize it into front matter, back matter, and parts.
6. Print-ready PDF table of contents. Notice the page references and how the content is grouped into 3 parts.
7. Print-ready PDF page spread. Notice the running head of the article title (yellow), the graphs generated via a plugin using Javascript (green), the external hyperlinks converted to footnotes (blue), and the page numbers are on the outside of each page (pink).
8. Digital PDF using Buurma Whitepaper design. The design adds a background color with a gradient, and a custom logo behind the page number. Designers can use the full power of HTML and CSS in their PDF designs!

== Changelog ==
= 4.0.1 November 11, 2026 =
* "Pro Print" and "projects" are now called "Saved Prints": a document set up once and generated again whenever the content changes. Quick Print keeps its name; "Pro Print ―" is gone from the Designs page titles.
* The "Powered by" setting and the print button settings no longer mention the paid service.

= 4.0.0 November 10, 2026 =
* Removed the paid printmy.blog PDF service, the DocRaptor test PDFs, licence credits, the Help page (which mailed the upstream author), the review prompts and the sales notices. PDFs are made by the browser.
* PDF print pages are laid out with Paged.js 0.4.3 before printing: page numbers, running headers, table of contents page numbers, page references and footnotes now work when saving as PDF from the browser.
* All former Pro features are available: duplicating projects and copying posts to Print Materials.
* Removed the links to printmy.blog and to Print My Blog on wordpress.org.
* Renamed from Print My Blog to AV-PvH Print Website: plugin folder and main file `avpvh-print-website/avpvh-print-website.php`, text domain `avpvh-print-website`, PHP namespace `Avpvh\PrintWebsite`, prefix `avpvh_prwb_` / `AVPVH_PRWB_`.
* Shortcodes are now `[avpvh_print_website_…]` (for example `[avpvh_print_website_print_buttons]`); the old `[pmb_…]` names keep working.
* On first load the plugin moves the Print My Blog data over (settings, projects, designs, tables, capabilities, shortcodes in posts and widgets) and deactivates Print My Blog.

= 3.27.18 October 12, 2026 =
* Updated Freemius SDK to the latest version.

= 3.27.17 April 13, 2026 =
* Test with WordPress 7.0. 
* Enhancement: Have render_toc_with_thumbnails fallback to first article image if no featured image is present.

= 3.27.16 April 7, 2026 =
* Bugfix: Add CSS rules to help prevent images from breaking across pages in the Classic ePud Design. 

= 3.27.15 April 4, 2026 =
* Bugfix: Improved CSS rules for rendering images in the ePub design. 
* Enhancement: Added a Design Development helper function for adding featured image thumbnails to TOC.

= 3.27.14 March 19, 2026 =
* Enhancement: Updated Freemius to version 2.13.0.

= 3.27.13 February 23, 2026 =
* Bugfix: Fixed an Uncaught TypeError that might occur in avpvh_prwb_set_image_dimension_attributes for some users. 

= 3.27.12 February 9, 2026 =
* Bugfix: Several minor bug fixes and code improvements. 

= 3.27.11 August 4, 2025 =
* Documentation: Added DB backup warning message to Installation Instructions.
* Documentation: Added compatibility note related to Divi Theme.
* Enhancement: Changed Print Option help text wording.

= 3.27.10 August 4, 2025 =
* Enhancement: Updated Freemius to version 2.12.1.
* Bugfix: [Hide Haller Tabloid double header from page preview](https://github.com/mnelson4/printmyblog/issues/395)
* Bugfix: [Added a refresh button on Print Page to prevent rendering issue caused by selecting Print with Browser and Download PDF consecutively.](https://github.com/mnelson4/printmyblog/issues/408)

= 3.27.9 June 18, 2025 =
* Feature: for Print Buttons, optionally open print page in new tab

= 3.27.8 May 30 2025 =
* Enhancement: for Pro Print, added a filter for the project's print page

= 3.27.7 April 24, 2025 =
* Compatibility: hide related posts from Related Post by PickPlugins

= 3.27.6 Jan 27, 2024 =
* Bugfix: for all datepickers, allow choosing dates older than 10 years
* Enhancement: update Freeimus SDK to 2.11.0
* Bugfix: for Pro Print, fix error where DesignRegistry::updateDesign not usable by add-ons

= 3.27.5 December 18, 2024 =
* Feature: for Pro Print, introduce "pmb-wide" CSS class, which is just like "mayer-wide" but applies to other designs too.
* Enhancement: for Pro Print, set a larger maximum size for images before resizing them
* Bugfix: for Pro Print, avoid error on Edit Content step by checking for negative index
* Compatibility: avoid overwriting jQuery UI styles

= 3.27.4 October 19, 2024 =
* Compatibility: for Pro Print, fix Javascript error sometimes occuring with Hustle plugin

= 3.27.3 July 31, 2024 =
* Bugfix, for Pro Print, also use AVPVH_PRWB_DEBUG on "avpvh_prwb_ajax" requests (which are what's actually used currently to create print pages)

= 3.27.2 July 19, 2024 =
* Compatibility: for Pro Print with WPML, fix bug that prevented generating in a secondary language
* Enhancement: for Pro Print, introduce constant AVPVH_PRWB_DEBUG to control whether to display warnings when generating a project. Changed default to *not* show warnings.


= 3.27.1 June 21, 2024 =
* Bugfix: for Pro Print, fix warning when a project contains a deleted post
* Bugfix: for Quick Print, escape all post content before rendering to page

= 3.27.0 May 16, 2024 =
* Feature: significant German translations contributed by Torben Bertram of https://archiv-wintermoor.de/
* Feature: for Pro Print Haller Design, allow using a featured image for the header
* Feature: for Pro Print Haller and Mayer Designs, add section template "Full Column Content" which omits article header info but leave the content in columns
* Bugfix: for Quick print, fix an error that prevented the "print" button from being enabled when there was an error processing certain videos.
* Bugfix: for Pro Print Haller Design when printing from the browser, remove duplicate header; all elements with class "pmb-pro-only" get removed when printing using the browser
* Bugfix: for Pro Print, allow changing a design setting to blank (eg the Haller Design's subtitle)
* Enhancement: to facilitate styling articles and hiding certain elements, add CSS classes "pmb-post-id", "pmb-post-author", "pmb-post-author", "pmb-post-date", "pmb-post-categories", "pmb-post-url-wrapper".

= 3.26.3 January 29, 2024 =
* Enhancement: update select2 Javascript library to 4.0.13 to improve compatibility with other plugins that also use it
* Enhancement: add Patchstack Vulnerability Disclosure Program to readme to encourage responsible disclosure of security vulnerabilities

= 3.26.2 January 11, 2024 =
* Enhancement: updated logo
* Bugfix: in Quick Print, fix rendering local videos with the source as a sub-element
* Bugfix: fix regression where some of 3.26.0 changes were lost in 3.26.1 (only released on Freemius)

= 3.26.1 December 20, 2023 =
* Bugfix: in Pro Print, fix QR codes being added onto videos
* Enhancement: mention that Pro Print's Word Documents don't work with Apple Pages


= 3.26.0 November 21, 2023 =
* Enhancement: remove AV-PvH Print Website dashboard news as I was just told this is in violation of WordPress.org plugin guidelines.
* Bugfix: fix admin print buttons not including some HTML which could affect the design's layout
* Bugfix: fix image quality adjusting so the CSS class `pmb-dont-change-image-quality` works as intended
* Developer: simplify adding custom fonts to designs (introduces Avpvh\PrintWebsite\domain\DefaultDesignTemplates::getPdfFontSettings() and adds custom font CSS if those settings are used)
* Enhancement: avoid pagebreaks between images and their captions
* Feature: in Pro Print, introduce CSS class "pmb-snap" to snap a block or image to nearest page edge

= 3.25.5 August 31, 2023 =
* Bugfix: in Pro Print, fix missing dates when posts were on the same date
* Bugfix: fix missing PMB icon on WP plugins update page

= 3.25.4 August 31, 2023 =
* Bugfix: on the designs page, fixed bug that only showed first 10 designs
* Feature: added GTranslate plugin compatibility (see https://wordpress.org/plugins/gtranslate/)
* Enhancement: removed unnecessary jQuery UI Javascript file

= 3.25.3 July 17, 2023 =
* Feature: in Pro Print PDFs, use [DocRaptor's Pipeline 10.1](https://docraptor.com/documentation/article/7898168-2022-07-27-pipeline-10-and-10-1-with-prince-15-and-15-1) which adds support for WebP and AVIF images and other features

= 3.25.2 July 5, 2023 =
* Enhancement: improve stability of Freemius license/subscription features by updating Freemius SDK to 2.5.10

= 3.25.1 June 23, 2023 =
* Bugfix: in Pro Print for Word, make nested items in the Word TOC appear indented by adjusting their heading levels
* Translation: props to @ryanvdk for improving the Japanese translations
* Miscellaneous: added @szepeviktor as a contributor (contributed to 2.1.4, so this is old)

= 3.25.0 June 16, 2023 =
* Feature: in Pro Print Word Documents, add setting to specify maximum image height
* Enhancement: make post row actions to generate PMB files smaller (eg instead of "Generate Word Document" it just says "Word Document") to save space

= 3.24.4 June 6, 2023 =
* Bugfix: in Pro Print for Word, avoid warning in header and footers when using default settings
* Documentation: point users of the Haller Tabloid to the CSS class "pmb-dont-snap" to avoid image snapping
* Compatibility: in Pro Print, prevent [AdvancedExcerpt](https://wordpress.org/plugins/advanced-excerpt/) from filtering content on the print page

= 3.24.3 May 29, 2023 =
* Bugfix: in Admin Print Buttons, respect the design's "Apply Website Theme" setting
* Bugfix: in Admin Print Buttons, add a unique code onto print page URLs

= 3.24.2 May 29, 2023 =
* Feature: show Admin Print Buttons to Free users too (again, they can be removed from AV-PvH Print Website > Settings)

= 3.24.1 May 29, 2023 =
* Feature: display Admin Print Buttons by default (although they can still be removed from AV-PvH Print Website > Settings)

= 3.24.0 May 27 2023 =
* Feature: add Admin Print Buttons, to quickly create PDF, eBooks and Word documents from individual posts, pages, and other post types. Go to AV-PvH Print Website > Settings to enable.
* Feature: add Designs page, where you can set the default design for each project and customize any design (even ones not in use on any projects currently.)

= 3.23.5 May 16, 2023 =
* Bugfix: fix creating database tables when the database has no charset specified
* Feature: in Pro Print Mayer Magazine, add "Post Contents" option so you can include post ID, author, date, categories and tags, URL, excerpt, and custom fields; allow removing title, featured image and content

= 3.23.4 May 11, 2023 =
* Compatibility: in Pro Print with Elementor, fix extra columns appearing in projects when using nested columns

= 3.23.3 May 9, 2023 =
* Enhancement: in Pro Print, note that Mayer Magazine's pagebreaks work best with Pro PDF Service; not so great with browsers
* Bugfix: in Pro Print's Mayer Magazine avoid warning when Cover Preamble is blank

= 3.23.2 April 5, 2023  =
* Bugfix: in Pro Print, avoid losing styles if there are warnings on the print page
* Bugfix: in Pro Print with Pro PDF Service, prevent "Filesystem access not allowed" error because of relative URLs
* Bugfix: in Pro Print content editing step, prevent exceeding maximum nesting level by dragging

= 3.23.1 March 10, 2023 =
* Bugfix: in Pro Print, avoid post being reset by setting up WP_Query properly

= 3.23.0 March 9, 2023 =
* Feature: in Pro Print Word Documents, add support for headers and footers to documents
* Bugfix: in Pro Print, prevent 403 error for some webservers because we used spaces and colons in the URL

= 3.22.0 February 20, 2023 =
* Feature: in Pro Print PDFs and ePubs, add design options to set custom fonts
* Feature: allow uploading font files to website
* Feature: add Chinese translations
* Feature: in Pro Print Classic Digital PDF and Classic Print-Ready PDF designs, add a "Show a Dividing Line Between Posts" setting
* Bugfix: in Pro Print PDFs, apply the chosen font to content in margins (not just the main body)
* Bugfix: in Pro Print Classic Digital PDF and Classic Print-Ready PDF designs, respect the "Each Post Begins on a New Page" setting
* Bugfix: in Pro Print Word, enqueue PMB's common print page CSS

= 3.21.5 February 13, 2023 =
* Enhancement: in Pro Print's Edit Content step, give a warning if project is empty
* Bugfix: in Pro Print, when printing from browser, bring back the TOC (it disappeared in 3.21.0 when printing from the browser)

= 3.21.4 February 8, 2023 =
* Bugfix: in Pro Print, add support for relative hyperlinks, hyperlinks with wrong protocol, hyperlinks with an extra "www", and support for anchor links
* Bugfix: in Pro Print ePubs, make anchor links work for Kindle Previewer too
* Bufix: in Pro Print Word docs, fix regression where file wouldn't download

= 3.21.3 February 3, 2023 =
* Bugfix: in Pro Print, remove warning for designs that don't use Page Reference Text (e.g., Mayer Magazine)

= 3.21.2 January 30, 2023 =
* Compatibility: hide WP Socializer sharing bar
* Compatibility: hide Post Like Dislike button

= 3.21.1 January 27, 2023 =
* Compatibility: in Pro Print with Elementor, handle lazy-loaded YouTube and Vimeo videos
* Bugfix: in Pro Print, fix downloading ePubs even when there is an error downloading an image

= 3.21.0 January 26, 2023 =
* Compatibility: in Pro Print, improve integration with many other plugins by doing most print-page processing when the download button is pressed (which gives other plugins more time to do all their processing)

= 3.20.6 January 17, 2023 =
* Bugfix: in Pro Print ePubs, tidy up galleries
* Compatibility: show Elementor items even if they're animated
* Bugfix: in Pro Print ePubs, support CSS classes to prevent pagebreaks

= 3.20.5 January 10, 2023 =
* Bugfix: in Pro Print ePubs, keep video text with video screenshot
* Compatibility: in Pro Print with Elementor, handle lazy-loaded local videos
* Compatibility: in Pro Print with Elementor, centre images by default
* Compatibility: in Pro Print with Elementor, fix galleries in Calibre eReader

= 3.20.4 January 5, 2023 =
* Bugfix: in Pro Print, fix protocols in URLs in inline styles added after pageload (again from Advances Ads plugin)
* Bugfix: in Pro Print, when creating Word docs, handle errors when images dont exist
* Bugfix: in Pro Print, prevent some Javascript from running out of order
* Compatibility: hide One Click Accessibility Toolbar on Print Page

= 3.17.0 September 28, 2022 =
* Feature: build Print Page on front end requests insead of admin AJAX requests so more plugins' JS and CSS load
* Compatibility: make Supsystic tables render in projects
* Compatibility: with WPML, prevent projects from hiding when selecting a different language

= 3.16.5 September 5, 2022 =
* Bugfix: show all projects when there are more than 10

= 3.16.4 August 19, 2022 =
* Compatibility: don't cut off Elementor/WooCommerce items at the bottom of the page of PDFs
* Compatibility: avoid leading empty page of PDFs when using WooCommerce

= 3.16.3 August 16, 2022 =
* Bugfix: prevent warning from JetPack infinite scroll during generation by using $wp_the_query instead of $wp_query

= 3.16.2 August 3, 2022 =
* Bugfix: avoid lazy loading featured images in Pro Print
