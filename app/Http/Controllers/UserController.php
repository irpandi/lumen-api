<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

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
            return response()->json(['message' => 'Token Unauthorized'], 401);
        }
        return self::respondWithToken($token);
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
        return response($data);
    }
}
