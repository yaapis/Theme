<?php

namespace YAAP\Theme\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ThemeGeneratorCommand.
 */
class ThemeGeneratorCommand extends BaseThemeCommand
{
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

    protected array $containerFolder;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Check if the theme is already exists.
        if (!$this->canGenerateTheme()) {
            return self::FAILURE;
        }

        // Directories.
        $this->containerFolder = $this->config->get('theme.containerDir');

        $this->generateThemeStructure();
        $this->generateAssets();
        $this->generatePublicFolders();

        // mix
        if ($this->option('with-mix')) {
            $this->info('Seeding webpack.mix.js');
            $this->files->append(base_path('webpack.mix.js'), $this->fromTemplate('mix.js'));
        }

        $this->info('Theme "' . $this->getTheme()->getName() . '" has been created.');

        return self::SUCCESS;
    }

    protected function generatePublicFolders(): void
    {
        // public assets
        $this->makeAssetsFile('css/.gitkeep');
        $this->makeAssetsFile('js/.gitkeep');
        $this->makeAssetsFile('img/.gitkeep');
        $this->makeAssetsFile('fonts/.gitkeep');
    }

    protected function generateThemeStructure(): void
    {
        // Generate inside config.
        $this->makeFile('config.php', $this->fromTemplate('config.php'));

        $this->makeFile(
            $this->containerFolder['layout'] . '/master.blade.php',
            $this->fromTemplate('layout.blade.php')
        );

        $this->makeFile(
            $this->containerFolder['partial'] . '/header.blade.php',
            $this->fromTemplate('header.blade.php')
        );
        $this->makeFile(
            $this->containerFolder['partial'] . '/footer.blade.php',
            $this->fromTemplate('footer.blade.php')
        );

        $this->makeFile($this->containerFolder['view'] . '/hello.blade.php', $this->fromTemplate('view.blade.php'));

        $this->makeFile($this->containerFolder['lang'] . '/en/labels.php', $this->fromTemplate('lang.php'));
    }

    protected function generateAssets(): void
    {
        // frontend sources
        $assets = $this->containerFolder['assets'];

        $this->makeFile("{$assets}/sass/_variables.scss", $this->fromTemplate('_variables.scss'));
        $this->makeFile("{$assets}/sass/app.scss", $this->fromTemplate('app.scss'));
        $this->makeFile("{$assets}/js/app.js", $this->fromTemplate('app.js.stub'));
        $this->makeFile("{$assets}/img/favicon.png", $this->fromTemplate('favicon.png'));
        $this->makeFile("{$assets}/fonts/.gitkeep");
    }

    protected function canGenerateTheme(): bool
    {
        $themeInfo = $this->getTheme();

        $directoryExists = $this->directoryExists();
        if ($directoryExists) {
            $this->error('Theme "' . $themeInfo->getName() . '" is already exists.');
        }

        if ($this->option('force')) {
            return $this->confirm('Are you sure want to override existing theme folder?');
        }

        return !$directoryExists;
    }

    /**
     * Make directory.
     */
    protected function makeDir(string $path): void
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true);
        }
    }

    /**
     * Make file.
     */
    protected function makeFile(string $file, string $content = '', bool $assets = false): void
    {
        $filePath = $assets
            ? $this->getAssetsPath($file)
            : $this->getTheme()->pathForItem($file);

        if (!$this->files->exists($filePath) || $this->option('force')) {
            $this->makeDir(pathinfo($filePath, PATHINFO_DIRNAME));

            $this->files->put($filePath, $content);
        }
    }

    /**
     * Make file.
     */
    protected function makeAssetsFile(string $file, string $template = ''): void
    {
        $this->makeFile($file, $template, true);
    }

    /**
     * Get template content.
     */
    protected function fromTemplate(string $templateName, array $replacements = []): string
    {
        $templatePath = $this->getTemplatePath($templateName);

        $replacements = array_merge($replacements, [
            '%theme_name%' => $this->getTheme()->getName(),
        ]);

        $content = $this->files->get($templatePath);

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $content
        );
    }

    protected function getTemplatePath(string $templateName): string
    {
        $templatesPath = realpath(__DIR__ . '/../templates');

        return "{$templatesPath}/{$templateName}";
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            new InputArgument('name', InputArgument::REQUIRED, 'A name of the new theme.'),
        ];
    }

    protected function getOptions(): array
    {
        return [
            new InputOption('with-mix', null, InputOption::VALUE_NONE, 'Seed webpack.mix.js with themes specific'),
            new InputOption('force', null, InputOption::VALUE_NONE, 'Force create theme with same name'),
        ];
    }
}
