<?php

namespace YAAP\Theme\Commands;

use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use YAAP\Theme\Facades\ThemeLoader;
use YAAP\Theme\ThemeInfo;

/**
 * Class BaseThemeCommand.
 */
abstract class BaseThemeCommand extends Command
{
    /**
     * Theme info.
     */
    protected ThemeInfo $themeInfo;

    /**
     * Repository config.
     */
    protected Repository $config;

    /**
     * Filesystem.
     */
    protected Filesystem $files;

    /**
     * Create a new command instance.
     */
    public function __construct(Repository $config, Filesystem $files)
    {
        $this->config = $config;

        $this->files = $files;

        parent::__construct();
    }

    protected function directoryExists(): bool
    {
        return $this->files->isDirectory($this->getTheme()->getRootDirectoryPath());
    }

    /**
     * Get assets writable path.
     */
    protected function getAssetsPath(string $pathInTheme = null): string
    {
        $rootPath = $this->config->get('theme.assets_path', 'themes');

        return rtrim(public_path($rootPath) . '/' . $this->getTheme()->getName() . '/' . $pathInTheme, '/');
    }

    /**
     * Get the theme name.
     */
    protected function getTheme(): ThemeInfo
    {
        return $this->themeInfo ??= new ThemeInfo(
            $this->argument('name'),
            ThemeLoader::themesPath(),
        );
    }
}
