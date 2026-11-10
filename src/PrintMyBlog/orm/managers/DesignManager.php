<?php

namespace Avpvh\PrintWebsite\orm\managers;

use Avpvh\PrintWebsite\orm\entities\Design;
use Avpvh\PrintWebsite\Twine\orm\managers\PostWrapperManager;
use \WP_Query;

/**
 * Class DesignManager
 * @package Avpvh\PrintWebsite\orm\managers
 */
class DesignManager extends PostWrapperManager
{
    /**
     * @var string
     */
    protected $class_to_instantiate = 'Avpvh\PrintWebsite\orm\entities\Design';

    /**
     * @var string
     */
    protected $cap_slug = 'avpvh_prwb_design';

    /**
     * @param string $format slug of format
     */
    public function getDesignsForFormat($format){
        // get all the designs for this format
        // including which format is actually in-use
        $wp_query_args = [
            // Sorry, I'm storing the design on a metakey. (Ya maybe we could store them on a custom table too).
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
            'meta_query' => [
                [
                    'key' => Design::META_PREFIX . 'format',
                    'value' => $format,
                ],
            ],
        ];
        return $this->getAll(new WP_Query($wp_query_args));
    }
}
