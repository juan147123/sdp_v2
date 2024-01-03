<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\{
    AplicacionUsuarioRepositoryInterface,
    EloquentRepositoryInterface,
    PersonalChileRepositoryInterface,
    PersonalPeruRepositoryInterface,
    SolicitudRepositoryInterface,
    UsuarioRepositoryInterface,
    UsuarioRolRepositoryInterface,
};
use App\Repositories\{
    AplicacionUsuarioRepository,
    BaseRepository,
    PersonalChileRepository,
    PersonalPeruRepository,
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
        $this->app->bind(PersonalPeruRepositoryInterface::class, PersonalPeruRepository::class);
        $this->app->bind(PersonalChileRepositoryInterface::class, PersonalChileRepository::class);
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
