<?php

namespace Avpvh\PrintWebsite\db\migrations;

use Avpvh\PrintWebsite\system\CustomPostTypes;
use Avpvh\PrintWebsite\Twine\db\migrations\MigrationBase;

// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
/**
 * Class Migration3_2_3
 * @package Avpvh\PrintWebsite\db\migrations
 */
class Migration3_2_3 extends MigrationBase
{
    /**
     * @return bool
     */
    public function perform()
    {
        global $wpdb;
        // Only do this once on a singe request.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->update(
            $wpdb->posts,
            [
                'post_status' => 'private',
            ],
            [
                'post_type' => CustomPostTypes::CONTENT,
                'post_status' => 'publish',
            ]
        );
        return true;
    }
}
