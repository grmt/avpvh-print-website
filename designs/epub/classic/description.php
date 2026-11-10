<h2><?php esc_html_e('Purpose', 'avpvh-print-website'); ?></h2>
<p><?php esc_html_e('Similar to AV-PvH Print Website Quick Print and dotEpub, but supporting Pro features. Great for printing your WordPress content as an eBook for reading from a tablet or phone, or uploading to eBook marketplaces like Amazon or Apple Book Store.', 'avpvh-print-website'); ?></p>
<p><?php esc_html_e('A table of contents is generated and inserted into the book (no need to add it using an external service like Kindle Direct Publishing or Calibre).', 'avpvh-print-website'); ?></p>
<p><?php esc_html_e('Images are bundled into the book file so they can be used offline.', 'avpvh-print-website'); ?></p>
<p><?php esc_html_e('Image galleries are rearranged into a simple list of images which displays better on smaller devices.', 'avpvh-print-website'); ?></p>
<p><?php
    printf(
        // translators: 1: opening HTML tag, 2: closing HTML tag
        esc_html__('If there are any features or options you want, %1$splease get in touch.%2$s', 'avpvh-print-website'),
        '<a href="' . esc_url_raw(admin_url(AVPVH_PRWB_ADMIN_HELP_PAGE_PATH)) . '">',
        '</a>'
    );
    ?></p>

<h2><?php esc_html_e('Features', 'avpvh-print-website'); ?></h2>
<ul class="pmb-list">
    <li><?php esc_html_e('Supports nesting articles (like posts and pages) into “parts”', 'avpvh-print-website'); ?></li>
    <li><?php esc_html_e('External hyperlinks (links to web pages not in the project) are left as working hyperlinks', 'avpvh-print-website'); ?></li>
    <li><?php esc_html_e('Internal hyperlinks (links to content included in the project) are converted into links to the appropriate page of the eBook', 'avpvh-print-website'); ?>
    </li>
    <li><?php esc_html_e('The default title page can include: Project Title, Subtitle, Site URL, Date Printed, Credit to AV-PvH Print Website', 'avpvh-print-website'); ?>
    </li>
    <li><?php esc_html_e('Each article can include: Title, ID, Author, URL, Date Published, Categories and Tags, Featured Image, Excerpt, Custom Fields, Content', 'avpvh-print-website'); ?>
    </li>
    <li><?php esc_html_e('All images are automatically included in the ePub file (instead of merely pointing to the external image source). This makes the filesize larger, but allows users to view images while disconnected from the Internet.', 'avpvh-print-website'); ?>
    </li>
    <li><?php esc_html_e('Small images are automatically centered', 'avpvh-print-website'); ?></li>
    <li><?php esc_html_e('Image galleries are converted into regular list of images, which look better on small screens.', 'avpvh-print-website'); ?></li>
</ul>

<h2><?php esc_html_e('Page Layout', 'avpvh-print-website'); ?></h2>
<p><?php esc_html_e('The page design is mostly dictated by the app and device used for reading', 'avpvh-print-website'); ?></p>
<p><?php esc_html_e('Each article begins on a new page.', 'avpvh-print-website'); ?></p>
