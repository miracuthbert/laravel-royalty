<?php

namespace Miracuthbert\Royalty\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Miracuthbert\Royalty\Models\Point;

class RoyaltyActions extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'royalty:actions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show a list of actions and their corresponding points in database';

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
        $startTime = microtime(true);

        $path = $this->files->allFiles(
            app_path(str_replace('\\', DIRECTORY_SEPARATOR, config('royalty.point.actions_path')))
        );

        $points = Point::get(['id', 'name', 'points', 'key'])->map(function ($action) use ($path) {
            return $this->getActionFile($action, $path);
        });

        $this->table(['id', 'key', 'points', 'name', 'file'], $points);

        $runTime = round(microtime(true) - $startTime, 2);

        $this->line("<info>Run in:</info> {$runTime} seconds");
    }

    /**
     * Get an action's file.
     *
     * @param $action
     * @param $path
     * @return array
     */
    protected function getActionFile($action, $path)
    {
        $file = Collection::make($path)->first(function ($actPath) use ($action) {
            $contents = $this->files->get($actPath);

            if (mb_stristr($contents, '\'' . $action->key . '\'')) {
                return true;
            }
        });

        $name = '?';

        if ($file) {
            $actPath = Arr::last(explode(DIRECTORY_SEPARATOR, $file));
            $name = str_replace('.php', '', basename($actPath));
        }

        return [$action->id, $action->key, $action->points, $action->name, '<info>' . $name . '</info>'];
    }
}
