<?php

namespace App\Providers;

use App\Interfaces;
use App\Repositories;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Interfaces\AppVersionInterface::class, Repositories\AppVersionRepository::class);
        $this->app->bind(Interfaces\UserInterface::class, Repositories\UserRepository::class);
        $this->app->bind(Interfaces\RoleInterface::class, Repositories\RoleRepository::class);
        $this->app->bind(Interfaces\UserFirebaseInterface::class, Repositories\UserFirebaseRepository::class);
        $this->app->bind(Interfaces\ProvinceInterface::class, Repositories\ProvinceRepository::class);
        $this->app->bind(Interfaces\CategoryInterface::class, Repositories\CategoryRepository::class);
        $this->app->bind(Interfaces\AccountInterface::class, Repositories\AccountRepository::class);
        $this->app->bind(Interfaces\TransactionInterface::class, Repositories\TransactionRepository::class);
        $this->app->bind(Interfaces\BudgetInterface::class, Repositories\BudgetRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
