<?php

namespace Miracuthbert\Royalty;

use Illuminate\Support\ServiceProvider;
use Miracuthbert\Royalty\Console\RoyaltyAction;
use Miracuthbert\Royalty\Console\RoyaltyActions;
use Miracuthbert\Royalty\Console\RoyaltySetup;

class RoyaltyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();

        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishing();

        $this->registerCommands();
    }

    /**
     * Setup configuration for the package.
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-royalty.php', 'royalty'
        );
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {

            // publish config
            $this->publishes([
                __DIR__ . '/../config/laravel-royalty.php' => config_path('royalty.php'),
            ], Royalty::ROYALTY_CONFIG);

            // publish migrations
            $this->publishes([
                __DIR__ . '/../database/migrations/create_points_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_points_table.php'),
                __DIR__ . '/../database/migrations/create_point_user_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', strtotime('+2 seconds')) . '_create_point_user_table.php'),
            ], Royalty::ROYALTY_MIGRATIONS);

            // publish components
            $this->publishes([
                __DIR__ . '/../resources/js/components/RoyaltyBadge.vue.stub' => resource_path('js/components/royalty/RoyaltyBadge.vue'),
            ], Royalty::ROYALTY_COMPONENTS);
        }
    }

    /**
     * Register the package's commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RoyaltySetup::class,
                RoyaltyAction::class,
                RoyaltyActions::class,
            ]);
        }
    }
}
