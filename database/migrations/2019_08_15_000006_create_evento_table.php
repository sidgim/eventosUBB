<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventoTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'evento';

    /**
     * Run the migrations.
     * @table evento
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('idEvento');
            $table->string('nombreEvento', 100)->nullable();
            $table->string('ubicacion', 45)->nullable();
            $table->string('direccion', 45)->nullable();
            $table->string('detalles', 250)->nullable();
            $table->string('visibilidad')->nullable();
            $table->string('imagen', 500)->nullable();
            $table->integer('capacidad')->nullable();
            $table->string('nombreEventoInterno', 100)->nullable();
            $table->integer('ciudad_idCiudad')->nullable()->unsigned();
            $table->integer('categoria_idCategoria')->nullable()->unsigned();
            $table->integer('tipoEvento_idtipoEvento')->unsigned()->nullable();
            $table->integer('utilidad_idUtilidad')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();


            $table->index(["ciudad_idCiudad"], 'fk_evento_ciudad_idx');

            $table->index(["tipoEvento_idtipoEvento"], 'fk_evento_tipoEvento1_idx');
            
            $table->index(["categoria_idCategoria"], 'fk_evento_categoria1_idx');

            $table->index(["utilidad_idUtilidad"], 'fk_evento_utilidad1_idx');
            
             
            $table->foreign('ciudad_idCiudad', 'fk_evento_ciudad_idx')
                ->references('idCiudad')->on('ciudad')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('categoria_idCategoria', 'fk_evento_categoria1_idx')
                ->references('idCategoria')->on('categoria')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->foreign('tipoEvento_idtipoEvento', 'fk_evento_tipoEvento1_idx')
                ->references('idtipoEvento')->on('tipoEvento')
                ->onDelete('set null')
                ->onUpdate('cascade');
            
            $table->foreign('utilidad_idUtilidad', 'fk_evento_utilidad1_idx')
                ->references('idUtilidad')->on('utilidad')
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
