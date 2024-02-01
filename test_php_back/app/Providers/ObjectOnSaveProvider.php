<?php

namespace App\Providers;
namespace App\Providers;

use App\Services\ObjectOnSaveService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class ObjectOnSaveProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ObjectOnSaveService::class, function($app) {
            return new ObjectOnSaveService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Model::saving(function ($model) {
            $original = $model->getOriginal();
            $changes = $model->getChanges();
            dd($original);
        });
    }
}
