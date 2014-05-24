<?php namespace YAAP\Theme;

use Illuminate\Container\Container;
use Illuminate\View\ViewFinderInterface;

/**
 * Class Theme
 * @package YAAP\Theme
 */
class Theme
{

    /**
     * The IoC Container
     *
     * @var Container
     */
    protected $app;

    /**
     * Default view finder
     *
     */

    protected $finder;

    /**
     * theme config
     *
     */
    protected $config;

    /**
     * parent themes
     *
     */
    protected $parents;

    /**
     *  cache for paths
     *
     */
    protected $cache;

    /**
     * Build a new Theme manager
     *
     * @param Container $app
     * @param ViewFinderInterface $finder
     */
    public function __construct(Container $app, ViewFinderInterface $finder)
    {

        $this->app = $app;

        $this->finder = $finder;
    }


    /**
     *
     * Initialize a theme by name
     * @param $theme
     */
    public function init($theme)
    {

        // read theme path
        $path = $this->app['config']->get('theme::path', app_path('views/themes'));

        //init config
        $this->config = include($path . '/' . $theme . '/config.php');


        // theme parents
        $this->parents = array();

        while (!empty($theme)) {

            // add theme's root folder
            $this->finder->addLocation($path . '/' . $theme);

            // add folder with views
            $this->finder->addLocation($path . '/' . $theme . '/views');


            // read theme config
            $current_theme_config = include($path . '/' . $theme . '/config.php');

            $theme = array_get($current_theme_config, 'parent_theme');

            if (!empty($theme)) {
                $this->parents[] = $theme;
            }
        }

    }

    /**
     * Generate an asset path for current theme.
     *
     * @param $path
     * @param null $secure
     * @return mixed
     */
    public function asset($path, $secure = null)
    {

        $full_path = $this->_asset_full_path($path);

        return $this->app['url']->asset($full_path, $secure);

    }

    /**
     * Return filemtime of given asset or null if asset doesn't exists
     *
     * @param $path
     * @return int|null
     */
    public function asset_version($path)
    {

        $full_path = $this->_asset_full_path($path);

        $file_path = public_path($full_path);

        if (file_exists($file_path)) {
            return filemtime($file_path);
        }

        return null;
    }

    /**
     *
     * Try to find asset file in current theme or in parents
     *
     * @param $path
     * @return string
     */
    private function _asset_full_path($path)
    {

        $path = trim($path, '/');

        // alredy processed
        if (isset($this->cache[$path])) {
            return $this->cache[$path];
        }

        $assets_path = trim($this->app['config']->get('theme::assets_path', 'assets/themes'), '/');

        $name = array_get($this->config, 'name');

        $full_path = $assets_path . '/' . $name . '/' . $path;

        // theme has this asset
        if (!file_exists(public_path($full_path))) {

            $found = false;

            // loop over parents
            foreach ($this->parents as $parent) {

                $full_path = $assets_path . '/' . $parent . '/' . $path;

                if (file_exists(public_path($full_path))) {
                    $found = true;
                    break;
                }
            }

            // in case of failure to find asset - return default theme asset
            // (404 error will be signal of promlems)
            if (!$found) {
                $full_path = $assets_path . '/' . $name . '/' . $path;
            }

        }

        $this->cache[$path] = $full_path;

        return $full_path;
    }

}
 