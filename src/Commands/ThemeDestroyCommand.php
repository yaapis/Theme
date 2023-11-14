<?php

namespace YAAP\Theme\Commands;

use Symfony\Component\Console\Input\InputArgument;

/**
 * Class ThemeDestroyCommand.
 */
class ThemeDestroyCommand extends BaseThemeCommand
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
    protected $description = 'Remove existing theme';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // The theme is not exists.
        $directoryDoesNotExists = !$this->directoryExists();
        if ($directoryDoesNotExists) {
            $this->error('Theme "' . $this->getTheme()->getName() . '" does not exist.');

            return self::FAILURE;
        }

        if ($this->confirm('Are you sure you want to permanently delete?')) {
            // Delete permanent.
            $this->files->deleteDirectory($this->getTheme()->getRootDirectoryPath(), false);
            $this->files->deleteDirectory($this->getAssetsPath(), false);

            $this->info('Theme "' . $this->getTheme()->getName() . '" has been destroyed.');
        }

        return self::SUCCESS;
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            new InputArgument('name', InputArgument::REQUIRED, 'Name of the theme to generate.'),
        ];
    }
}
