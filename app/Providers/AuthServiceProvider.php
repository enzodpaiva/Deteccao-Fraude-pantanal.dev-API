<?php

namespace App\Providers;

use App\Bussiness\Models\UserAuthApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->configure('auth');
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $listGuard = Config::get('auth.guards');

        foreach ($listGuard as $guard) {

            $this->app['auth']->viaRequest($guard['driver'], function (Request $request) {

                $route = $request->route();
                $guardRequest = (explode(':', ($route[1]['middleware'][0])))[1] ?? (config('auth.defaults'))['guard'];

                $guardConfig = config('auth.guards.' . $guardRequest);

                switch ($guardConfig['authMethod']) {
                    case 'authorization':
                        if ($request->header('authorization')) {
                            return $this->authentication($request->header('authorization'), $guardConfig['user']);
                        }
                        break;
                }
            });
        }
    }

    private function authentication($apiToken, $username)
    {
        return UserAuthApi::where(
            [
                ['apiToken', $apiToken],
                ['username', $username],
            ]
        )->first();
    }
}
