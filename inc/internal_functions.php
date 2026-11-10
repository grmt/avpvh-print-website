<?php
/**
 * Functions used internally by AV-PvH Print Website that other devs probably won't need.
 */

use Avpvh\PrintWebsite\controllers\Admin;
use Avpvh\PrintWebsite\controllers\Frontend;
use Avpvh\PrintWebsite\entities\DesignTemplate;
use Avpvh\PrintWebsite\orm\entities\Project;
use Avpvh\PrintWebsite\orm\entities\ProjectSection;
use Avpvh\PrintWebsite\system\Context;
use Avpvh\PrintWebsite\system\CustomPostTypes;

/**
 * Whether PMB is allowed to make requests to external services.
 * @return bool
 */
function avpvh_prwb_external_requests_enabled()
{
    return ! defined('AVPVH_PRWB_DISABLE_EXTERNAL_REQUESTS') || ! AVPVH_PRWB_DISABLE_EXTERNAL_REQUESTS;
}

function avpvh_prwb_is_premium()
{
    return false;
}

function avpvh_prwb_is_free_plan()
{
    return true;
}

function avpvh_prwb_has_required_plan($plan = '')
{
    return false;
}

function avpvh_prwb_upgrade_url()
{
    return admin_url(AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH);
}

function avpvh_prwb_is_registered_install()
{
    return false;
}

function avpvh_prwb_is_anonymous_install()
{
    return false;
}

function avpvh_prwb_connect_again()
{
}

function avpvh_prwb_license()
{
    return null;
}

function avpvh_prwb_site_registration()
{
    return null;
}

/**
 * Adds a help icon with text
 * @param string $explanation
 * @return string
 */
function avpvh_prwb_hover_help($explanation = '')
{
    return '<span data-help="' . esc_attr($explanation) . '" class="dashicons dashicons-info pmb-hover"></span>';
}

/**
 * Adds an icon which has explanatory text when hovered over.
 * @param string $explanation
 * @param string $extra_css_classes
 * @param string $url not escaped
 * @return string HTML
 */
function avpvh_prwb_hover_tip($explanation = '', $extra_css_classes = '', $url = '')
{
    $html = '';
    if ($url) {
        $html .= '<a href="' . esc_url($url) . '">';
    }
    $html .= '<span data-help="' . esc_attr($explanation) . '" class="dashicons dashicons-superhero pmb-hover ' . $extra_css_classes . '"></span>';
    if ($url) {
        $html .= '</a>';
    }
    return $html;
}

/**
 * Returns a string that says this feature only works with Pro Print Service (not supported by browsers).
 * @return string
 */
function avpvh_prwb_pro_print_service_only($explanation = '')
{
    return '';
}

/**
 * Echoes out that this feature only works with pro print service (not supported by browsers)
 * @param string $explanation
 */
function avpvh_prwb_pro_print_service_only_e($explanation = '')
{
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- outputting HTML
    echo avpvh_prwb_pro_print_service_only($explanation);
}

/**
 * Returns a string that says this feature works best with AV-PvH Print Website Pro.
 * @return string
 */
function avpvh_prwb_pro_print_service_best($explanation = '')
{
    return '';
}

/**
 * Echoes out this feature works best with pro print service.
 * @param string $explanation
 */
function avpvh_prwb_pro_print_service_best_e($explanation = '')
{
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- outputting HTML
    echo avpvh_prwb_pro_print_service_best($explanation);
}

/**
 * Echoes out that there's a better feature in Pro Print
 * @param string $explanation
 */
function avpvh_prwb_pro_better_e($explanation = '')
{
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- outputting HTML
    echo avpvh_prwb_pro_better($explanation);
}

/**
 * To tell folks about features of Pro Print not available for Quick print
 * @param string $explanation
 * @return string
 */
function avpvh_prwb_pro_better($explanation = '')
{
    return '';
}


/**
 * Maps
 * @param \Avpvh\PrintWebsite\orm\entities\ProjectSection $section
 *
 * @return string
 */
function avpvh_prwb_map_section_to_division(ProjectSection $section)
{
    if ($section->getPlacement() === DesignTemplate::IMPLIED_DIVISION_FRONT_MATTER) {
        return 'front_matter_article';
    }
    if ($section->getPlacement() === DesignTemplate::IMPLIED_DIVISION_BACK_MATTER) {
        return 'back_matter_article';
    }
    return apply_filters(
        'avpvh_prwb_map_section_to_division',
        avpvh_prwb_map_section_height_to_division($section->getHeight()),
        $section
    );
}

/**
 * @param int $height
 * @return string
 */
function avpvh_prwb_map_section_height_to_division($height)
{
    switch ($height) {
        case 1:
            $division_name = 'part';
            break;
        case 2:
            $division_name = 'volume';
            break;
        case 3:
            $division_name = 'anthology';
            break;
        case 0:
        default:
            $division_name = 'article';
    }
    return $division_name;
}

