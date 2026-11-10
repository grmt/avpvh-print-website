<?php

namespace Avpvh\PrintWebsite\db\migrations;

use Avpvh\PrintWebsite\Twine\db\migrations\MigrationManagerBase;

/**
 * Class MigrationManager
 * @package Avpvh\PrintWebsite\db\migrations
 */
class MigrationManager extends MigrationManagerBase
{

    /**
     * @return string[]
     */
    public function getMigrationInfos()
    {
        return [
            '3.2.3' => 'Avpvh\PrintWebsite\db\migrations\Migration3_2_3',
        ];
    }
}
