<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\{
    AplicacionUsuarioRepositoryInterface,
    ConfiguracionRepositoryInterface,
    EloquentRepositoryInterface,
    EstadosRepositoryInterface,
    PersonalChileRepositoryInterface,
    PersonalPeruRepositoryInterface,
    SolicitudColaboradorRepositoryInterface,
    SolicitudRepositoryInterface,
    TerminosRepositoryInterface,
    UsuarioRepositoryInterface,
    UsuarioRolRepositoryInterface,
    ArchivoRepositoryInterface,
};
use App\Repositories\{
    AplicacionUsuarioRepository,
    BaseRepository,
    ConfiguracionRepository,
    EstadosRepository,
    PersonalChileRepository,
    PersonalPeruRepository,
    SolicitudColaboradorRepository,
    SolicitudRepository,
    TerminosRepository,
    UsuarioRepository,
    UsuarioRolRepository,
    ArchivoRepository
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
        $this->app->bind(SolicitudColaboradorRepositoryInterface::class, SolicitudColaboradorRepository::class);
        $this->app->bind(ConfiguracionRepositoryInterface::class, ConfiguracionRepository::class);
        $this->app->bind(TerminosRepositoryInterface::class, TerminosRepository::class);
        $this->app->bind(EstadosRepositoryInterface::class, EstadosRepository::class);
        $this->app->bind(ArchivoRepositoryInterface::class, ArchivoRepository::class);
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