/**
 * Renders a PMB template in the template directory
 * @param string $template_name
 * @param array $context
 */
function avpvh_prwb_render_template($template_name, $context = [])
{
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- outputting HTML
    echo avpvh_prwb_get_contents(AVPVH_PRWB_TEMPLATES_DIR . $template_name, $context);
}

/**
 * Renders any file
 * @param $filename
 * @param array $context
 */
function avpvh_prwb_get_contents($filename, $context = [])
{
    // phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- we're making them available while rendering the template file.
    extract($context);
    ob_start();
    require $filename;
    return ob_get_clean();
}

/**
 * @param \Avpvh\PrintWebsite\orm\entities\Design $design
 */
function avpvh_prwb_design_preview(\Avpvh\PrintWebsite\orm\entities\Design $design)
{
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- outputting HTML
    avpvh_prwb_render_template(
        'partials/design_preview.php',
        [
            'design' => $design,
        ]
    );
}

/**
 * Gets a post item div HTML for the sortable.js sorting area.
 * @param ProjectSection|WP_Post $posty_row
 * @param Project $project
 * @param int $max_nesting
 */
function avpvh_prwb_content_item($posty_row, Project $project, $max_nesting = null)
{
    if ($posty_row instanceof \Avpvh\PrintWebsite\orm\entities\ProjectSection) {
        $id = $posty_row->getPostId();
        $title = $posty_row->getPostTitle();
        $post_type = get_post_type_object(get_post_type($posty_row->getPostId()));
        $template = $posty_row->getTemplate();
        $height = $posty_row->getHeight();
        $subs = $posty_row->getCachedSubsections();
        $depth = $posty_row->getDepth();
    } else {
        $id = $posty_row->ID;
        $title = $posty_row->post_title;
        $post_type = get_post_type_object($posty_row->post_type);
        $template = null;
        $height = 0;
        $subs = [];
        $depth = 1;
    }
    $edit_url = get_edit_post_link($id);
    $view_url = get_permalink($id);
    $duplicate_url = wp_nonce_url(
        add_query_arg(
            [
                'action' => Admin::SLUG_ACTION_DUPLICATE_PRINT_MATERIAL,
                'ID' => $id,
            ],
            admin_url(AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH)
        ),
        Admin::SLUG_ACTION_DUPLICATE_PRINT_MATERIAL
    );
    // if the post type is no longer registered, the plugin that added it probably got removed, so hide this item.
    if (! $post_type) {
        return;
    }
    if ($max_nesting === null) {
        $max_nesting = $project->getLevelsAllowed();
    }
    ?>
    <div class="list-group-item pmb-project-item" tabindex="0" id="pmb-content-item-<?php echo esc_attr($id); ?>" data-id="<?php echo esc_attr($id); ?>">
        <div class="pmb-project-item-header" title="<?php
        echo esc_attr(
            sprintf(
                __('Drag the %s "%s" into your project', 'avpvh-print-website'),
                $post_type->labels->singular_name,
                $title
            )
        );
        ?>
">
            <div class="pmb-grabbable pmb-project-item-title">
                <?php
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function returns HTML and prepares it.
                echo avpvh_prwb_post_type_icon_html($post_type);
                ?>
                <span class="pmb-project-item-title-text"><?php echo esc_html($title); ?></span>
                <?php do_action('avpvh_prwb_content_items__project-item-title end', $id, $title, $post_type, $template, $subs, $depth); ?>
            </div>

            <div class="pmb-project-item-options">
                <div class="pmb-project-item-options-inner">
                    <div class="pmb-project-item-options-buttons no-drag">
                        <?php
                        // only show the viewing button if it will work
                        if ($post_type->public) {
                            ?><a
                                    href="<?php echo esc_attr($view_url); ?>"
                                    title="<?php
                                    echo esc_attr(
                                        sprintf(
                                        // translators: %s: post title.
                                            __('View "%s"', 'avpvh-print-website'),
                                            $title
                                        )
                                    );
                                    ?>"
                                    target="_blank"><span
                                        class="dashicons dashicons-visibility pmb-icon pmb-clickable"></span></a><?php
                        }
                        ?><a
                                href="<?php echo esc_attr($edit_url); ?>"
                                title="<?php
                                echo esc_attr(
                                    sprintf(
                                        // translators: %s: post title.
                                        __('Edit "%s"', 'avpvh-print-website'),
                                        $title
                                    )
                                );
                                ?>"
                                target="_blank"><span
                                    class="dashicons dashicons-edit pmb-icon pmb-clickable"></span></a><?php
                        if (avpvh_prwb_has_required_plan('founding_members')) {
                            $label = '';
                            if ($post_type->name !== CustomPostTypes::CONTENT) {
                                $print_material = null;
                                $print_materials = Context::instance()->reuse('Avpvh\PrintWebsite\Twine\orm\managers\PostWrapperManager')->getByPostMeta('_avpvh_prwb_original_post', (string)$id, 1);
                                if ($print_materials) {
                                    $print_material = reset($print_materials);
                                }
                                if ($print_material) {
                                    // translators: 1: post title
                                    $label = sprintf(__('Replace "%s" with the existing Print Material for customizing.', 'avpvh-print-website'), $print_material->getWpPost()->post_title);
                                } else {
                                    $label = sprintf(__('Replace "%s" with a New Print Material for customizing', 'avpvh-print-website'), $title);
                                }
                            }
                            if ($label) {
                                ?><a
                                title="<?php
                                echo esc_attr($label);
                                ?>"
                                data-id="<?php echo esc_attr($id); ?>" class="pmb-duplicate-post-button" tabindex="0"><span class="dashicons dashicons-update pmb-icon pmb-clickable"></span></a><?php
                            }
                        } else {
                            if ($post_type->name !== CustomPostTypes::CONTENT) {
                                ?><span tabindex="0" class="dashicons dashicons-update pmb-icon pmb-disabled-icon" title="<?php esc_attr_e('Upgrade to Professional License for one-click copying to Print Materials for customization.', 'avpvh-print-website'); ?>"></span>
                                <?php
                            }
                        }
                        ?><a
                                title="<?php
                                echo esc_attr(
                                    sprintf(
                                            // translators: %s: post title
                                        __('Add "%s" to project', 'avpvh-print-website'),
                                        esc_html($title)
                                    )
                                );
                                ?>"
                                class="pmb-add-item" href="#pmb-content-item-<?php echo esc_attr($id); ?>"><span
                                    class="dashicons dashicons-plus pmb-icon pmb-clickable"></span></a><a
                                title="<?php
                                echo esc_attr(
                                    sprintf(
                                            // translators: %s: post title
                                        __('Remove "%s"', 'avpvh-print-website'),
                                        $title
                                    )
                                );
                                ?>"
                                tabindex="0"><span
                                    class="dashicons dashicons-no-alt pmb-icon pmb-remove-item pmb-clickable"></span></a>
                    </div>
                    <span class="pmb-project-item-template-container"><?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- that's meant to output HTML.
                        echo avpvh_prwb_section_template_selector($template, $project); ?></span>
                </div>
            </div>
        </div>

        <div class="pmb-nested-sortable pmb-draggable-area <?php echo $depth < $max_nesting ? 'pmb-sortable' : 'pmb-sortable-inactive'; ?> pmb-subs">
            <?php
            foreach ($subs as $sub) {
                avpvh_prwb_content_item($sub, $project, $max_nesting);
            }
            avpvh_prwb_drag_here();
            ?>
        </div>

    </div>
    <?php
}

