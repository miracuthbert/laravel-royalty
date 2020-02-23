<?php

namespace Miracuthbert\Royalty\Console;

use Illuminate\Console\ConfirmableTrait;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Miracuthbert\Royalty\Models\Point;
use Symfony\Component\Console\Input\InputOption;

class RoyaltyAction extends GeneratorCommand
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'royalty:action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new points action class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Point';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        if (($count = count($this->canCreatePointInDb())) === 1) {
            $this->warn('You need to pass both "--name" and "--points" options to create point in the database.');
            return;
        }

        parent::handle();

        $this->createPoint();
    }

    /**
     * Build the class with the given name.
     *
     * @param  string $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $key = $this->buildActionKey();

        return str_replace(
            [
                'DummyKey',
            ],
            [
                $key,
            ],
            parent::buildClass($name)
        );
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/action.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . config('royalty.point.actions_path');
    }

    /**
     * Build the action's key.
     *
     * @return string
     */
    protected function buildActionKey()
    {
        $name = $this->qualifyClass($this->getNameInput());

        if ($key = $this->option('key')) {
            $slug = Str::slug($key, '-');

            return $slug;
        }

        $slug = Str::slug(Str::snake(class_basename($name), '-'));

        return $slug;
    }

    /**
     * Determine if the action's point should be created in the database.
     *
     * @return array
     */
    protected function canCreatePointInDb()
    {
        // create point
        $optionNotNull = function ($option) {
            return $option !== null;
        };

        $dbOptions = Arr::only($this->options(), ['name', 'points']);

        $canCreatePointInDb = array_filter(array_map($optionNotNull, $dbOptions), function ($option) {
            return $option === true;
        });

        return $canCreatePointInDb;
    }

    /**
     * Create action point in database.
     *
     * @return void
     */
    protected function createPoint()
    {
        if (count($this->canCreatePointInDb()) === 2) {
            Point::firstOrCreate(['key' => $this->buildActionKey()],
                Arr::only($this->options(), ['name', 'points', 'description'])
            );

            $this->info($this->type . ' created in database.');
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['key', null, InputOption::VALUE_OPTIONAL, 'Overrides the generated key with the given one'],
            ['name', null, InputOption::VALUE_OPTIONAL, 'The action point name to be used in the database'],
            ['points', null, InputOption::VALUE_OPTIONAL, 'The amount of points to rewarded'],
            ['description', null, InputOption::VALUE_OPTIONAL, 'A short overview of what the point reward is'],
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the point already exists'],
        ]);
    }
}
