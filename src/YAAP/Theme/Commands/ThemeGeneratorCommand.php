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
	public function handle()
	{
		// The theme is already exists.
		if ($this->files->isDirectory($this->getPath(null)))
		{
			return $this->error('Theme "'.$this->getTheme().'" is already exists.');
		}


		// Directories.
		$container = $this->config->get('theme.containerDir');

        $this->makeDir($this->getPath($container['layout']));
        $this->makeDir($this->getPath($container['partial']));
        $this->makeDir($this->getPath($container['view']));


        $this->makeFile($container['layout'].'/master.blade.php', $this->getTemplate('layout.blade.php'));
        $this->makeFile($container['partial'].'/header.blade.php', $this->getTemplate('header.blade.php'));
        $this->makeFile($container['partial'].'/footer.blade.php', $this->getTemplate('footer.blade.php'));
        $this->makeFile($container['view'].'/hello.blade.php', $this->getTemplate('view.blade.php'));


        //lang
        $this->makeDir($this->getPath($container['lang']));
        $this->makeDir($this->getPath($container['lang'].'/en'));
        $this->makeFile($container['lang'].'/en/labels.php', $this->getTemplate('lang.php'));

        // frontend sources
        $this->makeDir($this->getPath($container['assets']));
        //sass
        $this->makeDir($this->getPath($container['assets'].'/sass'));
        $this->makeFile($container['assets'].'/sass/_variables.scss', $this->getTemplate('_variables.scss'));
        $this->makeFile($container['assets'].'/sass/app.scss', $this->getTemplate('app.scss'));

        //js
        $this->makeDir($this->getPath($container['assets'].'/js'));
        $this->makeFile($container['assets'].'/js/app.js', $this->getTemplate('app.js'));

        // img
        $this->makeDir($this->getPath($container['assets'].'/img'));
        $this->makeFile($container['assets'].'/img/favicon.png', $this->getTemplate('favicon.png'));

        // fonts
        $this->makeDir($this->getPath($container['assets'].'/fonts'));

        //public assets
        $this->makeDir($this->getAssetsPath('css'));
        $this->makeAssetsFile('css/.gitkeep', '');

        $this->makeDir($this->getAssetsPath('js'));
        $this->makeAssetsFile('js/.gitkeep', '');

        $this->makeDir($this->getAssetsPath('img'));
        $this->makeAssetsFile('img/.gitkeep', '');

        $this->makeDir($this->getAssetsPath('fonts'));
        $this->makeAssetsFile('fonts/.gitkeep', '');


		// Generate inside config.
		$this->makeFile('config.php', $this->getTemplate('config.php', ['%theme_name%' => $this->getTheme()]));

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
     * @param bool $assets
     * @return void
     */
	protected function makeFile($file, $template = null, $assets = false)
	{
		if ( ! $this->files->exists($this->getPath($file)))
		{
			$content = $assets ? $this->getAssetsPath($file, true) : $this->getPath($file);

			$this->files->put($content, $template);
		}
	}

    /**
     * Make file.
     *
     * @param  string $file
     * @param  string $template
     * @internal param bool $assets
     * @return void
     */
	protected function makeAssetsFile($file, $template = null)
	{
		$this->makeFile($file, $template, true);
	}

	/**
	 * Get root writable path.
	 *
	 * @param  string $path
	 * @return string
	 */
	protected function getPath($path)
	{
		$rootPath = $this->config->get('theme.path', base_path('themes'));

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
        $rootPath = $this->config->get('theme.assets_path', 'themes');

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
     * @param array $replacements
     * @return string
     */
    protected function getTemplate($template, $replacements = array())
    {

        $path = realpath(__DIR__ . '/../templates/' . $template);

        $content = $this->files->get($path);

        if (!empty($replacements)) {
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        }

        return $content;
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
