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
use  Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{

 // user login by email and password

    public function userLogin(Request $request){

        $response = Http::asForm()->post('http://127.0.0.1:8001/oauth/token', [
          'grant_type' => 'password',
          'client_id' => '2',
          'client_secret' => 'CQClBuXCpkx8Iw3BeTvuiXwzfW6e0nd55SasNSeq',
          'username' => $request->email,
          'password' => $request->password,
          'scope' => '*',
        ]);


        if ($response->successful()) {
          return response()->json($response->body(), 200);
        } else {
          if ($response->serverError()) {
            return response()->json(['message'=> "Something went wrong on the server!"], 500);
          } else if ($response->clientError()) {
            return response()->json(['message'=> "Login Failed! Please check your input!"], 422);
          } else {
            return response()->json(['message'=> "Bad Request!"], 400);
          }
        }

      }

  // user logout

    public function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return response()->json(['message'=> "Logged out successfully"], 200);
    }

}
