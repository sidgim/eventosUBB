<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUtilidadTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'utilidad';

    /**
     * Run the migrations.
     * @table utilidad
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('idUtilidad');
            $table->string('coordenadax', 45)->nullable();
            $table->string('coordenaday', 45)->nullable();
            $table->string('tipoFuente', 45)->nullable();
            $table->string('tamanioFuente', 45)->nullable();
            $table->string('colorFuente', 45)->nullable();
            $table->string('imagen', 500)->nullable();
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
