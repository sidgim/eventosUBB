<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersUnidadTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'users_unidad';

    /**
     * Run the migrations.
     * @table users_unidad
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('idusers_unidad');
            $table->integer('users_id')->unsigned()->nullable();
            $table->integer('unidad_idUnidad')->unsigned()->nullable();
            $table->integer('perfilUnidad')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(["users_id"], 'fk_users_unidad_users1_idx');

            $table->index(["unidad_idunidad"], 'fk_users_unidad_unidad1_idx');


            $table->foreign('users_id', 'fk_users_unidad_users1_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('unidad_idunidad', 'fk_users_unidad_unidad1_idx')
                ->references('idUnidad')->on('unidad')
                ->onDelete('no action')
                ->onUpdate('no action');

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
