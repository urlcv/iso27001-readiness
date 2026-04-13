<?php

declare(strict_types=1);

namespace URLCV\Iso27001Readiness\Laravel;

use Illuminate\Support\ServiceProvider;

class Iso27001ReadinessServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'iso27001-readiness');
    }
}
