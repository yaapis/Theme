<?php

namespace YAAP\Theme\Commands;

use Illuminate\Config\Repository;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class ThemeDestroyCommand.
 */
class ThemeDestroyCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:destroy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove exsisting theme';

    /**
     * Repository config.
     *
     * @var Repository
     */
    protected $config;

    /**
     * Filesystem.
     *
     * @var File
     */
    protected $files;

    /**
     * Create a new command instance.
     */
    public function __construct(Repository $config, File $files)
    {
        $this->config = $config;

        $this->files = $files;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // The theme is not exists.
        if (!$this->files->isDirectory($this->getPath(null))) {
            $this->error('Theme "' . $this->getTheme() . '" is not exists.');

            return;
        }

        $themePath = $this->getPath(null);

        $assetsPath = $this->getAssetsPath(null);

        if ($this->confirm('Are you sure you want to permanently delete? [yes|no]')) {
            // Delete permanent.
            $this->files->deleteDirectory($themePath, false);
            $this->files->deleteDirectory($assetsPath, false);

            $this->info('Theme "' . $this->getTheme() . '" has been destroyed.');
        }
    }

    /**
     * Get root writable path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function getPath($path)
    {
        $rootPath = $this->config->get('theme::path', base_path('themes'));

        return $rootPath . '/' . strtolower($this->getTheme()) . '/' . $path;
    }

    /**
     * Get assets writable path.
     *
     * @param string $path
     * @param bool   $absolute
     *
     * @return string
     */
    protected function getAssetsPath($path, $absolute = true)
    {
        $rootPath = $this->config->get('theme::assets_path', 'themes');

        if ($absolute) {
            $rootPath = public_path($rootPath);
        }

        return $rootPath . '/' . strtolower($this->getTheme()) . '/' . $path;
    }

    /**
     * Get the theme name.
     *
     * @return string
     */
    protected function getTheme()
    {
        return strtolower($this->argument('name'));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Name of the theme to generate.'],
        ];
    }
}
