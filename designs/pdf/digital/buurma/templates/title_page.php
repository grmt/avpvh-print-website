<?php
/**
 * @var $avpvh_prwb_project \Avpvh\PrintWebsite\orm\entities\Project
 * @var $avpvh_prwb_design \Avpvh\PrintWebsite\orm\entities\Design
*
 */
?>
<div class="pmb-title-page">
    <span class="buurma-issue"><?php $avpvh_prwb_project->echoSetting('issue');?></span>
        <h1 class="project-title"><?php $avpvh_prwb_project->echoPublishedTitle(); ?></h1>

        <h2 class="project-byline"><?php $avpvh_prwb_project->echoSetting('byline'); ?></h2>
        <span class="project-date"><?php $avpvh_prwb_project->echoSetting('date');?></span>
    <div class="project-description"><?php $avpvh_prwb_project->echoSetting('cover_preamble');?></div>
</div>