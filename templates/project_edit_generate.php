<?php
/**
 * @var $generations \Avpvh\PrintWebsite\entities\ProjectGeneration[]
 * @var $project \Avpvh\PrintWebsite\orm\entities\Project
 * @var $steps_to_urls array
 * @var $current_step string
 * @var $review_url string
 * @var $suggest_review boolean
 */

use Avpvh\PrintWebsite\controllers\Admin;

avpvh_prwb_render_template(
	'partials/project_header.php',
	[
		'project' => $project,
		'page_title' => __('Generate Print Page', 'avpvh-print-website'),
		'current_step' => $current_step,
		'steps_to_urls' => $steps_to_urls
	]
);
$base_args = [
    'ID' => $project->getWpPost()->ID,
    'action' => Admin::SLUG_ACTION_EDIT_PROJECT,
];
$setup_link = add_query_arg(
    array_merge(
        $base_args,
        [
            'subaction' => Admin::SLUG_SUBACTION_PROJECT_SETUP
        ]
    ),
    admin_url(AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH)
);
$edit_content_link = add_query_arg(
        array_merge(
            $base_args,
            [
                'subaction' => Admin::SLUG_SUBACTION_PROJECT_CONTENT
            ]
        ),
    admin_url(AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH)
);
$edit_metadata_link = add_query_arg(
    array_merge(
        $base_args,
        [
            'subaction' => Admin::SLUG_SUBACTION_PROJECT_META
        ]
    ),
    admin_url(AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH)
);
$clear_cache_link = wp_nonce_url(
    add_query_arg(
        array_merge(
            $base_args,
            [
                'subaction' => Admin::SLUG_SUBACTION_PROJECT_CLEAR_CACHE
            ]
        ),
        admin_url(AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH)
    ),
    Admin::SLUG_ACTION_EDIT_PROJECT
);
do_action('project_edit_generate__under_header', $project, $generations);
?>
<?php
foreach($generations as $generation){
    $generation_base_args = array_merge(
            $base_args,
        [
            'format' => $generation->getFormat()->slug()
        ]
    );
	$generate_link = add_query_arg(
	    array_merge(
            $generation_base_args,
            [
                'subaction' => Admin::SLUG_SUBACTION_PROJECT_GENERATE,
            ]
        ),
		admin_url(AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH)
	);
	$change_design_link = add_query_arg(
        array_merge(
            $generation_base_args,
            [
                'subaction' => Admin::SLUG_SUBACTION_PROJECT_CHANGE_DESIGN
            ]
        ),
        admin_url(AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH)
    );
	$edit_design_link = add_query_arg(
        array_merge(
            $generation_base_args,
            [
                'subaction' => Admin::SLUG_SUBACTION_PROJECT_CUSTOMIZE_DESIGN,

            ]
        ),
        admin_url(AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH)
    );

	$format_slug = $generation->getFormat()->slug();
	?>
    <div class="pmb-generate-options-for-<?php echo esc_attr($format_slug);?>">
        <h2><?php echo $generation->getFormat()->coloredTitleAndIcon();?> <?php echo $project->getDesignFor($generation->getFormat())->getWpPost()->post_title;?></h2>
        <a class="button button-primary pmb-generate avpvh_prwb_spin_on_click" data-use-theme="<?php echo $project->getDesignFor($generation->getFormat())->getSetting('use_theme') ? '1' : '0';?>" data-format="<?php echo esc_attr($format_slug);?>" aria-label="<?php echo esc_attr(sprintf(esc_html__('Generate %s', 'avpvh-print-website'), $generation->getFormat()->title()));?>"><?php
            esc_html_e('Generate', 'avpvh-print-website');
        ?></a>
        <a
                class="button button-secondary" href="<?php echo esc_attr($change_design_link);?>"><?php esc_html_e('Change Design', 'avpvh-print-website');?></a>
        <a
            class="button button-secondary" href="<?php echo esc_attr($edit_design_link);?>"><?php esc_html_e('Customize Design', 'avpvh-print-website');?></a>
        <?php
        ?>
        <div class="pmb-after-generation" style="display:none">

            <button class="button button-primary" href="<?php echo esc_attr($generation->getGeneratedIntermediaryFileUrl());?>"><?php printf(__('View %s Print Page', 'avpvh-print-website'), $generation->getFormat()->title());?></button>
        </div>
    </div>
    <br>

<?php
}
?>
<h2><?php esc_html_e('Need to Change Something?', 'avpvh-print-website');?></h2>
    <a class="button" href="<?php echo esc_url($setup_link);?>"><?php esc_html_e('Change Title or Format', 'avpvh-print-website');?></a>
    <a class="button" href="<?php echo esc_url($edit_content_link);?>"><?php esc_html_e('Edit Content', 'avpvh-print-website');?></a>
    <a class="button" href="<?php echo esc_url($edit_metadata_link);?>"><?php esc_html_e('Edit Metadata', 'avpvh-print-website');?></a>
    <a class="button" href="<?php echo esc_url($clear_cache_link);?>"><?php esc_html_e('Clear Cached Resources', 'avpvh-print-website');?></a>
<?php
avpvh_prwb_render_template('partials/project_footer.php');

