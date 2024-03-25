<?php

namespace YAAP\Theme\Commands;

use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\text;

/**
 * Class ThemeGeneratorCommand.
 */
class ThemeGeneratorCommand extends BaseThemeCommand implements PromptsForMissingInput
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
        $message = $this->validateValue($this->argument('name'));
        if ($message) {
            $this->error($message);

            return self::FAILURE;
        }

        // Check if the theme is already exists.
        if (!$this->canGenerateTheme()) {
            return self::FAILURE;
        }

        // Directories.
        $dirMapping = $this->config->get('theme.containerDir');
        $this->containerFolder = [
            'assets' => $dirMapping['assets'] ?? 'assets',
            'lang' => $dirMapping['lang'] ?? 'lang',
            'layout' => $dirMapping['layout'] ?? 'views/layouts',
            'partial' => $dirMapping['partial'] ?? 'views/partials',
            'view' => $dirMapping['view'] ?? 'views',
        ];

        $this->generateThemeStructure();
        if ($this->isVite()) {
            $this->generateViteAssets();

            $this->info('Append next lint to vite.config.js');
            $this->info($this->fromTemplate('vite/vite.config.stub'));
        } else {
            $this->generatePublicFolders();
            $this->generateMixAssets();

            $this->info('Seeding webpack.mix.js');
            $this->files->append(base_path('webpack.mix.js'), $this->fromTemplate('laravel-mix/mix.stub'));
        }

        return self::SUCCESS;
    }

    protected function generatePublicFolders(): void
    {
        // public assets
        $this->makeAssetsFile('css/.gitkeep');
        $this->makeAssetsFile('js/.gitkeep');
        $this->makeAssetsFile('fonts/.gitkeep');
    }

    protected function generateThemeStructure(): void
    {
        // Generate inside config.
        $this->makeFile('config.php', $this->fromTemplate('common/config/config.php'));

        $this->makeFile(
            $this->containerFolder['lang'] . '/en/labels.php',
            $this->fromTemplate('common/lang/labels.php')
        );

        $this->makeFile(
            $this->containerFolder['partial'] . '/header.blade.php',
            $this->fromTemplate('common/views/partials/header.blade.php')
        );
        $this->makeFile(
            $this->containerFolder['partial'] . '/footer.blade.php',
            $this->fromTemplate('common/views/partials/footer.blade.php')
        );


        if ($this->isVite()) {
            $this->makeFile(
                $this->containerFolder['layout'] . '/master.blade.php',
                $this->fromTemplate('vite/views/layouts/master.blade.php')
            );
            $this->makeFile(
                $this->containerFolder['layout'] . '/master.blade.php',
                $this->fromTemplate('vite/views/layouts/master.blade.php')
            );
            $this->writeContent(
                app_path('View/Components/AppLayout.php'),
                $this->fromTemplate('vite/app/AppLayout.stub')
            );
            $this->makeFile(
                $this->containerFolder['view'] . '/hello.blade.php',
                $this->fromTemplate('vite/views/hello.blade.php')
            );
        } else {
            $this->makeFile(
                $this->containerFolder['layout'] . '/master.blade.php',
                $this->fromTemplate('laravel-mix/views/layouts/master.blade.php')
            );
            $this->makeFile(
                $this->containerFolder['view'] . '/hello.blade.php',
                $this->fromTemplate('laravel-mix/views/hello.blade.php')
            );
        }
    }

    protected function generateMixAssets(): void
    {
        // frontend sources
        $assets = $this->containerFolder['assets'];

        $this->makeFile("{$assets}/img/favicon.png", $this->fromTemplate('common/favicon.png'));
        $this->makeFile("{$assets}/sass/_variables.scss", $this->fromTemplate('common/scss/_variables.scss'));
        $this->makeFile("{$assets}/sass/app.scss", $this->fromTemplate('common/scss/app.scss'));
        $this->makeFile("{$assets}/fonts/.gitkeep");

        $this->makeFile("{$assets}/js/app.js", $this->fromTemplate('laravel-mix/js/app.js'));
    }

    protected function isVite(): bool
    {
        return $this->argument('assets') === 'vite';
    }

    protected function generateViteAssets(): void
    {
        // frontend sources
        $assets = $this->containerFolder['assets'];

        $this->makeFile("{$assets}/img/favicon.png", $this->fromTemplate('common/favicon.png'));

        $this->makeFile("{$assets}/sass/_variables.scss", $this->fromTemplate('common/scss/_variables.scss'));
        $this->makeFile("{$assets}/sass/app.scss", $this->fromTemplate('common/scss/app.scss'));
        $this->makeFile("{$assets}/fonts/.gitkeep");

        $this->makeFile("{$assets}/js/app.js", $this->fromTemplate('vite/js/app.js'));
        $this->makeFile("{$assets}/js/bootstrap.js", $this->fromTemplate('vite/js/bootstrap.js'));
    }

    protected function canGenerateTheme(): bool
    {
        $directoryExists = $this->directoryExists();
        if (!$directoryExists) {
            return true;
        }

        $name = $this->getTheme()->getName();

        $this->error("Theme \"{$name}\" already exists.");

        $forceOverride = $this->option('force')
            || $this->confirm("Are you sure want to override \"{$name}\" theme folder?");

        if ($forceOverride) {
            $this->warn("Overriding Theme \"{$name}\".");
        } else {
            $this->error("Generation of Theme \"{$name}\" has been canceled.");
        }

        return $forceOverride;
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
    protected function makeFile(string $file, string $content = ''): void
    {
        $this->writeContent($this->getTheme()->pathForItem($file), $content);
    }

    /**
     * Make file.
     */
    protected function makeAssetsFile(string $file, string $template = ''): void
    {
        $this->writeContent($this->getAssetsPath($file), $template);
    }

    protected function writeContent(string $filePath, string $content): void
    {
        if (!$this->files->exists($filePath) || $this->option('force')) {
            $this->makeDir(pathinfo($filePath, PATHINFO_DIRNAME));

            $this->files->put($filePath, $content);
        }
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
        $templatesPath = realpath(__DIR__ . '/../../stubs');

        return "{$templatesPath}/{$templateName}";
    }

    private function validateValue($value): ?string
    {
        return match (true) {
            empty($value) => 'Name is required.',

            !empty(
                preg_match(
                    '/[^a-zA-Z0-9\-_\s]/',
                    $value,
                )
            ) => 'Name must be alphanumeric, dash, space or underscore.',

            $this->files->isDirectory(
                $this->makeTheme($value)->getRootDirectoryPath()
            ) => "Theme \"{$value}\" already exists.",

            default => null,
        };
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            new InputArgument('name', InputArgument::REQUIRED, 'A name of the new theme'),
            new InputArgument('assets', InputArgument::OPTIONAL, 'A type of assets to install', 'vite', ['vite', 'mix']
            ),
        ];
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => fn () => text(
                label: 'What is a name of the new theme?',
                default: 'default',
                validate: fn ($value) => $this->validateValue($value)
            ),
        ];
    }

    protected function getOptions(): array
    {
        return [
            new InputOption('force', null, InputOption::VALUE_NONE, 'Force create theme with same name'),
        ];
    }
}
