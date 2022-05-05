<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // * Method untuk login get token JWT
    public function login(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $email    = $request->email;
        $password = $request->password;

        $credentials = array(
            'email'    => $email,
            'password' => $password,
        );

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Login Failed'], 401);
        }

        return $this->responseWithToken($token);
    }

    // * Method untuk register token JWT
    public function register(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required|string',
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $name     = $request->name;
        $email    = $request->email;
        $password = $request->password;

        $email = User::where('email', $email)
            ->count();

        if ($email > 0) {
            $response = array(
                'message'  => 'User Exist',
                'response' => 500,
            );
        } else {
            $created = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make($password),
            ]);

            if ($created) {
                $token = Auth::fromUser($created);

                $response = array(
                    'message'  => 'Data Created',
                    'response' => 201,
                    'token'    => $token,
                );
            } else {
                $response = array(
                    'message'  => 'Data Failed Created',
                    'response' => 409,
                );
            }
        }

        return response($response, $response['response']);
    }

    // * Method untuk get All data User
    public function getUser()
    {
        $data = User::all();

        $arrData = array(
            'message' => 'success',
            'status'  => 200,
            'data'    => $data,
        );

        return response()->json($arrData);
    }

    // * Function untuk get api login user
    public function loginUser()
    {
        $user = Auth::user();

        $data = array(
            'message' => 'success',
            'status'  => 200,
            'data'    => $user,
        );

        return response()->json($data);
    }

    // * Function untuk logout user
    public function logoutUser()
    {
        Auth::logout();

        $data = array(
            'message' => 'success',
            'status'  => 200,
        );

        return response()->json($data);
    }

    // * Function response with token
    private function responseWithToken($token)
    {
        return response()->json([
            'token'      => $token,
            'expires_in' => Auth::factory()->getTTL() * 60,
        ], 200);
    }

    // * Function refresh token
    public function refreshToken()
    {
        return $this->responseWithToken(Auth::refresh());
    }
}
