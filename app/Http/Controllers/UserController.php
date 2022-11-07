<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(Request $request)
    {

        $control = User::where('email', $request->email)->first();
        if (!isset($control->id)) {

            $new = User::create($request->all());
            if ($new) {


                return response()->json(['status' => true, 'message' => 'success']);
            } else {

                return response()->json(['status' => false, 'message' => 'failed, error create']);
            }
        } else {

            return response()->json(['status' => false, 'message' => 'failed, already registered']);
        }
    }
    public function login(Request $request)
    {


        if (Auth::attempt($request->all())) {

            $isUser = User::where(['email' => $request->email])->first();
            $tokenResult = $isUser->createToken('Personal Access Token ' . Str::random(10));
            return response()->json(['status' => true, 'message' => 'success', 'api_token' => $tokenResult->plainTextToken], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'failed, error login']);
        }
    }



    public function list($start = 0, $limit = 15, $q = "")
    {


        if ($q != "") {
            $data = User::where('name', 'LIKE', '%' . $q . '%')->orWhere('email', 'LIKE', '%' . $q . '%')->offset($start)->limit($limit)->get();
        } else {
            $data = User::offset($start)->limit($limit)->get();
        }
        $total = User::get()->count();




        return response()->json(['status' => true, 'message' => 'success', 'data' => $data, 'total' => $total]);
    }

    public function me()
    {

        $me = Auth::user();
        return response()->json(['status' => true, 'message' => 'success', 'data' => $me]);
    }

    public function logout()
    {

        Auth::logout();
        return response()->json(['status' => true, 'message' => 'success']);
    }
}
