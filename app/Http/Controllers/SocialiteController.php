<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use Illuminate\Support\Facades\Auth;
use App\User;

class SocialiteController extends Controller
{
       /**
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function handlerProviderCallback()
    {
        $userGoogle = Socialite::driver('google')->user();
       // dd($userGoogle);
       $user = User::where('email' , $userGoogle->getEmail())->first();
        if($user){
            auth()->login($user, true);
        }else{
            $newUser = new User();
            $newUser->nombreUsuario = $userGoogle->getName();
            $newUser->email = $userGoogle->getEmail();
            $newUser->google_id = $userGoogle->getId();
            $newUser->save();
            auth()->login($newUser, true);
            
        }
        $userGoogle = Auth::user();
        $token =  $userGoogle->createToken('MyApp')-> accessToken;
        return redirect()->to('http://localhost:4200/login?code='.$userGoogle->google_id.'&token='.$token);
           
    }
}
