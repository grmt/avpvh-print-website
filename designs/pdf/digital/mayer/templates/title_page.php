<?php
/**
 * @var $avpvh_prwb_project \Avpvh\PrintWebsite\orm\entities\Project
 * @var $avpvh_prwb_design \Avpvh\PrintWebsite\orm\entities\Design
*
 */
?>
<div class="pmb-posts-header">
    <h1 class="project-title mayer-wide"><?php $avpvh_prwb_project->echoPublishedTitle(); ?></h1>
    <h2 class="project-byline mayer-wide"><?php $avpvh_prwb_project->echoSetting('byline'); ?></h2>
    <?php
    $intro = $avpvh_prwb_project->renderSetting('cover_preamble');
    if ($intro) { ?>
        <p class="project-intro"><?php echo $intro; ?></p>
	<?php } ?>