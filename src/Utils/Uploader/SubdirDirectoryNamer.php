<?php

namespace App\Utils\Uploader;

use Doctrine\Common\Util\ClassUtils;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\ConfigurableInterface;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

/**
 * Class SubdirDirectoryNamer for determining directories for uploaded files
 *
 * @author Fondative <devteam@fondative.com>
 */
class SubdirDirectoryNamer implements DirectoryNamerInterface, ConfigurableInterface
{
    protected $directories;

    /**
     * @param array $options Options for this namer. The following options are accepted:
     *                       - directories: List of directory for each entity
     */
    public function configure(array $options)
    {
        $this->directories = $options['directories'];
    }

    /**
     * {@inheritdoc}
     */
    public function directoryName($object, PropertyMapping $mapping): string
    {
        $classDirectories = $this->directories[ClassUtils::getClass($object)] ?? null;

        return is_array($classDirectories) ? $classDirectories[$mapping->getFilePropertyName()] ?? null : $classDirectories;
    }
}