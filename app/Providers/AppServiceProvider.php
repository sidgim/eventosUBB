<?php

namespace App\Providers;

use App\Evento_users;
use App\Mail\AgregarEncargado;
use App\User;
use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        User::created(function($user) {
            if(!$user->isDirty('google_id')){
                    Mail::to($user)->send(new UserCreated($user));   
            }    
        });

        Evento_users::created(function($idUser) {
            if($idUser->rol_idRol==1){
                $user = User::findOrFail($idUser['users_id']);
                Mail::to($user)->send(new AgregarEncargado($user));   
            }    
        });

    
    }
  
}
