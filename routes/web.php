<?php
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\ApiAuthMiddleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

//Cargndo clse

Route::get('/', function () {
	return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//RUTA DE REDIRECCION AL PROVEEDOR
Route::get('api/login/google', 'SocialiteController@redirectToProvider');
//ruta que recibe la respuesta del proovedor
Route::get('api/login/google/callback', 'SocialiteController@handlerProviderCallback');

//Rutas del usuario
Route::post('/api/register', 'UserController@register');
Route::post('/api/login', 'UserController@login');
Route::put('/api/user/update/{id}', 'UserController@update2');
Route::post('/api/user/upload', 'UserController@upload');
Route::get('/api/user/avatar/{filename}', 'UserController@getImage');
Route::get('/api/user/detail/{id}', 'UserController@detail');
Route::get('/api/getAll/{id}', 'UserController@getAll');
Route::get('/api/getUsuariosEncargado', 'UserController@getUsuariosEncargado');
Route::get('/api/getUsuarioComision/{id}', 'UserController@getUsuarioComision');

Route::post('/api/google', 'UserController@google');
Route::get('/api/getDataUser/{id}', 'UserController@getDataUsuario'); //función para retornar datos del user
Route::post('/api/sendPassword', 'UserController@password'); //envio correo cambio contaseña
Route::post('/api/changePassword', 'UserController@cambiarContraseña');//Cambia la contraseña
Route::get('/api/reporteAdminUBB', 'UserController@getReporteAdminUBB');//Datos reporte para el admin ubb
Route::post('/api/getReporteAdminUnidad','UserController@getReporteAdminUnidad');//Datos reporte para el admin unidad


Route::name('resend')->get('users/{user}/resend', 'User\UserController@resend');
Route::get('/api/verify/{token}', 'UserController@verify');

//Ruta del controlador de evento
Route::resource('/api/evento', 'EventoController');

//Ruta del controlador colaborador
Route::resource('/api/colaborador', 'ColaboradorController');
Route::get('/api/getColaborador/{id}', 'ColaboradorController@showColaboradorById');
Route::post('/api/upload', 'ColaboradorController@upload'); //ruta para subir imagen y almacenarla
Route::get('/api/image/{filename}', 'ColaboradorController@getImage'); //obtener imagen
Route::get('/api/colaborador/listar/{id}', 'ColaboradorController@getEventosByCategory'); //ruta para listar los eventos de ese colaborador

//Ruta Material
Route::resource('/api/material', 'MaterialController');
Route::get('/api/getMaterial/{id}', 'MaterialController@showMaterialById');
Route::post('/api/materiales/upload', 'MaterialController@upload');
Route::get('/api/materiales/ver/{filename}', 'MaterialController@getImage');
Route::get('/api/downloadMaterial/{id}','MaterialController@downloadMaterial'); //Descargar materiales

//Ruta Jornada
Route::resource('/api/jornada', 'JornadaController');
Route::get('/api/getJornada/{id}', 'JornadaController@showJornadaById');

//Ruta Actividad
Route::resource('/api/actividad', 'ActividadController');
Route::get('/api/getActividad/{id}', 'ActividadController@showActividadById');

//Ruta Expositor
Route::resource('/api/expositor', 'ExpositorController');
Route::get('/api/mostrarExpositor/{id}', 'ExpositorController@showExpositor');
Route::get('/api/getExpositor/{id}', 'ExpositorController@showExpositorById');
Route::get('/api/getExpositorActividad/{id}', 'ExpositorController@expositorActividad');


//Ruta EventoPojo
Route::resource('/api/eventoPojo', 'EventoPojoController');
//Route::post('/api/material/upload', 'EventoPojoController@upload');
Route::get('/api/getEventoPojo','EventoPojoController@showEventos');
Route::post('/api/material/upload/{filename}', 'EventoPojoController@getFile');
Route::post('/api/asistencia', 'EventoPojoController@asistencia');

//Ruta evento_users
Route::resource('/api/evento_users', 'Evento_usersController');
Route::post('/api/evento_users/{id}', 'Evento_usersController@store2');
Route::get('/api/evento_users/{id}/{idUser}', 'Evento_usersController@show2');
Route::post('/api/comision/{id}', 'Evento_usersController@comision');
Route::get('/api/getAllComision/{id}', 'Evento_usersController@getAllComision');
Route::get('/api/misEventos/{id}', 'Evento_usersController@getEventosByUser');
Route::get('/api/misEventosAdmin/{id}', 'Evento_usersController@getEventosByAdmin');//Metodo para mostrar los eventos al crear comision
Route::get('/api/misEventosAdmin2/{id}', 'Evento_usersController@getEventosByAdmin2'); //Ruta para obtener todos los eventos que tengo asociado
Route::post('/api/evento_users/delete/comision', 'Evento_usersController@destroyComision');
Route::get('/api/getUsersByEventoId/{id}','Evento_usersController@getAllUsuariosEventoById'); //Ruta nueva de prueba

//Ruta Rol
Route::resource('/api/rol', 'RolController');

//Ruta Ciudad
Route::resource('/api/ciudad', 'CiudadController');

//Ruta userPojo
Route::resource('/api/userUnidad', 'userPojoController');
Route::post('/api/logo', 'userPojoController@upload'); //ruta para subir imagen y almacenarla
Route::get('/api/logoImage/{filename}', 'userPojoController@getImage'); //obtener imagen
Route::get('/api/logoImageDownload/{filename}', 'userPojoController@downloadImage'); //obtener imagen

//Ruta de repositorio
Route::resource('/api/repositorio', 'RepositorioController');
Route::post('/api/repo', 'RepositorioController@upload'); //ruta para subir imagen y almacenarla
Route::get('/api/repositorios/{filename}', 'RepositorioController@getImage'); //obtener imagen
Route::get('/api/downloadRepositorio/{id}','RepositorioController@downloadRepositorio'); //Descargar archivos del repositorio


//Ruta de categoria
Route::resource('/api/categoria' , 'CategoriaController');

//Ruta de tipo de evento
Route::resource('/api/tipoEvento', 'TipoEventoController');

//Ruta de tipo de colaborador
Route::resource('/api/tipoColaborador', 'TipoColaboradorController');

//Ruta de SubUnidad
Route::resource('/api/subUnidad', 'SubUnidadController' );

//Ruta Unidad
Route::resource('/api/unidad', 'UnidadController'); //el metodo destroy funciona para unidad y subUnidad
Route::get('/api/getAllUnidad', 'UnidadController@getAllUnidad');
Route::get('/api/getUnidadById/{id}', 'UnidadController@getUnidadById');
Route::get('/api/getAllUnidad2', 'UnidadController@getAllUnidad2');