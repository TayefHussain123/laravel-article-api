<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Response;
use App\Enum\EntityStatus;

class RegisterController extends Controller
{

    // user register
    
    public function userRegister(Request $request) {
      
        $validators=Validator::make($request->all(),[
            'name' => 'required|min:3|max:60',
            'email' => 'required|email:filter|min:3|max:60|unique:users',
            'password' => 'required|min:6',
         ]);

         if($validators->fails()){
            return Response::json(['message'=>$validators->getMessageBag()->toArray()],422);
        }
    
          $user= new User;
          $user->name=$request->name;
          $user->email=$request->email;
          $user->password=Hash::make($request['password']);
          $user->user_status=EntityStatus::Active;
          $user->remember_token = Str::random(10);
          $token = $user->createToken('Laravel Password Grant Client')->accessToken;
          $response = ['token' => $token];
          $user->save();
          return response($user, 200);
          
    }
}
