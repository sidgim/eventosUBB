<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActividadTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'actividad';

    /**
     * Run the migrations.
     * @table actividad
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('idActividad');
            $table->string('nombreActividad', 45)->nullable();
            $table->time('horaInicioActividad')->nullable();
            $table->time('horaFinActividad')->nullable();
            $table->string('ubicacionActividad', 45)->nullable();
            $table->string('descripcionActividad', 250)->nullable();
            $table->integer('actividadParalela')->nullable();
            $table->integer('cupos')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->integer('jornada_idJornada')->unsigned()->nullable();

            $table->index(["jornada_idJornada"], 'fk_actividad_jornada1_idx');

            $table->foreign('jornada_idJornada', 'fk_actividad_jornada1_idx')
                ->references('idJornada')->on('jornada')
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
