<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepositorioTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'repositorio';

    /**
     * Run the migrations.
     * @table repositorio
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('idRepositorio');
            $table->string('archivo')->nullable();
            $table->integer('evento_idevento')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(["evento_idevento"], 'fk_repositorio_evento1_idx');


            $table->foreign('evento_idevento', 'fk_repositorio_evento1_idx')
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
