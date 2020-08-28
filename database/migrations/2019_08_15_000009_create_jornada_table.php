<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJornadaTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'jornada';

    /**
     * Run the migrations.
     * @table jornada
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('idJornada');
            $table->string('nombreJornada', 45)->nullable();
            $table->date('fechaJornada')->nullable();
            $table->time('horaInicioJornada')->nullable();
            $table->time('horaFinJornada')->nullable();
            $table->softDeletes();
            $table->string('ubicacionJornada', 45)->nullable();
            $table->string('descripcionJornada', 250)->nullable();
            $table->integer('evento_idEvento')->unsigned()->nullable();

            $table->index(["evento_idEvento"], 'fk_jornada_evento1_idx');


            $table->foreign('evento_idEvento', 'fk_jornada_evento1_idx')
                ->references('idEvento')->on('evento')
                ->onDelete('set null')
                ->onUpdate('cascade');
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
