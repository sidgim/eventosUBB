<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpositorActividadTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'expositor_actividad';

    /**
     * Run the migrations.
     * @table expositor_actividad
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('idexpositor_actividad');
            $table->integer('evento')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->integer('expositor_idExpositor')->unsigned()->nullable();
            $table->integer('actividad_idActividad')->unsigned()->nullable();

            $table->index(["actividad_idActividad"], 'fk_expositor_actividad_actividad1_idx');

            $table->index(["expositor_idExpositor"], 'fk_expositor_actividad_expositor1_idx');


            $table->foreign('expositor_idExpositor', 'fk_expositor_actividad_expositor1_idx')
                ->references('idexpositor')->on('expositor')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('actividad_idActividad', 'fk_expositor_actividad_actividad1_idx')
                ->references('idactividad')->on('actividad')
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
