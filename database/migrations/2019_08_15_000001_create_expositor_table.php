<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpositorTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'expositor';

    /**
     * Run the migrations.
     * @table expositor
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('idExpositor');
            $table->string('nombreExpositor', 45)->nullable();
            $table->string('apellidoExpositor', 45)->nullable();
            $table->string('apellido2Expositor', 45)->nullable();
            $table->string('sexo', 45)->nullable();
            $table->string('correoExpositor', 45)->nullable();
            $table->string('empresa', 45)->nullable();
            $table->string('foto', 500)->nullable();
            $table->integer('telefonoExpositor')->nullable();

            $table->softDeletes();
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
