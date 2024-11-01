<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {
        $factory->macro('success', function ($data) use ($factory) {
            $customFormat = [
                'success' => true,
                'data' => $data
            ];
            return $factory->make($customFormat);
        });

        $factory->macro('error', function ($message, $code = 500) use ($factory) {
            $customFormat = [
                'success' => false,
                'message' => $message
            ];
            return $factory->make($customFormat,$code);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
