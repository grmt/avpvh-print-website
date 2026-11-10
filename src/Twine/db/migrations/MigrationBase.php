<?php

namespace Avpvh\PrintWebsite\Twine\db\migrations;

/**
 * Class MigrationBase
 * @package Avpvh\PrintWebsite\Twine\db\migrations
 */
abstract class MigrationBase
{
    /**
     * Performs the migration
     * @return bool
     */
    abstract public function perform();
}
