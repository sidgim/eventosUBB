<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'material';

    /**
     * Run the migrations.
     * @table material
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('idMaterial');
            $table->string('nombreMaterial', 45)->nullable();
            $table->string('archivo', 400)->nullable();
            $table->softDeletes();
            $table->integer('evento_idEvento')->unsigned()->nullable();

            $table->index(["evento_idEvento"], 'fk_material_evento1_idx');


            $table->foreign('evento_idEvento', 'fk_material_evento1_idx')
                ->references('idEvento')->on('evento')
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
