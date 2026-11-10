<?php
/**
 * This function is only included when rendering AV-PvH Print Website
 */

use Avpvh\PrintWebsite\orm\entities\Design;
use Avpvh\PrintWebsite\orm\entities\Project;

/**
 * Returns the post's anchor ID.
 * @param WP_Post|null $post
 * @return bool|false|string|WP_Error
 */
function avpvh_prwb_get_the_post_anchor($post = null)
{
    return avpvh_prwb_convert_url_to_anchor(get_the_permalink($post));
}

/**
 * Gets the site's domain (site_url minus the "https://" or "http://" prefix).
 *
 * @return string|string[]|void
 */
function avpvh_prwb_get_domain()
{
    return str_replace('http://','', site_url('', 'http'));
}

/**
 * Echoes the anchor ID for the post. Does not escape it (use avpvh_prwb_permalink_as_attr
 */
function avpvh_prwb_the_post_anchor()
{
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by avpvh_prwb_convert_url_to_anchor
    echo avpvh_prwb_convert_url_to_anchor(avpvh_prwb_get_the_post_anchor());
}

/**
 * Takes a URL and turns it into the form we use for post anchor links in PMB projects.
 * Specifically,
 * * removes the "http://" or "https://",
 * * removes "www."
 * * removes the domain name.
 * @param string $url
 * @return string|void
 */
function avpvh_prwb_convert_url_to_anchor($url)
{
    return str_replace(
        [
            '%',
            'https://www.',
            'http://www.',
            'https://',
            'http://',
            avpvh_prwb_get_domain(),
        ],
        [
            '-',
            '',
            '',
            '',
            '',
            '',
        ],
        $url
    );
}

/**
 * @param string $relative_filepath filepath relative to the current design's templates directory
 * @param array $context_variables an array to extract into variables when rendering the template. Keys become variable names, values become their values.
 * @global Design $avpvh_prwb_design
 * @global Project $avpvh_prwb_project
 * @global \Avpvh\PrintWebsite\entities\ProjectGeneration $avpvh_prwb_project_generation
 */
function avpvh_prwb_include_design_template($relative_filepath, $context_variables = [])
{
    /**
     * @var $avpvh_prwb_design Design
     */
    global $avpvh_prwb_project, $avpvh_prwb_design, $avpvh_prwb_project_generation;
    extract($context_variables);
    require $avpvh_prwb_design->getDesignTemplate()->getTemplatePathToDivision($relative_filepath);
}

/**
 * Add this to the HTML div that wraps a section and its subsections.
 * @param string $class
 */
function avpvh_prwb_section_wrapper_class($class = '')
{
    global $post;
    $section = $post->avpvh_prwb_section;
    $avpvh_prwb_classes = '';
    if ($section instanceof \Avpvh\PrintWebsite\orm\entities\ProjectSection) {
        $avpvh_prwb_classes = 'pmb-' . avpvh_prwb_map_section_to_division($section) . '-wrapper pmb-section-wrapper';
        if ($section->getSectionOrder() === 1) {
            $avpvh_prwb_classes .= ' pmb-first-section-wrapper';
        }
    }
    echo 'class="' . esc_attr($avpvh_prwb_classes . ' ' . $class) . '"';
}

/**
 * Add this to any article tags for a PMB section.
 * @param string $class
 * @return void echoes
 */
function avpvh_prwb_section_class($class = '')
{
    global $post;
    $section = $post->avpvh_prwb_section;
    $avpvh_prwb_classes = '';
    if ($section instanceof \Avpvh\PrintWebsite\orm\entities\ProjectSection) {
        $avpvh_prwb_classes = ' pmb-section pmb-' . avpvh_prwb_map_section_to_division($section) . ' pmb-height-' . $section->getHeight() . ' pmb-depth-' . $section->getDepth();
        if ((int)$section->getSectionOrder() === 1) {
            $avpvh_prwb_classes .= ' pmb-first-section';
        }
    }
    post_class($avpvh_prwb_classes . ' ' . $class);
    echo 'data-height="' . esc_attr($section->getHeight()) . '" data-depth="' . esc_attr($section->getDepth()) . '"';
}

/**
 * Echoes the ID attribute and value for a PMB section wrapper tag.
 */
function avpvh_prwb_section_wrapper_id()
{
    global $post;
    echo 'id="' . esc_attr($post->post_name) . '-wrapper"';
}

/**
 * Echoes out the ID attribute to use on the section.
 */
function avpvh_prwb_section_id(){
	echo 'id="' . esc_attr(avpvh_prwb_get_the_post_anchor()) . '"';
}