/**
 * Echoes HTML to show that project items can be dragged to this area.
 */
function avpvh_prwb_drag_here()
{
    ?>
    <div class="pmb-no-sort pmb-drag-here no-drag">
        <div class="pmb-drag-here-inner">
            <a class="pmb-add-material" href="#ui-id-1">
                <?php esc_html_e('Drag or click here', 'avpvh-print-website'); ?> <span class="pmb-add-section dashicons
                dashicons-plus-alt"></span>
            </a>
        </div>
    </div>
    <?php
}

/**
 * Gets the URL to perform "PMB AJAX" requests (just like regular WP_AJAX, except on a request that's technically
 * for the frontend, which most plugins think it's a frontend request).
 * @return string
 */
function avpvh_prwb_ajax_url(){
    return add_query_arg(
        [
            Frontend::AVPVH_PRWB_AJAX_INDICATOR => 1,
        ],
        site_url('/')
    );
}

function avpvh_prwb_get_filename($url, $remove_extension = false){
    $filename_and_extension = basename($url);
    if($remove_extension){
        $filename_and_extension = substr($filename_and_extension, 0, strpos($filename_and_extension, '.'));
    }
    return $filename_and_extension;
}

/**
 * Returns whether or not to use PMB Central for generate requests. (We like to use DocRaptor's server on free sites
 * to keep the load down on printmy.blog; but for paying customers we like to monitor their API requests for quality assurance.)
 * @return int
 */
function avpvh_prwb_use_avpvh_prwb_central(){
    $use_avpvh_prwb_central = 0;
    if (! avpvh_prwb_external_requests_enabled()) {
        return $use_avpvh_prwb_central;
    }
    if (avpvh_prwb_has_required_plan('business') || (defined('AVPVH_PRWB_USE_CENTRAL') && AVPVH_PRWB_USE_CENTRAL)) {
        $use_avpvh_prwb_central = 1;
    }

    return $use_avpvh_prwb_central;
}
