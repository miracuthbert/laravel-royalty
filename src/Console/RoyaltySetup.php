<?php

namespace Miracuthbert\Royalty\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputOption;

class RoyaltySetup extends Command
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'royalty:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the files required to use Royalty';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        $this->info('Publishing config...');

        $this->call('vendor:publish', [
            '--provider' => 'Miracuthbert\Royalty\RoyaltyServiceProvider',
            '--tag' => 'royalty-config',
            '--force' => $this->hasOption('force') && $this->option('force')
        ]);

        if ($publishMigrations = $this->canPublishMigrations()) {
            $this->info('Publishing migrations...');

            $this->call('vendor:publish', [
                '--provider' => 'Miracuthbert\Royalty\RoyaltyServiceProvider',
                '--tag' => 'royalty-migrations',
                '--force' => $publishMigrations,
            ]);
        }

        if ($this->hasOption('components') && $this->option('components')) {
            $this->info('Publishing components...');

            $this->call('vendor:publish', [
                '--provider' => 'Miracuthbert\Royalty\RoyaltyServiceProvider',
                '--tag' => 'royalty-components',
                '--force' => $this->hasOption('force') && $this->option('force')
            ]);
        }

        $this->info('Update the keys in "config/royalty.php" before migrating your database.');
    }

    /**
     * Determine if migrations can be published.
     *
     * @return bool
     */
    protected function canPublishMigrations()
    {
        if ($this->hasOption('force') && $this->option('force')) {
            return true;
        }

        $path = Collection::make($this->files->files(database_path('migrations')))->map(function ($migration){
            return $migration->getFilename();
        })->toArray();

        if (count(preg_grep('/points|point_user/', $path)) >= 2) {
            return false;
        }

        return true;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['components', null, InputOption::VALUE_NONE, 'Publish the included components'],
            ['force', null, InputOption::VALUE_NONE, 'Setup the files for Royalty even if they already exists'],
        ]);
    }
}
