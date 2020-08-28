<?php

namespace App\Helpers;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User;
use GuzzleHttp\Psr7\Request;

class JwtAuth
{
    public $key;

    public function __construct()
    {
        $this->key = 'clave_123';
    }


    public function signup($email, $password, $getToken = null)
    {
      

        $users = User::where('email', $email)->first();
        
        if(!is_object($users)){
            $data = [
                'status' => 'error',
                'correo' => 'correo no existe'  
            ];
            return $data;
        }

         //Buscar si existe el usuario con sus credenciales
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();

    
        //Comprobar si son correctas 
        $signup = false;
        if (is_object($user)) {
            $signup = true;
        }
        //Generar el token con los datos del usuario identificado
        if ($signup) {
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'nombreUsuario' => $user->nombreUsuario,
                'apellidoUsuario' => $user->apellidoUsuario,
                'avatar' => $user->avatar,
                'verified' => $user['verified'],
                'perfil_idPerfil' => $user->perfil_idPerfil,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            );

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
            //Devolver los datos decodificados o el token, en funcion de un parametro

            if (is_null($getToken)) {
                $data = $jwt;
            } else {
                $data = $decoded;
            }
        } else {
            $data = [
                'status' => 'error',
                'password' => 'password incorrecto'
               
            ];
        }

        return $data;
    }

    public function checkToken($jwt, $getIdentity = false)
    {
        $auth = false;

        try {
            $jwt = str_replace('"', '', $jwt);
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }

        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
            $auth = true;
        } else {
            $auth = false;
         }

         if($getIdentity){
            return $decoded;
         }
         return $auth;
    }

    
}
