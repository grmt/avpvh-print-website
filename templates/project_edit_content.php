<?php

use Avpvh\PrintWebsite\orm\entities\Project;

/**
 * @var $project \Avpvh\PrintWebsite\orm\entities\Project
 * @var $form_url string
 * @var $project_support_front_matter bool
 * @var $project_support_back_matter bool
 * @var $front_matter_sections \Avpvh\PrintWebsite\orm\entities\ProjectSection[]
 * @var $main_matter_sections \Avpvh\PrintWebsite\orm\entities\ProjectSection[]
 * @var $back_matter_sections \Avpvh\PrintWebsite\orm\entities\ProjectSection[]
 * @var $post_types WP_Post_Type[]
 * @var $authors WP_User[]
 * @var $steps_to_urls array
 * @var $current_step string
 */
avpvh_prwb_render_template(
	'partials/project_header.php',
	[
		'project' => $project,
		'page_title' => __('Edit Project Content', 'avpvh-print-website'),
		'current_step' => $current_step,
		'steps_to_urls' => $steps_to_urls
	]
);
?>
    <form id="pmb-filter-form" method="get" action="<?php echo esc_attr( admin_url( 'admin-ajax.php' ));?>">
        <input type="hidden" name="action" value="avpvh_prwb_post_search">
        <input type="hidden" name="project" value="<?php echo $project->getWpPost()->ID;?>">
    </form>
    <form id="pmb-project-form" method="POST" action="<?php echo $form_url;?>">
        <div id="pmb-project-layout" class="pmb-project-layout">
            <div class="pmb-project-layout-inner">
                <div class="pmb-project-column pmb-project-choices-column">
                    <div class="pmb-project-choices-text"><h2 class="pmb-inline-header pmb-snug-header"><span class="dashicons dashicons-search"></span><?php _e('Available Content', 'avpvh-print-website');?>
                        <?php
                        echo avpvh_prwb_hover_help(
                                sprintf(
                                __('Select content from your website, then move it into your project. %1$sRead more%2$s.', 'avpvh-print-website'),
                                '<a href="https://printmy.blog/user-guide/getting-started/6-choose-project-content/" target="_blank">',
                                '</a>'
                            )
                        );
                        ?></h2>
                            <span id="pmb-use-ctrl-key" class="pmb-comment pmb-use-ctrl-key"><?php _e('Use <em>CTRL</em> or <em>SHIFT</em> to select multiple items', 'avpvh-print-website');?></span></div>
                    <div class="pmb-search-bar">
                        <input id="pmb-project-choices-search" class="pmb-search-input" type="text" name="pmb-search" form="pmb-filter-form" placeholder="<?php echo esc_attr(__('Search Posts, Pages, Custom Post Types...', 'avpvh-print-website'));?>">
                        <a id="pmb-expand-filters" href="#pmb-filters" class="pmb-expand-filters button pmb-filters-closed"><?php esc_html_e('Show Filters', 'avpvh-print-website');?><span class="dashicons dashicons-filter pmb-icon"></span></a>
                        <a id="pmb-hide-filters" href="#pmb-content-available" class="pmb-hide-filters button pmb-filters-opened"><?php esc_html_e('Hide Filters', 'avpvh-print-website');?><span class="dashicons dashicons-filter pmb-icon"></span></a>
                    </div>
                    <div class="pmb-project-choices-filters pmb-scrollable-window pmb-filters-opened" id="pmb-filters">
                        <div class="pmb-project-choices-filters-table-wrap">
                            <table class="form-table">
                                <?php do_action('avpvh_prwb__project_edit_content__filters_top', $project);?>
                                <tr>
                                    <th><label for="pmb-project-choices-post-type"><?php esc_html_e('Post Type', 'avpvh-print-website');?></label></th>
                                    <td>
                                        <select id="pmb-project-choices-post-type" name="pmb-post-type" form="pmb-filter-form">
                                            <option value=""><?php esc_html_e('Any', 'avpvh-print-website');?></option>
                                            <?php
                                            foreach($post_types as $post_type_obj){
                                                ?><option value="<?php echo esc_attr($post_type_obj->name);?>"><?php echo $post_type_obj->label;?></option><?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('Status','avpvh-print-website' );?></th>
                                    <td> <?php
                                        // status
                                        $statuses = [
                                            'draft'    => esc_html__( 'Draft' ),
                                            'pending'  => esc_html__( 'Pending Review' ),
                                            'private'  => esc_html__( 'Private' ),
                                            'publish'  => esc_html__( 'Published' ),
                                            'future'   => esc_html__( 'Scheduled' ),
                                            'trash'    => esc_html__( 'Trash' )
                                        ];
                                        foreach($statuses as $value => $label ){
                                            ?>
                                            <input type="checkbox" name="pmb-status[]" value="<?php echo esc_attr($value);?>" id="<?php echo esc_attr($value);?>-id" <?php echo in_array($value, ['publish', 'private']) ? 'checked="checked"' : '';?> form="pmb-filter-form">
                                            <label for="<?php echo esc_attr($value);?>-id"><?php echo $label;?></label><br>
                                            <?php
                                        }
                                        ?></td>
                                </tr>
                                <?php foreach(get_taxonomies(array('show_in_rest' => true, 'show_ui' => true), 'objects') as $taxonomy){
                                    $rest_base = $taxonomy->rest_base ? $taxonomy->rest_base : $taxonomy->name;
                                    /**
                                     * @var $taxonomy WP_Taxonomy
                                     */
                                    ?>
                                    <tr>
                                        <th><label for="pmb-project-choices-by"><?php echo $taxonomy->label; ?></label></th>
                                        <td>
                                            <select id="pmb-taxonomy-<?php echo esc_attr($taxonomy->name);?>" class="pmb-taxonomies-select" name="taxonomies[<?php echo esc_attr($taxonomy->name);?>][]" multiple="multiple"data-rest-base="<?php echo esc_attr($rest_base);?>" form="pmb-filter-form"></select>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <th><label for="pmb-project-choices-by"><?php esc_html_e('By', 'avpvh-print-website'); ?></label></th>
                                    <td>
                                        <select id="pmb-project-choices-by" class="pmb-author-select" name="pmb-author" form="pmb-filter-form">
                                            <option value=""></option>
                                            <?php
                                            foreach($authors as $author){
                                                ?><option value="<?php echo esc_attr($author->ID);?>"><?php echo $author->display_name;?></option><?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('Written Between', 'avpvh-print-website');?></th>
                                    <td><?php printf(
                                            // translators: 1: "from" date input, 2: "to" date input
                                            __('%s and %s', 'avpvh-print-website'),
                                        '<input id="pmb-from-date" type="text" class="pmb-date" name="pmb-date[from]" form="pmb-filter-form">',
                                        '<input id="pmb-to-date" type="text" class="pmb-date" name="pmb-date[to]" form="pmb-filter-form">'
                                    ); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="pmb-show-included"><?php esc_html_e('Show Included Content?', 'avpvh-print-website');?></label></th>
                                    <td><input type="checkbox" form="pmb-filter-form" name="pmb-show-included" id="pmb-show-included">
                                        <p class="pmb-help"><?php esc_html_e('Show content already added to project', 'avpvh-print-website');?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="pmb-project-choices-order-by"><?php esc_html_e('Order By', 'avpvh-print-website');?></label></th>
                                    <td><select id="pmb-project-choices-order-by" name="pmb-order-by" form="pmb-filter-form">
                                            <?php
                                            $orderby = [
                                                'title' => __('Title', 'avpvh-print-website'),
                                                'date' => __('Date', 'avpvh-print-website'),
                                                'menu_order' => __('Menu Order', 'avpvh-print-website'),
                                                'relevance' => __('Search Relevance', 'avpvh-print-website'),
                                                'ID' => __('Post ID', 'avpvh-print-website'),
                                            ];
                                            $default = 'ID';
                                            foreach($orderby as $value => $display){
                                                ?><option value="<?php echo esc_attr($value);?>" <?php echo $value === $default ? 'selected' : '';?> ><?php echo $display;?></option><?php
                                            }
                                            ?>
                                        </select>
                                        <br>
                                        <select name="pmb-order" form="pmb-filter-form">
                                            <option value="ASC"><?php esc_html_e('Ascending', 'avpvh-print-website');?></option>
                                            <option value="DESC" selected><?php esc_html_e('Descending', 'avpvh-print-website');?></option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="pmb-project-content-available list-group pmb-scrollable-window pmb-filters-closed" id="pmb-content-available">
                        <div id="pmb-project-choices" class="pmb-draggable-area pmb-project-choices">
                            <div class="no-drag"><div class="pmb-spinner"></div></div>
                        </div>
                    </div>
                    <div class="pmb-column-options">
                        <div class="pmb-filters-apply-bar pmb-filters-opened">
                            <a class="button pmb-hide-filters" id="pmb-filter-form-submit" href="#pmb-content-available"><?php esc_html_e('Search & Apply Filters', 'avpvh-print-website');?></a>
                        </div>
                        <div class="pmb-project-content-available-options pmb-filters-closed-flex">
                            <a class="button avpvh_prwb_spin_on_click" id="pmb-select-all" href="#pmb-content-available"><?php esc_html_e('Select All', 'avpvh-print-website');?></a>
                            <a class="button" id="pmb-deselect-all" href="#pmb-content-available"><?php esc_html_e('Deselect All', 'avpvh-print-website');?></a>
                        </div>
                    </div>
                </div>
                <div class="pmb-actions-column">
                    <button id="pmb-add-item" class="button" title="<?php
                    echo esc_attr(
                            __('Add selected items to project', 'avpvh-print-website')
                    );
                    ?>"></span><span class="dashicons dashicons-plus"></span></button>
                    <button id="pmb-remove-item" class="button" title="<?php
                    echo esc_attr(
                            __('Remove selected items', 'avpvh-print-website')
                    );
                    ?>"><span class="dashicons dashicons-no-alt"></span></button>
                    <button id="pmb-move-up" class="button" title="<?php
                    echo esc_attr(
                            __('Move selected items up', 'avpvh-print-website')
                    );
                    ?>"><span class="dashicons dashicons-arrow-up-alt2"></span></button>
                    <button id="pmb-move-down" class="button" title="<?php
                    echo esc_attr(
                            __('Move selected items down', 'avpvh-print-website')
                    );
                    ?>"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
                </div>
                <div class="pmb-project-column pmb-project-matters-wrapper">
                    <h2 class="pmb-snug-header"><span class="dashicons dashicons-portfolio"></span> <?php esc_html_e('Chosen Project Content', 'avpvh-print-website');?></h2>
                    <div class="pmb-project-matters pmb-scrollable-window">
                        <?php if($project_support_front_matter){
                           ?>
                            <h2><?php esc_html_e('Front Matter', 'avpvh-print-website');?> <?php
                                echo avpvh_prwb_hover_help(
                                    sprintf(
                                        __('Preliminary content like a title page, copyright page and introduction. %1$sRead more%2$s', 'avpvh-print-website'),
                                        '<a href="https://printmy.blog/user-guide/getting-started/6-choose-project-content/#front-matter" target="_blank">',
                                        '</a>'
                                    ),
                                    'dashicons-info'
                                );
                            ?></h2>
                            <div id="pmb-project-front-matter" class="pmb-draggable-area pmb-project-content-chosen list-group pmb-sortable pmb-sortable-base pmb-sortable-root" data-max-nesting="0">
	                            <?php
	                            foreach($front_matter_sections as $post) {
		                            avpvh_prwb_content_item( $post, $project, 0 );
	                            }
	                            avpvh_prwb_drag_here();
	                            ?>
                            </div>

                        <?php
                        }?>
                        <h2>
                            <?php _e('Body', 'avpvh-print-website');?>
                            <?php
                            $all_designs = $project->getDesignsSelected();
                            $a_design = reset($all_designs);
                            $division_descriptions = [];
                            for($i=0; $i < $project->getLevelsAllowed(); $i++){
                                $division_descriptions[]=  sprintf(
                                    __('Each %1$s can be put in a %2$s.', 'avpvh-print-website'),
                                    $a_design->getDesignTemplate()->divisionLabelSingular($i),
                                    $a_design->getDesignTemplate()->divisionLabelSingular($i+1)
                                );
                            }
                            echo avpvh_prwb_hover_help(
                                    sprintf(
                                            __('The main contents of your document or book, like chapters or articles. %1$s %2$sRead more%3$s', 'avpvh-print-website'),
                                        implode(', ', $division_descriptions),
                                        '<a href="https://printmy.blog/user-guide/getting-started/6-choose-project-content/#main-matter" target="_blank">',
                                        '</a>'
                                    )
                            )
                            ?>
                        </h2>
                        <div id="pmb-project-main-matter" class="pmb-draggable-area pmb-project-content-chosen list-group pmb-sortable pmb-sortable-base pmb-sortable-root" data-max-nesting="<?php echo esc_attr($project->getLevelsAllowed());?>">
                            <?php
                            foreach($sections as $post) {
                                avpvh_prwb_content_item( $post, $project );
                            }
                            avpvh_prwb_drag_here();
                            ?>
                        </div>
	                    <?php if($project_support_back_matter){
		                    ?>
                            <h2><?php esc_html_e('Back Matter', 'avpvh-print-website');?>
                            <?php
                                echo avpvh_prwb_hover_help(
                                    sprintf(
                                            __('Supplemental content like "About the author", glossary, and "Further reading". %1$sRead more.%2$s', 'avpvh-print-website'),
                                        '<a href="https://printmy.blog/user-guide/getting-started/6-choose-project-content/#back-matter" target="_blank">',
                                        '</a>'
                                    )
                            );
                            ?></h2>
                            <div id="pmb-project-back-matter" class="pmb-draggable-area pmb-project-content-chosen list-group pmb-sortable pmb-sortable-base pmb-sortable-root" data-max-nesting="0">
			                    <?php
			                    foreach($back_matter_sections as $post) {
				                    avpvh_prwb_content_item( $post, $project, 0 );
			                    }
			                    avpvh_prwb_drag_here();
			                    ?>
                            </div>
		                    <?php
	                    }?>
                    </div>
                    <input type="hidden" name="pmb-project-front-matter-data" id="pmb-project-front-matter-data">
                    <input type="hidden" name="pmb-project-main-matter-data" id="pmb-project-main-matter-data">
                    <input type="hidden" name="pmb-project-back-matter-data" id="pmb-project-back-matter-data">
                    <input type="hidden" name="pmb-project-depth" id="pmb-project-depth">
                </div>
            </div>
        </div>
        <?php wp_nonce_field( 'pmb-project-edit' );?>
        <div class="pmb-button-spacer">
            <button class="button-primary button" id="pmb-save" name="pmb-save" value="save"><?php esc_html_e('Save & Proceed', 'avpvh-print-website'); ?></button>
        </div>
    </form>

    <div
            style="display:none" id="pmb-add-print-materials-dialogue"
            title="<?php esc_html_e('Quick Add Print Material',
        'avpvh-print-website');?>"
    >
        <div class="pmb-add-print-materials-dialogue-content">
            <label><?php esc_html_e('Title', 'avpvh-print-website');?> <input type="text" name="title"
                                                                        id="pmb-print-material-title" ></label>
            <input type="hidden" name="project" value="<?php echo esc_attr($project->getWpPost()->ID);?>"
                   id="pmb-print-material-project">

        </div>
    </div>

<?php avpvh_prwb_render_template('partials/project_footer.php');
