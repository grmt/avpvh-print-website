<h2><?php esc_html_e('Purpose', 'avpvh-print-website'); ?></h2>
<p><?php esc_html_e('Very similar to AV-PvH Print Website Quick Print, but supporting Pro features. Great for making a PDF for an easy-to-read digital backup of your favorite website content.', 'avpvh-print-website'); ?></p>

<h2><?php esc_html_e('Features', 'avpvh-print-website'); ?></h2>
<ul class="pmb-list">
    <li><?php esc_html_e('Supports nesting articles (like posts and pages) into “parts”', 'avpvh-print-website'); ?></li>
    <li><?php esc_html_e('External hyperlinks (links to web pages not in the project) can be automatically converted to footnotes, left in-place, or removed', 'avpvh-print-website'); ?><?php avpvh_prwb_pro_print_service_best_e(__('Footnotes only work with Pro', 'avpvh-print-website')); ?></li>
    <li><?php esc_html_e('Internal hyperlinks (links to content included in the project) can be automatically converted to to footnotes, inline page references, left in-place, or removed', 'avpvh-print-website'); ?><?php avpvh_prwb_pro_print_service_best_e(__('Footnotes and page references only work with Pro PDF Service', 'avpvh-print-website')); ?></li>
    <li><?php esc_html_e('The default title page can include: Project Title, Subtitle, Site URL, Date Printed, Credit to AV-PvH Print Website', 'avpvh-print-website'); ?></li>
    <li><?php esc_html_e('Each article can include: Title, ID, Author, URL, Date Published, Categories and Tags, Featured Image, Excerpt, Custom Fields, Content', 'avpvh-print-website'); ?></li>
    <li><?php esc_html_e('Limit image size by setting maximum height', 'avpvh-print-website'); ?></li>
    <li><?php esc_html_e('Optionally automatically center images', 'avpvh-print-website'); ?></li>
    <li><?php esc_html_e('Optionally float images to the top or bottom of each page', 'avpvh-print-website'); ?><?php avpvh_prwb_pro_print_service_only_e(); ?></li>
</ul>
<h2><?php esc_html_e('Page Layout', 'avpvh-print-website'); ?></h2>
<ul class="pmb-list">
    <li><?php esc_html_e('Page numbers in bottom-right corner, article titles in the top-right, and part titles (if using parts) appear in the top-left corner. Front-matter’s pages are numbered with Roman numerals', 'avpvh-print-website'); ?><?php avpvh_prwb_pro_print_service_best_e(__('Most browsers add their own content to the page margins', 'avpvh-print-website')); ?></li>
    <li><?php esc_html_e('Each article can optionally begin on a new page', 'avpvh-print-website'); ?></li>
</ul>
