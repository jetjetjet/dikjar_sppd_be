<?php

namespace App\Providers;

use App\Repositories\Contracts\AnggaranRepositoryInterface;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Contracts\BiayaRepositoryInterface;
use App\Repositories\Contracts\FileRepositoryInterface;
use App\Repositories\Contracts\InapRepositoryInterface;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use App\Repositories\Contracts\PejabatTtdRepositoryInterface;
use App\Repositories\Contracts\PengeluaranRepositoryInterface;
use App\Repositories\Contracts\ReportSPPDRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\SPTDetailRepositoryInterface;
use App\Repositories\Contracts\SPTGuestRepositoryInterface;
use App\Repositories\Contracts\SPTLogRepositoryInterface;
use App\Repositories\Contracts\SPTRepositoryInterface;
use App\Repositories\Contracts\TransportRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\AnggaranRepository;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Eloquent\BiayaRepository;
use App\Repositories\Eloquent\FileRepository;
use App\Repositories\Eloquent\InapRepository;
use App\Repositories\Eloquent\PegawaiRepository;
use App\Repositories\Eloquent\PejabatTtdRepository;
use App\Repositories\Eloquent\PengeluaranRepository;
use App\Repositories\Eloquent\ReportSPPDRepository;
use App\Repositories\Eloquent\RoleRepository;
use App\Repositories\Eloquent\SPTDetailRepository;
use App\Repositories\Eloquent\SPTGuestRepository;
use App\Repositories\Eloquent\SPTLogRepository;
use App\Repositories\Eloquent\SPTRepository;
use App\Repositories\Eloquent\TransportRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public array $bindings = [
        SPTRepositoryInterface::class => SPTRepository::class,
        SPTDetailRepositoryInterface::class => SPTDetailRepository::class,
        SPTLogRepositoryInterface::class => SPTLogRepository::class,
        SPTGuestRepositoryInterface::class => SPTGuestRepository::class,
        BiayaRepositoryInterface::class => BiayaRepository::class,
        TransportRepositoryInterface::class => TransportRepository::class,
        InapRepositoryInterface::class => InapRepository::class,
        PengeluaranRepositoryInterface::class => PengeluaranRepository::class,
        ReportSPPDRepositoryInterface::class => ReportSPPDRepository::class,
        FileRepositoryInterface::class => FileRepository::class,
        PegawaiRepositoryInterface::class => PegawaiRepository::class,
        AnggaranRepositoryInterface::class => AnggaranRepository::class,
        RoleRepositoryInterface::class => RoleRepository::class,
        UserRepositoryInterface::class => UserRepository::class,
        PejabatTtdRepositoryInterface::class => PejabatTtdRepository::class,
        AuthRepositoryInterface::class => AuthRepository::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
