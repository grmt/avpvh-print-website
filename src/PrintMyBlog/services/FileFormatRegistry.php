<?php

namespace Avpvh\PrintWebsite\services;

use Avpvh\PrintWebsite\entities\FileFormat;
use Avpvh\PrintWebsite\factories\FileFormatFactory;

/**
 * Class FileFormatRegistry
 * @package Avpvh\PrintWebsite\services
 */
class FileFormatRegistry
{

    /**
     * @var FileFormatFactory
     */
    protected $factory;

    /**
     * Called by Context.
     * @param FileFormatFactory $factory
     */
    public function inject(FileFormatFactory $factory)
    {
        $this->factory = $factory;
    }
    /**
     * @var FileFormat[]
     */
    protected $formats;

    /**
     * @param string $slug
     *
     * @return FileFormat
     */
    public function getFormat($slug)
    {
        if (isset($this->formats[$slug])) {
            return $this->formats[$slug];
        }
        return null;
    }
    /**
     * Gets the declared project formats
     * @return FileFormat[]
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * @param string $slug
     * @param array $args
     */
    public function registerFormat($slug, $args)
    {
        $format = $this->factory->create($args);
        $format->constructFinalize($slug);
        $this->formats[$slug] = $format;
    }
}
