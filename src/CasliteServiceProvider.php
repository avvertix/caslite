<?php

namespace Avvertix\Caslite;

use Illuminate\Support\ServiceProvider;

class CasliteServiceProvider extends ServiceProvider
{
    

    
    
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Avvertix\Caslite\Contracts\Factory', function ($app) { 
             return new CasliteManager($app); 
         }); 

    }
    
    /** 
      * Get the services provided by the provider. 
      * 
      * @return array 
      */ 
     public function provides() 
     { 
         return ['Avvertix\Caslite\Contracts\Factory']; 
     } 

}