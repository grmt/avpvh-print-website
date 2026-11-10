<?php

namespace Avpvh\PrintWebsite\orm\managers;

use Avpvh\PrintWebsite\db\TableManager;
use Avpvh\PrintWebsite\orm\entities\Design;
use Avpvh\PrintWebsite\system\Context;
use Avpvh\PrintWebsite\Twine\orm\managers\PostWrapperManager;
use WP_Post;

/**
 * Class ProjectManager
 * @package Avpvh\PrintWebsite\orm\managers
 */
class ProjectManager extends PostWrapperManager
{
    /**
     * @var string
     */
    protected $class_to_instantiate = 'Avpvh\PrintWebsite\orm\entities\Project';

    /**
     * @var string
     */
    protected $cap_slug = 'avpvh_prwb_project';
}
