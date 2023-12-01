<?php

declare(strict_types=1);

namespace YAAP\Theme;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ThemeInfo
{
    private string $name;
    private string $themesRootPath;
    private array $configs = [];

    public function __construct(
        string $name,
        string $themesRoot
    ) {
        $this->name = (string)Str::of($name)->snake()->lower();
        $this->themesRootPath = $themesRoot;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConfigs(): array
    {
        return $this->configs;
    }

    public function getParentThemeName(): ?string
    {
        return $this->configs['inherit'] ?? null;
    }

    /**
     * Get root theme writable path.
     */
    public function getRootDirectoryPath(): string
    {
        return "{$this->themesRootPath}/{$this->name}";
    }

    /**
     * Get theme item path.
     *
     * @param string|array<string> $pathParts
     */
    public function pathForItem($pathParts = null): string
    {
        $pathParts = Arr::wrap($pathParts);

        $folders = empty($pathParts) ? '' : implode('/', $pathParts);

        return $this->getRootDirectoryPath() . '/' . $folders;
    }

    /**
     * Get theme config path.
     */
    public function getConfigPath(): string
    {
        return $this->pathForItem('config.php');
    }

    /**
     * Get theme config path.
     */
    public function readConfig(): void
    {
        $this->configs = $this->safeReadConfig($this->getConfigPath());
    }

    /**
     * Safe read config if exists.
     */
    private function safeReadConfig(string $path): array
    {
        if (file_exists($path)) {
            return include $path;
        }

        return [];
    }
}
