<?php

namespace YAAP\Theme;

use Illuminate\Container\Container;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\View\ViewFinderInterface;
use YAAP\Theme\Exceptions\ThemeException;

class ThemeLoader
{
    /**
     * The IoC Container.
     */
    protected Container $app;

    /**
     * Default view finder.
     */
    protected ViewFinderInterface $viewFinder;

    /**
     * Default locale loader.
     */
    protected Loader $localeLoader;

    /**
     * theme config.
     */
    protected array $config;

    /**
     * parent themes.
     */
    protected array $parents;

    /**
     * Build a new Theme manager.
     */
    public function __construct(Container $app, ViewFinderInterface $finder, Loader $localeLoader)
    {
        $this->app = $app;

        $this->viewFinder = $finder;

        $this->localeLoader = $localeLoader;
    }

    /**
     * Initialize a theme by name.
     *
     * @throws ThemeException
     */
    public function init(string $themeName): void
    {
        if (empty($themeName)) {
            throw new ThemeException('Theme name should not be empty.');
        }

        $baseTheme = new ThemeInfo(
            $themeName,
            $this->themesPath()
        );

        $currentTheme = $baseTheme;

        while (true) {
            if (!is_dir($currentTheme->getRootDirectoryPath())) {
                throw new ThemeException("Theme folder {$currentTheme->getName()} not found.");
            }

            // init config
            $currentTheme->readConfig();

            // add theme's root folder
            // root needed for layouts => /layouts/master (layouts.master)
            $this->viewFinder->addLocation($currentTheme->getRootDirectoryPath());

            // add folder with views
            // root for other views
            $views = config('theme.containerDir.view', 'views');
            $this->viewFinder->addLocation($currentTheme->pathForItem($views));

            // Check inheritance theme
            $inheritThemeName = $currentTheme->getParentThemeName();

            // if theme has no parent, break the loop
            if (empty($inheritThemeName) || !is_string($inheritThemeName)) {
                break;
            }

            // if theme has parent, add it to the list
            // and load on next loop iteration
            $currentTheme = new ThemeInfo(
                $inheritThemeName,
                $this->themesPath()
            );
        }

        // Register lang files for base theme with namespace
        $this->localeLoader->addNamespace($themeName, $baseTheme->pathForItem('lang'));
    }

    /**
     * Returns the list of available themes names in an array.
     *
     * @return array<int, ThemeInfo>
     */
    public function getList(): array
    {
        $path = $this->themesPath();

        $list = [];

        if (!file_exists($path)) {
            return $list;
        }

        $dir_list = dir($path);
        while (false !== ($entry = $dir_list->read())) {
            $themeInfo = new ThemeInfo($entry, $path);
            if (!file_exists($themeInfo->getConfigPath())) {
                continue;
            }

            $list[] = $themeInfo;
        }

        return $list;
    }

    public function themesPath(): string
    {
        return rtrim($this->app['config']->get('theme.path', base_path('themes')), '/');
    }
}
