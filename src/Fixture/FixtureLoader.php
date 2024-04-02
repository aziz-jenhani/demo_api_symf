<?php

namespace App\Fixture;

use Nelmio\Alice\Throwable\LoadingThrowable;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;

class FixtureLoader
{
    private string $fixtureDir;
    private NativeLoader $loader;

    public function __construct(string $projectDir)
    {
        $this->loader = new NativeLoader();
        $this->fixtureDir = $projectDir . '/fixtures/';
    }

    /**
     * @param string $file
     * @return array<object>
     * @throws LoadingThrowable
     */
    public function loadFile(string $file): array
    {
        $objectSet = $this->loader->loadFile($this->fixtureDir . $file);

        /** @var array<object> $objects */
        $objects =  $objectSet->getObjects();

        return $objects;
    }

    /**
     * @param array<string> $files
     * @return array<object>
     * @throws LoadingThrowable
     */
    public function loadFiles(array $files): array
    {
        $objectSet = $this->loader->loadFiles($files);

        /** @var array<object> $objects */
        $objects =  $objectSet->getObjects();

        return $objects;
    }

    /**
     * Gets the list of files that can be found in the given path.
     *
     * @param string $path
     * @return array<string>
     */
    public function locateFiles(string $path): array
    {
        $files = Finder::create()->files()
            ->in($this->fixtureDir . $path)->depth(0)
            ->name('/.*\.ya?ml$/i')
            ->sortByType()
            ->getIterator();

        return array_map(
            function (SplFileInfo $file) {
                return $file->getRealPath();
            },
            iterator_to_array($files)
        );
    }
}
