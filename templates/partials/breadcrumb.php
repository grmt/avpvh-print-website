<?php
use \Avpvh\PrintWebsite\orm\entities\Project
/**
 * @var Project|true $project
 * @var string|null $project_url
 */
?>
<div class="pmb-top-bar">
	<h1 class="pmb-breadcrumb">
		<span><?php esc_html_e('AV-PvH Print Website', 'avpvh-print-website');?></span>
		<?php
		if($project){
			$projects_url = admin_url(AVPVH_PRWB_ADMIN_PROJECTS_PAGE_PATH);
			?>
			<span><a href="<?php echo esc_attr($projects_url);?>"><?php esc_html_e('Saved Prints', 'avpvh-print-website');?></a></span>
			<?php
		}
		if($project instanceof Project){
		    ?>
            <span><a href="<?php echo esc_attr($project_url);?>"><?php echo $project->getWpPost()->post_title;?></a></span>
            <?php
        }
		?>
	</h1>
</div>