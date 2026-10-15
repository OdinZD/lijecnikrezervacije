<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerSqliteFunctions();
    }

    /**
     * The search layer leans on Postgres functions (f_unaccent + the haversine
     * trig). On the cPanel SQLite deploy those don't exist, so we register them
     * as SQLite UDFs — letting the exact same SQL run on both drivers.
     */
    private function registerSqliteFunctions(): void
    {
        try {
            $connection = DB::connection();

            if ($connection->getDriverName() !== 'sqlite') {
                return;
            }

            $pdo = $connection->getPdo();
        } catch (Throwable) {
            return; // no usable DB (e.g. during some artisan commands) -> skip
        }

        $pdo->sqliteCreateFunction('f_unaccent', fn (?string $value) => $value === null ? null : Str::ascii($value), 1);
        $pdo->sqliteCreateFunction('radians', fn ($deg) => deg2rad((float) $deg), 1);
        $pdo->sqliteCreateFunction('acos', fn ($x) => acos((float) $x), 1);
        $pdo->sqliteCreateFunction('cos', fn ($x) => cos((float) $x), 1);
        $pdo->sqliteCreateFunction('sin', fn ($x) => sin((float) $x), 1);
        $pdo->sqliteCreateFunction('least', fn ($a, $b) => min((float) $a, (float) $b), 2);
    }
}
