<?php

namespace Avpvh\PrintWebsite\factories;

use Avpvh\PrintWebsite\entities\ProjectGeneration;
use Avpvh\PrintWebsite\orm\entities\Design;
use Avpvh\PrintWebsite\services\generators\ProjectFileGeneratorBase;
use Avpvh\PrintWebsite\system\Context;

/**
 * Class ProjectFileGeneratorFactory
 * @package Avpvh\PrintWebsite\factories
 */
class ProjectFileGeneratorFactory
{
    /**
     * @param string $classname
     * @param ProjectGeneration $project_generation
     * @param Design|null $design
     * @return ProjectFileGeneratorBase
     */
    public function create($classname, ProjectGeneration $project_generation, Design $design = null)
    {
        return Context::instance()->useNew(
            $classname,
            [
                $project_generation,
                $design,
            ]
        );
    }
}
