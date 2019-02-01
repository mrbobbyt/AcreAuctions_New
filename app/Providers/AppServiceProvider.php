<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Configuration String
     */
    const CONFIG_STRING = 'bindings';


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        if ( ! empty(Config::get(self::CONFIG_STRING))) {
            $this->registerBindingGroups(Config::get(self::CONFIG_STRING));
        }
    }


    /**
     * Register Application Binding Groups
     * @author Yitzchok Willroth (@coderabbi) <coderabbi@gmail.com>
     * @copyright Yitzchok Willroth (@coderabbi) <coderabbi@gmail.com>
     * @license MIT <http://opensource.org/licenses/MIT>
     * @package  Coderabbi\BinderClip
     * @param array $groups
     */
    private function registerBindingGroups(array $groups)
    {
        foreach ($groups as $arrays) {
            foreach ($arrays as $bindings) {
                foreach ($bindings as $interface => $implementation) {
                    $this->app->bind($interface, $implementation);
                }
            }
        }
    }
}
