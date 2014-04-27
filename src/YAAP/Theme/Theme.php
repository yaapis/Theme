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

    protected $finder;

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


    public function init($name)
    {

        if (!empty($name)) {

            $path = $this->app['config']->get('theme::path', 'views/themes');

            // add theme's root folder
            $this->finder->addLocation($path . '/' . $name);

            // add folder with views
            $this->finder->addLocation($path . '/' . $name . '/views');

        }

    }

}
 