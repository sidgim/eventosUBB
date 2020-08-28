<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Evento_users;
use App\Jornada;
class sendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registered:Evento_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recordatorio de evento proximo';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $us = \DB::table('jornada')->whereRaw('Date(fechaJornada) = CURDATE()');
        foreach( $us as $uss ) {

        $eventos = Evento_users::where('evento_idEvento', '=', $uss['evento_idEvento'])->where('rol_idRol', '=', 1)->get();

        foreach( $eventos as $evento ) {
            $usuario = User::where('id','=',$evento->users_id);
            Mail::to($usuario->email)->send(new SendMailable($evento));
        }
    }
    }
}
