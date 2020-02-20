<?php

declare(strict_types=1);

namespace App\Asset;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

final class VersionStrategy implements VersionStrategyInterface
{
    private $manifestPath;

    /**
     * @var string[]
     */
    private $hashes;

    public function __construct(string $manifestPath)
    {
        $this->manifestPath = $manifestPath;
    }

    public function getVersion($path)
    {
        if (!is_array($this->hashes)) {
            $this->hashes = $this->loadManifest();
        }

        if (empty($this->hashes)) {
            return '';
        }

        return isset($this->hashes[$path]) ? $this->hashes[$path] : '';
    }

    public function applyVersion($path)
    {
        $version = $this->getVersion($path);

        return ('' === $version) ? $path : $version;
    }

    private function loadManifest()
    {
        if (file_exists($this->manifestPath) && ($manifestContent = file_get_contents($this->manifestPath))) {
            return json_decode($manifestContent, true);
        }

        return [];
    }
}
