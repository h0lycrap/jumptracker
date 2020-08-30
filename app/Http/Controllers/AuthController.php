<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            //'password'  => 'required|min:3|confirmed',
        ]);

        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['status' => 'success'], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $email = $request->email;

        if ($token = $this->guard()->attempt($credentials)) {
            return response()->json(Auth::user(), 200)->header('Authorization', $token);
        }

        return response()->json(['error' => $email], 401);
    }

    public function logout()
    {
        $this->guard()->logout();

        return response()->json([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);
    }

    public function user(Request $request)
    {
        $user = User::find(Auth::user()->id);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function userUE4(Request $request)
    {
        $user = User::find($request->id);
        $mapid = $request->mapid;

        return response()->json([
            'status' => 'success',
            'data' => $user->$mapid
        ]);
    }

    public function updateProgress(Request $request)
    {
        $user = User::find($request->id);
        $map = $request->mapid;
        $user->$map = $request->progress;
        $user->save();
        return response()->json($user, 200);
    }

    public function updateProgressUE4(Request $request)
    {
        $user = User::find($request->id);
        $map = $request->mapid;

        $progressObj = json([
        'name' => $request->name,
        'jumps' => $request->jumps,
        'jumpsCompleted' => $request->jumpscompleted,
        'leets' => $request->leets,
        'leetsCompleted' => $request->leetscompleted,
        'secrets' => $request->secrets,
        'secretsCompleted' => $request->secretscompleted,
        'progress'=> $request->progress,
        ]);

        $user->$map = $progressObj;
        $user->save();
        return response()->json(['status' => 'success'], 200);
        //return response()->json($progressObj, 200);
    }

    public function refresh()
    {
        if ($token = $this->guard()->refresh()) {
            return response()
                ->json(['status' => 'successs'], 200)
                ->header('Authorization', $token);
        }

        return response()->json(['error' => 'refresh_token_error'], 401);
    }

    private function guard()
    {
        return Auth::guard();
    }
}