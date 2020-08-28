<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColaboradorTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'colaborador';

    /**
     * Run the migrations.
     * @table colaborador
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('idColaborador');
            $table->string('nombreColaborador', 45)->nullable();
            $table->string('nombreRepresentante', 45)->nullable();
            $table->integer('telefonoColaborador')->nullable();
            $table->string('correoColaborador', 60)->nullable();
            $table->string('sitioWeb', 100)->nullable();
            $table->string('logo', 500)->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->integer('evento_idEvento')->unsigned()->nullable();
            $table->integer('tipoColaborador_idtipoColaborador')->unsigned()->nullable();

            $table->index(["evento_idEvento"], 'fk_colaborador_evento1_idx');

            $table->index(["tipoColaborador_idtipoColaborador"], 'fk_colaborador_tipoColaborador1_idx');


            $table->foreign('evento_idEvento', 'fk_colaborador_evento1_idx')
                ->references('idEvento')->on('evento')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('tipoColaborador_idtipoColaborador', 'fk_colaborador_tipoColaborador1_idx')
                ->references('idtipoColaborador')->on('tipoColaborador')
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
