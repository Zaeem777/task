<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditUserRequest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use Tymon\JWTAuth\Contracts\Providers\JWT;

class AuthController extends Controller
{

    public function register(RegisterUserRequest $request){
       $field = $request->validated();

        $user = User::create([
            'name' =>$field['name'],
            'email' =>$field['email'],
            'password' =>bcrypt($field['password']),
            'phone' =>$field['phone'],
            'status' =>$field['status'],
            
        ]);
        $token = auth()->login($user);

        return (new UserResource($user))->additional(['message'=> 'User Registered Successfully',
    'token'=> $token]);
        // return response()->json([
        //     'success' =>true,
        //     'message' =>'User registered successfully',
        //     'user'=> $user,
        //     'token' => $token
        // ],201);
        
}

  public function login(LoginUserRequest $request){
       $field = $request->validated();

       $user = User::where('email', $field['email'])->first();

    if(!$user || !Hash::check($field['password'], $user->password)){
        return response([
            'message' => 'Incorrect Credentials',
            'status' => '401'
        ]);
    }

       $token = auth()->login($user);

       return (new UserResource($user))
       ->additional([
        'Message'=> 'Login Successful',
        'Token'=> $token,
       ]);
        // return response()->json([
        //     'success' =>true,
        //     'message' =>'User logged in successfully',
        //     'user'=> $user,
        //     'token' => $token,
        //     'status' => 200
        // ]);
}

public function userInfo()
{
 try{
    $user = JWTAuth::parseToken()->authenticate();
    return response()->json(new UserResource($user), 200);
 }
 catch (\Exception $e) {
    return response()->json(
        ['message' => 'Could not retrieve user information', 
        'status' => 500]);
 }
}

public function editInfo(EditUserRequest $request){
    if($request->validated()){
    try{

        $user = JWTAuth::parseToken()->authenticate();
        $user->name = $request->input('name', $user->name);
        $user->email = $request->input('email', $user->email);
        $user->phone = $request->input('phone', $user->phone);
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        $user->status = $request->input('status', $user->status);
        $user->save();

        return response()->json(
            ['message' => 'User information updated successfully', 
            'status' => 200,
            'user' => new UserResource($user)]);
    }
    catch (\Exception $e) {
        return response()->json(
            ['message' => 'Could not update user information', 
            'status' => 500]);
     }
    }
    else{
        return response()->json(
            ['message' => 'Validation failed', 
            'status' => 422]);
    }
}

public function userDelete(){
    try{
        $user = JWTAuth::parseToken()->authenticate();
        $user->delete();
        return response()->json(
            ['message' => 'User deleted successfully', 
            'status' => 200]);
    }
    catch (\Exception $e) {
        return response()->json(
            ['message' => 'Could not delete user', 
            'status' => 500]);
     }
}

public function logout(Request $request)
{
     auth()->logout();

    return response()->json(
        ['message' => 'Successfully logged out',
        'status' => 200],
                                );
}
}



