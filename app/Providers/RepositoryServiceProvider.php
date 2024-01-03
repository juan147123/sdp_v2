<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\{
    AplicacionUsuarioRepositoryInterface,
    EloquentRepositoryInterface,
    SolicitudRepositoryInterface,
    UsuarioRepositoryInterface,
    UsuarioRolRepositoryInterface,
};
use App\Repositories\{
    AplicacionUsuarioRepository,
    BaseRepository,
    SolicitudRepository,
    UsuarioRepository,
    UsuarioRolRepository,
};

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(SolicitudRepositoryInterface::class, SolicitudRepository::class);
        $this->app->bind(AplicacionUsuarioRepositoryInterface::class, AplicacionUsuarioRepository::class);
        $this->app->bind(UsuarioRepositoryInterface::class, UsuarioRepository::class);
        $this->app->bind(UsuarioRolRepositoryInterface::class, UsuarioRolRepository::class);
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