/**
 * Returns the current post's permalink as an attribute
 * @return string|void
 * @deprecated use esc_attr(avpvh_prwb_get_the_post_anchor())
 */
function avpvh_prwb_permalink_as_attr(){
    return esc_attr(avpvh_prwb_get_the_post_anchor());
}

/**
 * Echoes out the section's title and makes sure to add the CSS class PMB expects (especially important for finding the table of contents.)
 */
function avpvh_prwb_the_title()
{
    echo '<h1 class="pmb-title">' . esc_html(avpvh_prwb_get_title()) . '</h1>';
}

/**
 * Gets the current post project's title.
 * @return string
 */
function avpvh_prwb_get_title()
{
    $post = get_post();
    if ($post instanceof WP_Post) {
        $title_from_meta = get_post_meta($post->ID, 'avpvh_prwb_title', true);
        if ($title_from_meta) {
            $title = $title_from_meta;
        } else {
            $title = get_the_title($post);
        }
    } else {
        $title = '';
    }
    return (string)$title;
}

/**
 * Returns whether or not the $post_content_thing is in the 'post_content' PMB settings. If it isn't, returns $default.
 * @param string $post_content_thing
 * @param mixed $default
 * @return bool
 */
function avpvh_prwb_design_uses($post_content_thing, $default)
{
    global $avpvh_prwb_design;
    $post_content = $avpvh_prwb_design->getSetting('post_content');
    if (! $post_content) {
        return $default;
    }
    return in_array($post_content_thing, $post_content, true);
}


/**
 * Gets the template options select input HTML
 * @param $selecstring ted_template
 * @param Project $project
 *
 * @return string
 */
function avpvh_prwb_section_template_selector($selected_template, Project $project)
{
    $options = $project->getSectionTemplateOptions();
    $html = '<select class="pmb-template">';
    foreach ($options as $value => $display_text) {
        $html .= '<option value="' . esc_attr($value) . '" ' . selected($value, $selected_template, false) . '>' . $display_text . '</option>';
    }
    $html .= '</select>';
    return $html;
}

/**
 * @param WP_Post_Type $post_type
 *
 * @param string $extra_css_classes
 * @param string $extra_attrs
 * @return string HTML for the post type's icon
 */
function avpvh_prwb_post_type_icon_html(WP_Post_Type $post_type, $extra_css_classes = '', $extra_attrs = '')
{
    $icon = $post_type->menu_icon;
    if (empty($icon)) {
        $icon = 'dashicons-media-default';
    }
    $img = '<img src="' . $icon . '" alt="" />';
    $img_style = '';
    $img_class = '';
    if ('none' === $icon || 'div' === $icon) {
        $img = '<br />';
    } elseif (0 === strpos($icon, 'data:image/svg+xml;base64,')) {
        $img = '<br />';
        $img_style = ' style=\'background-image:url("' . esc_attr($icon) . '") !important;\'';
        $img_class = 'pmb-svg-icon svg';
    } elseif (0 === strpos($icon, 'dashicons-')) {
        $img = '<br />';
        $img_class = ' dashicons ' . sanitize_html_class($icon);
    }
    return "<div class='{$img_class} " . esc_attr($extra_css_classes) . "'{$img_style} {$extra_attrs}>{$img}</div>";
}

/**
 * Copy of WP's deprecated the_meta() function: echoes out the post's meta keys and values.
 */
function avpvh_prwb_the_meta() {
    $keys = get_post_custom_keys();
    if ( $keys ) {
        $li_html = '';
        foreach ( (array) $keys as $key ) {
            $keyt = trim( $key );
            if ( is_protected_meta( $keyt, 'post' ) ) {
                continue;
            }

            $values = array_map( 'trim', get_post_custom_values( $key ) );
            $value  = implode( ', ', $values );

            $html = sprintf(
                "<li><span class='post-meta-key'>%s</span> %s</li>\n",
                /* translators: %s: Post custom field name. */
                esc_html( sprintf( _x( '%s:', 'Post custom field name' ), $key ) ),
                esc_html( $value )
            );

            /**
             * Filters the HTML output of the li element in the post custom fields list.
             *
             * @since 2.2.0
             *
             * @param string $html  The HTML output for the li element.
             * @param string $key   Meta key.
             * @param string $value Meta value.
             */
            $li_html .= apply_filters( 'the_meta_key', $html, $key, $value );
        }

        if ( $li_html ) {
            echo "<ul class='post-meta'>\n{$li_html}</ul>\n";
        }
    }
}