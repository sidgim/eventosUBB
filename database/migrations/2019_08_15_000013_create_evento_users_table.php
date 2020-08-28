<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateEventoUsersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'evento_users';

    /**
     * Run the migrations.
     * @table evento_users
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('idevento_users');
            $table->integer('contadorEvento')->nullable();
            $table->string('asistencia')->nullable();
            $table->softDeletes();
            $table->integer('evento_idEvento')->unsigned()->nullable();
            $table->integer('rol_idRol')->unsigned()->nullable();
            $table->integer('users_id')->unsigned()->nullable();

            $table->index(["evento_idEvento"], 'fk_evento_users_evento1_idx');

            $table->index(["rol_idRol"], 'fk_evento_users_rol1_idx');

            $table->index(["users_id"], 'fk_evento_users_users1_idx');


            $table->foreign('evento_idEvento', 'fk_evento_users_evento1_idx')
                ->references('idEvento')->on('evento')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('rol_idRol', 'fk_evento_users_rol1_idx')
                ->references('idRol')->on('rol')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('users_id', 'fk_evento_users_users1_idx')
                ->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');

                $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->tableName);
     }
}
