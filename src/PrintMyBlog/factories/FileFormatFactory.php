<?php

namespace Avpvh\PrintWebsite\factories;

use Avpvh\PrintWebsite\entities\FileFormat;
use Avpvh\PrintWebsite\system\Context;

/**
 * Class FileFormatFactory
 * @package Avpvh\PrintWebsite\factories
 */
class FileFormatFactory
{
    /**
     * @param array $args
     * @return FileFormat
     */
    public function create($args)
    {
        return Context::instance()->useNew(
            'Avpvh\PrintWebsite\entities\FileFormat',
            [$args]
        );
    }
}
