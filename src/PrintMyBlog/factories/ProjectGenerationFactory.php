<?php

namespace Avpvh\PrintWebsite\factories;

use Avpvh\PrintWebsite\entities\FileFormat;
use Avpvh\PrintWebsite\orm\entities\Project;
use Avpvh\PrintWebsite\system\Context;

/**
 * Class ProjectGenerationFactory
 * @package Avpvh\PrintWebsite\factories
 * Makes us some ProjectGeneration objects and makes sure their dependencies get injected.
 */
class ProjectGenerationFactory
{
    /**
     * @param Project $project
     * @param FileFormat $format
     * @return ProjectGeneration
     */
    public function create(Project $project, FileFormat $format)
    {
        return Context::instance()->useNew(
            'Avpvh\PrintWebsite\entities\ProjectGeneration',
            [
                $project,
                $format,
            ]
        );
    }
}
