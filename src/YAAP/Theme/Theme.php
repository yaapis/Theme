<?php namespace YAAP\Theme;

use Illuminate\Container\Container;
use Illuminate\View\ViewFinderInterface;
use YAAP\Theme\Exceptions\ThemeException;
use Illuminate\Support\Arr;

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
     */
    protected $cache;

    /**
     *  current theme
     */
    protected $theme;

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
     * @throws ThemeException
     */
    public function init($theme)
    {
        if (empty($theme)) throw new ThemeException('Theme name should not be empty');

        $this->theme = $theme;

        // read theme path
        $path = $this->app['config']->get('theme.path', base_path('themes'));

        //init config
        $this->config = $this->_readConfig( $path . '/' . $theme . '/config.php');

        // theme parents
        $this->parents = array();

        while (!empty($theme)) {

            if (!is_dir($path . '/' . $theme)) throw new ThemeException('Theme '.$theme.' not found.');

            // add theme's root folder
            $this->finder->addLocation($path . '/' . $theme);

            // add folder with views
            $this->finder->addLocation($path . '/' . $theme . '/' . config('theme.containerDir.view'));

            // read theme config
            $current_theme_config = $this->_readConfig($path . '/' . $theme . '/config.php');

            $theme = Arr::get($current_theme_config, 'inherit');

            if (!empty($theme)) {
                $this->parents[] = $theme;
            }
        }


    }

    /**
     * Returns the list of available themes names in an array.
     *
     * @return array
     */
    public function getList()
    {
        // read theme path
        $path = $this->app['config']->get('theme.path', base_path('themes'));

        if (file_exists($path))
        {
            $dir_list = dir($path);
            while (false !== ($entry = $dir_list->read())) {
                if (file_exists($path . '/' . $entry . '/' . 'config.php'))
                    $list[] = $entry;
            }
        }

        return $list;

    }

    /**
     * @param $path
     * @return array|mixed
     */
    private function _readConfig($path){
        if (file_exists($path))
                return include($path);

        return array();
    }

}

