<?php

namespace Pharaonic\Laravel\Messages;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class MessagesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Migration Loading
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishes
        $this->publishes([
            __DIR__ . '/database/migrations/2021_02_01_000018_create_messages_table.php' => database_path('migrations/2021_02_01_000018_create_messages_table.php'),
        ], ['pharaonic', 'laravel-messages']);

        // Directive
        Blade::if('messageable', function ($minutes) {
            return Message::messageable(request(), $minutes);
        });
    }
}
