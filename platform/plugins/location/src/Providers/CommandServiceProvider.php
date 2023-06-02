<?php

namespace Botble\Location\Providers;

use Botble\Location\Commands\MigrateLocationCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            MigrateLocationCommand::class,
        ]);
    }
}
