<?php

namespace Grilar\Installer\Providers;

use Grilar\Base\Events\FinishedSeederEvent;
use Grilar\Base\Events\UpdatedEvent;
use Grilar\Base\Facades\BaseHelper;
use Grilar\Base\Supports\ServiceProvider;
use Grilar\Base\Traits\LoadAndPublishDataTrait;
use Grilar\Installer\Http\Middleware\CheckIfInstalledMiddleware;
use Grilar\Installer\Http\Middleware\CheckIfInstallingMiddleware;
use Grilar\Installer\Http\Middleware\RedirectIfNotInstalledMiddleware;
use Carbon\Carbon;
use Illuminate\Routing\Events\RouteMatched;
use Throwable;

class InstallerServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this->setNamespace('packages/installer')
            ->loadHelpers()
            ->loadAndPublishConfigurations('installer')
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes()
            ->publishAssets();

        $this->app['events']->listen(RouteMatched::class, function () {
            if (defined('INSTALLED_SESSION_NAME')) {
                $router = $this->app->make('router');

                $router->middlewareGroup('install', [CheckIfInstalledMiddleware::class]);
                $router->middlewareGroup('installing', [CheckIfInstallingMiddleware::class]);

                $router->pushMiddlewareToGroup('web', RedirectIfNotInstalledMiddleware::class);
            }
        });

        try {
            $this->app['events']->listen([UpdatedEvent::class, FinishedSeederEvent::class], function () {
                BaseHelper::saveFileData(storage_path(INSTALLED_SESSION_NAME), Carbon::now()->toDateTimeString());
            });
        } catch (Throwable $exception) {
            info($exception->getMessage());
        }
    }
}
