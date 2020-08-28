<?php
use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'users';

    /**
     * Run the migrations.
     * @table users
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('nombreUsuario', 191)->nullable();
            $table->string('apellidoUsuario', 191)->nullable();
            $table->string('email', 191)->unique();
            $table->string('password', 191)->nullable();
            $table->string('verified')->default(User::USUARIO_NO_VERIFICADO);
            $table->string('avatar', 100)->nullable();
            $table->string('google_id', 191)->nullable();
            $table->softDeletes();
            $table->String('tema_usuario',500)->nullable();
            $table->integer('perfil_idPerfil')->unsigned()->nullable();

            $table->index(["perfil_idPerfil"], 'fk_users_perfil1_idx');

            $table->foreign('perfil_idPerfil', 'fk_users_perfil1_idx')
                ->references('idPerfil')->on('perfil')
                ->onDelete('set null');
                

            $table->rememberToken();
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
