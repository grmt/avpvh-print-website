<h2><?php esc_html_e('Purpose', 'avpvh-print-website'); ?></h2>
<p>
    <?php esc_html_e('Multi-column newspaper design designed for printing. Wide images can span multiple columns and always snap to the nearest page edge to make a visually-appealing layout.', 'avpvh-print-website'); ?>
</p>

<h2><?php esc_html_e('Features', 'avpvh-print-website'); ?></h2>
<ul class="pmb-list">
    <li><?php esc_html_e('The "Title page" is actually a special header on the front page containing your publication\'s title, issue number, date, and other optional prominent info (set on the design and saved print meta settings).', 'avpvh-print-website'); ?></li>
    <li><?php esc_html_e('Content is divided into two, three, or four columns.', 'avpvh-print-website'); ?><?php avpvh_prwb_pro_print_service_best_e(__('Some browsers don‘t support multiple columns', 'avpvh-print-website')); ?></li>
    <li><?php esc_html_e('The top margin contains the front page (publication title, issue number, date, etc.).', 'avpvh-print-website');?> <?php avpvh_prwb_pro_print_service_best_e(__('Most browsers add their own content to the page margins', 'avpvh-print-website')); ?></li>
    <li><?php esc_html_e('Designed for duplex printing with the left page mirroring the right page\'s margins.', 'avpvh-print-website');?></li>
    <li><?php esc_html_e('Content can be divided into parts. Part openings and important articles\' titles and cover images take up the full page width, whereas regular articles compactly fit into columns.', 'avpvh-print-website'); ?></li>

    <li><?php
        esc_html_e(
            'Each article may include post title, ID, author, published date, categories and tags, URL, featured image, excerpt, custom fields, and content.',
            'avpvh-print-website'
        );
        ?></li>
    <li><?php esc_html_e('If your content is already divided into columns, optionally automatically remove them to improve the layout.', 'avpvh-print-website'); ?></li>
    <li><?php esc_html_e('External hyperlinks (links to web pages not in the saved print) can be automatically converted to footnotes or removed', 'avpvh-print-website'); ?><?php avpvh_prwb_pro_print_service_best_e(__('Footnotes require Pro', 'avpvh-print-website')); ?></li>
    <li><?php esc_html_e('Internal hyperlinks (links to content included in the saved print) can be automatically converted to footnotes, inline page references, or removed', 'avpvh-print-website'); ?><?php avpvh_prwb_pro_print_service_best_e(__('Footnotes and page references require Pro PDF Service', 'avpvh-print-website')); ?></li>

</ul>
<h2><?php esc_html_e('Page Layout', 'avpvh-print-website'); ?></h2>
<ul class="pmb-list">
    <li><?php esc_html_e('Issue and page number are in the outside top margin, the publication\'s title is in the middle top margin, and the publication date is in the inside top margin. The optional publication subtitle is underneath the other top-margin content.', 'avpvh-print-website'); ?><?php avpvh_prwb_pro_print_service_best_e(__('Most browsers add their own content to the page margins', 'avpvh-print-website')); ?></li>
</ul>
<h3><?php esc_html_e('Section Templates', 'avpvh-print-website');?></h3>
<p><?php esc_html_e('Choose different styles for articles included in your saved print:', 'avpvh-print-website'); ?></p>
<ul class="pmb-list">
    <li><?php esc_html_e('Default Template: shows all content specified by the "Post Content" design setting (e.g. the article\'s title and featured image) in columns.', 'avpvh-print-website');?></li>
    <li><?php esc_html_e('Single Column: like the default template, but in a single column.', 'avpvh-print-website');?></li>
    <li><?php esc_html_e('Fullpage Content: contains only the article\'s content (no title, featured image, etc.) all in a single, wide column.', 'avpvh-print-website');?></li>
    <li><?php esc_html_e('Full Column Content: like "Fullpage Content", except content is still divided into multiple columns..', 'avpvh-print-website');?></li>
    <li><?php esc_html_e('Important: shows the article\'s featured image and title across all columns, but other content is shown in multiple columns.', 'avpvh-print-website');?></li>
</ul>
<p><?php esc_html_e('Parts start on a new page and include the part’s title, featured image, and content.', 'avpvh-print-website'); ?></p>
<h2><?php esc_html_e('Special Instructions', 'avpvh-print-website'); ?></h2>
<p><?php esc_html_e('Note that in order to improve page layout, all images and figures snap to the nearest page edge.', 'avpvh-print-website'); ?></p>
<p><?php esc_html_e('"Full width" images take up all columns and snap to the nearest page edge.','avpvh-print-website'); ?></p>
<p><?php esc_html_e('"Wide width" images take up 2 columns and snap to the nearest page edge.','avpvh-print-website'); ?></p>
<p><?php
    printf(
        // translators: %s: CSS class name
        esc_html__('Add the CSS class "%s" to any images you don\'t want to snap like this.', 'avpvh-print-website'),
        'pmb-dont-snap'
    );
?></p>