<?php namespace YAAP\Theme\Commands;

use Illuminate\Console\Command;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem as File;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class ThemeGeneratorCommand
 * @package YAAP\Theme\Commands
 */
class ThemeGeneratorCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'theme:create';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate theme structure';

	/**
	 * Repository config.
	 *
	 * @var Illuminate\Config\Repository
	 */
	protected $config;

	/**
	 * Filesystem
	 *
	 * @var Illuminate\Filesystem\Filesystem
	 */
	protected $files;

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Config\Repository $config
     * @param \Illuminate\Filesystem\Filesystem $files
     * @return \YAAP\Theme\Commands\ThemeGeneratorCommand
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
	public function fire()
	{
		// The theme is already exists.
		if ($this->files->isDirectory($this->getPath(null)))
		{
			return $this->error('Theme "'.$this->getTheme().'" is already exists.');
		}


		// Directories.
		$container = $this->config->get('theme::containerDir');

        $this->makeDir($this->getPath($container['layout']));
        $this->makeDir($this->getPath($container['partial']));
        $this->makeDir($this->getPath($container['view']));

        $this->makeFile($container['layout'].'/master.blade.php', $this->getTemplate('layout.blade'));
        $this->makeFile($container['partial'].'/header.blade.php', $this->getTemplate('header.blade'));
        $this->makeFile($container['partial'].'/footer.blade.php', $this->getTemplate('footer.blade'));
        $this->makeFile($container['view'].'/hello.blade.php', $this->getTemplate('view.blade'));



        //assets
        $this->makeDir($this->getAssetsPath('css'));
        $this->makeDir($this->getAssetsPath('js'));
        $this->makeDir($this->getAssetsPath('img'));


		// Generate inside config.
		$this->makeFile('config.php', $this->getTemplate('config'));

		$this->info('Theme "'.$this->getTheme().'" has been created.');
	}

    /**
     * Make directory.
     *
     * @param $path
     * @return void
     */
	protected function makeDir($path)
	{
		if ( ! $this->files->isDirectory($path))
		{
            $this->files->makeDirectory($path, 0777, true);
		}
	}

	/**
	 * Make file.
	 *
	 * @param  string $file
	 * @param  string $template
	 * @return void
	 */
	protected function makeFile($file, $template = null)
	{
		if ( ! $this->files->exists($this->getPath($file)))
		{
			$content = $this->getPath($file);

			$this->files->put($content, $template);
		}
	}

	/**
	 * Get root writable path.
	 *
	 * @param  string $path
	 * @return string
	 */
	protected function getPath($path)
	{
		$rootPath = $this->config->get('theme::path', app_path('views/themes'));

		return $rootPath.'/'.strtolower($this->getTheme()).'/' . $path;
	}

    /**
     * Get assets writable path.
     *
     * @param  string $path
     * @param bool $absolute
     * @return string
     */
	protected function getAssetsPath($path, $absolute = true)
	{
        $rootPath = $this->config->get('theme::assets_path', 'assets/themes');

        if ($absolute)
            $rootPath = public_path($rootPath);

		return $rootPath.'/'.strtolower($this->getTheme()).'/' . $path;
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
	 * Get default template.
	 *
	 * @param  string $template
	 * @return string
	 */
	protected function getTemplate($template)
	{
		$path = realpath(__DIR__.'/../templates/'.$template.'.txt');

		return $this->files->get($path);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('name', InputArgument::REQUIRED, 'Name of the theme to generate.'),
		);
	}


}