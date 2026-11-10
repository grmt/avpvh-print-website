<h2><?php esc_html_e('Features', 'avpvh-print-website'); ?></h2>
<ul class="pmb-list">
    <li><?php esc_html_e('Supports nesting articles (like posts and pages) into “parts”', 'avpvh-print-website'); ?></li>
    <li><?php esc_html_e('External hyperlinks (links to web pages not in the project) can be automatically converted to footnotes or removed', 'avpvh-print-website'); ?><?php avpvh_prwb_pro_print_service_best_e(__('Footnotes require Pro', 'avpvh-print-website')); ?></li>
    <li><?php esc_html_e('Internal hyperlinks (links to content included in the project) can be automatically converted to footnotes, inline page references, or removed', 'avpvh-print-website'); ?><?php avpvh_prwb_pro_print_service_best_e(__('Footnotes and page references require Pro PDF Service', 'avpvh-print-website')); ?></li>
    <li><?php esc_html_e('The default title page can include: Project Title, Subtitle, Site URL, Date Printed, Credit to AV-PvH Print Website', 'avpvh-print-website'); ?></li>
    <li><?php esc_html_e('Each article can include: Title, ID, Author, URL, Date Published, Categories and Tags, Featured Image, Excerpt, Custom Fields, Content', 'avpvh-print-website'); ?></li>
    <li><?php esc_html_e('Optionally automatically center images', 'avpvh-print-website'); ?></li>
    <li><?php esc_html_e('Optionally float images to the top or bottom of each page', 'avpvh-print-website'); ?><?php avpvh_prwb_pro_print_service_only(); ?></li>
</ul>
<h2><?php esc_html_e('Page Layout', 'avpvh-print-website'); ?></h2>
<p><?php
    esc_html_e(
        'This design is meant for printing on both sides of the page so you see two pages at once (the "page spread"). So the left-hand page will be styled differently than the right-hand page.',
        'avpvh-print-website'
    );
    ?></p>
<ul class="pmb-list">
    <li><?php esc_html_e('Page numbers in bottom-outside corner, part titles appear in the top of the left page, article titles appear at the top of the right page. Front-matter’s pages are numbered with Roman numerals', 'avpvh-print-website'); ?><?php avpvh_prwb_pro_print_service_best_e(__('Most browsers add their own content to the page margins', 'avpvh-print-website')); ?></li>
    <li><?php esc_html_e('Each article can optionally begin on a new page', 'avpvh-print-website'); ?></li>
</ul>
